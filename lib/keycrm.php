<?php

use SimpleBasket\Order;
use SimpleBasket\Product;

/**
 * Доступ до KeyCRM: ключ і URL з адмінки (Simplebasket Settings → KeyCRM, опція simplebasket-keycrm).
 * Якщо в адмінці порожньо — запасний варіант: константа KEYCRM_TOKEN у wp-config.php.
 *
 * @return array{token:string,url:string}
 */
function cb_keycrm_credentials(): array {
  $opt   = (array) get_option( 'simplebasket-keycrm', [] );
  $token = trim( (string) ( $opt['api_key'] ?? '' ) );
  if ( $token === '' && defined( 'KEYCRM_TOKEN' ) ) {
    $token = (string) KEYCRM_TOKEN;
  }
  $url = trim( (string) ( $opt['api_url'] ?? '' ) );
  if ( $url === '' ) {
    $url = 'https://openapi.keycrm.app/v1';
  }
  // Приймаємо і «https://openapi.keycrm.app», і «…/v1/», і випадково вставлене «…/v1/order».
  $url = preg_replace( '~/order/?$~', '', rtrim( $url, '/' ) );
  if ( ! preg_match( '~/v\d+$~', $url ) ) {
    $url .= '/v1';
  }

  return [ 'token' => $token, 'url' => $url ];
}

/**
 * source_uuid замовлення в KeyCRM. KeyCRM не приймає два замовлення з однаковим source_uuid,
 * а локальна/preview копії сайту мають ті самі ID, що й бойовий сайт. Тому:
 * бойовий сайт — просто ID; інші копії — з префіксом хоста (preview-5013, localhost-5011);
 * після повторного надсилання — збережений унікальний _keycrm_uuid.
 */
function cb_keycrm_source_uuid( $order_id ): string {
  $saved = (string) get_post_meta( $order_id, '_keycrm_uuid', true );
  if ( $saved !== '' ) {
    return $saved;
  }
  $host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
  $prod = (string) apply_filters( 'cb_keycrm_production_host', 'candybuffet.kiev.ua' );
  if ( $host === $prod || $host === 'www.' . $prod ) {
    return (string) $order_id;
  }

  return strtok( $host, '.' ) . '-' . $order_id;
}

add_action( 'simplebasket_order/new_order', 'cb_keycrm_send_order' );

/**
 * Надіслати замовлення в KeyCRM. Викликається при створенні замовлення
 * і кнопкою «Надіслати в KeyCRM» у картці замовлення.
 *
 * @return bool чи створено замовлення в CRM
 */
function cb_keycrm_send_order( Order $order, bool $retry = true ): bool {
  $crm = cb_keycrm_credentials();
  if ( $crm['token'] === '' ) {
    update_post_meta( $order->ID, 'keycrm_error', 'Не задано API key KeyCRM (Simplebasket Settings → KeyCRM).' );
    return false;
  }

  // Cart
  $products = [];
  $cart     = $order->get_cart();

  /** @var Product $item */
  foreach ( $cart->get_items_as_objects() as $item ) {
    $products[] = [
      // 'sku'      => $item->ID,
      'price'    => $item->get_price( 'lowest' ),
      'quantity' => $item->qty,
      // 'unit_type' => 'шт',
      'name'     => get_the_title( $item ),
      'picture'  => str_replace( '.local', '.kiev.ua', get_the_post_thumbnail_url( $item ) ),
    ];
  }

  $shipping_info  = $order->get_meta( 'simplebasket_shipping_info' );
  $shipping_total = '';

  $customer_name = $order->get_meta( 'simplebasket_billing_info->name' );

  $phone = '+' . preg_replace( '/\D+/', '', $order->get_meta( 'simplebasket_billing_info->phone' ) );
  $email = $order->get_meta( 'simplebasket_billing_info->email' );

  $order_notes = $order->get_meta( 'simplebasket_order_notes', '' );

  // Payment Info
  $payment_method_name  = $order->get_meta( 'simplebasket_payment_info->method', '' );
  $payment_method_id    = $payment_method_name === 'liqpay' ? 6 : 3; // liqpay || cash
  $order_total          = $order->get_meta( 'simplebasket_payment_info->total' );
  $delivery_method      = $order->get_meta( 'simplebasket_delivery_method' );
  $delivery_method_text = $order->get_meta( 'simplebasket_delivery_method_text' );
  // $delivery_method_id = $cart->get_delivery_methods()[$delivery_method];

  $delivery_info = $order->get_meta( 'simplebasket_delivery_info', '' );
  $order_uuid    = $order->get_id();

  $date                  = date( 'Y-m-d H:i:s' );
  $shipping_address_city = $delivery_method === 'taxi' ? 'Київ' : '';
  $bf_np                 = get_post_meta( $order->ID, 'cbbf_np', true );
  if ( $delivery_method === 'nv' && is_array( $bf_np ) && ! empty( $bf_np['city'] ) ) {
    $shipping_address_city = $bf_np['city'];
    $delivery_info         = $bf_np['warehouse'];
  }
  $delivery_method_id    = $cart->get_delivery_methods()[ $delivery_method ]['crm_method_id'];

  // Black Friday (плагін cb-black-friday): пакет, знижка, передоплата доставки, подарунок
  $bf_breakdown = get_post_meta( $order->ID, '_cbbf_breakdown', true );
  // знижка за акцією «другий набір» + знижка за промокодом
  $bf_discount  = is_array( $bf_breakdown ) ? (float) $bf_breakdown['discount'] + (float) ( $bf_breakdown['coupon']['discount'] ?? 0 ) : 0.0;
  $bf_shipping  = is_array( $bf_breakdown ) ? (float) $bf_breakdown['shipping'] : 0.0;
  if ( is_array( $bf_breakdown ) && ! empty( $bf_breakdown['pack'] ) ) {
    $products[] = [
      'price'    => (float) $bf_breakdown['pack']['price'],
      'quantity' => 1,
      'name'     => $bf_breakdown['pack']['label'],
    ];
  }
  $bf_gift_product = (int) get_post_meta( $order->ID, 'cbbf_gift_product', true );
  if ( $bf_gift_product ) {
    $products[] = [
      'price'    => 0,
      'quantity' => 1,
      'name'     => get_the_title( $bf_gift_product ) . ' (подарунок)',
      'picture'  => str_replace( '.local', '.kiev.ua', (string) get_the_post_thumbnail_url( $bf_gift_product ) ),
    ];
  }
  $bf_is_gift  = get_post_meta( $order->ID, 'cbbf_order_for', true ) === 'gift';
  $bf_comment  = [];
  if ( class_exists( '\CandyBuffet\BlackFriday\Checkout' ) ) {
    foreach ( \CandyBuffet\BlackFriday\Checkout::order_summary( (int) $order->ID ) as $k => $v ) {
      $bf_comment[] = $k . ': ' . $v;
    }
  }

  $jayParsedAry = [
    'source_id'     => 1,
    'source_name'   => 'Candy Buffet',
    'source_uuid'   => cb_keycrm_source_uuid( $order->ID ),
    'buyer_comment' => $order_notes,
    // 'shipping_price' => $shipping_total,
    'ordered_at'    => $date,
    'buyer'         => [
      'full_name' => $customer_name,
      'email'     => $email,
      'phone'     => $phone,
    ],
    'shipping'      => [
      'shipping_address_city'    => $shipping_address_city,
      'shipping_receive_point'   => $delivery_info,
      'shipping_address_country' => 'Україна',
      'delivery_service_id'      => $delivery_method_id,
      'recipient_full_name'      => $bf_is_gift ? (string) get_post_meta( $order->ID, 'gift_to_name', true ) : $customer_name,
      'recipient_phone'          => $bf_is_gift ? (string) get_post_meta( $order->ID, 'gift_to_phone', true ) : $phone,
    ],
    'products'      => $products,
    'discount_amount' => $bf_discount,
    'shipping_price'  => $bf_shipping,
    'manager_comment' => implode( "\n", $bf_comment ),
    'is_gift'         => $bf_is_gift,
    'gift_message'    => $bf_is_gift ? (string) get_post_meta( $order->ID, 'cbbf_card_text', true ) : '',
    'payments'      => [
      [
        'amount'            => $order_total,
        'payment_method_id' => $payment_method_id,
        'status'            => 'not_paid',
      ],
    ],
  ];

  $headers = array(
    'Content-Type'  => 'application/json',
    'Accept'        => 'application/json',
    'Authorization' => 'Bearer ' . $crm['token'],
  );

  add_action( 'http_api_debug', 'debug_wp_remote_post_and_get_request', 10, 5 );

  $response = wp_remote_post( $crm['url'] . '/order', [
    'timeout' => 20,
    'headers' => $headers,
    'body'    => json_encode( $jayParsedAry ),
  ] );

  remove_action( 'http_api_debug', 'debug_wp_remote_post_and_get_request', 10 );

  if ( is_wp_error( $response ) ) {
    update_post_meta( $order->ID, 'keycrm_error', 'HTTP: ' . $response->get_error_message() );
    error_log( '[keycrm] order ' . $order->ID . ' not sent: ' . $response->get_error_message() );

    return false;
  }

  $code          = (int) wp_remote_retrieve_response_code( $response );
  $response_body = wp_remote_retrieve_body( $response );

  if ( $code >= 200 && $code < 300 ) {
    // wp_slash: update_post_meta знімає «\», без цього JSON із лапками/юнікодом ламається.
    update_post_meta( $order->ID, 'keycrm_response', wp_slash( $response_body ) );
    delete_post_meta( $order->ID, 'keycrm_error' );

    return true;
  }

  // Такий source_uuid уже є в CRM (напр. тестове замовлення з копії сайту з тим самим ID) —
  // повторюємо один раз з унікальним uuid.
  if ( $retry && $code === 422 && stripos( (string) $response_body, 'source_uuid' ) !== false ) {
    update_post_meta( $order->ID, '_keycrm_uuid', $order->ID . '-' . wp_date( 'ymdHis' ) );

    return cb_keycrm_send_order( $order, false );
  }

  update_post_meta( $order->ID, 'keycrm_error', wp_slash( 'HTTP ' . $code . ': ' . mb_substr( (string) $response_body, 0, 1000 ) ) );
  error_log( '[keycrm] order ' . $order->ID . ' rejected (' . $code . '): ' . $response_body );

  return false;
}

/** Кнопка «Надіслати в KeyCRM» у картці замовлення (блок Black Friday). */
add_action( 'admin_post_cb_keycrm_resend', function () {
  $id = (int) ( $_GET['order'] ?? 0 );
  if ( ! $id || ! current_user_can( 'edit_post', $id ) || ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'cb_keycrm_resend_' . $id ) ) {
    wp_die( 'Немає доступу.' );
  }
  $raw = (string) get_post_meta( $id, 'keycrm_response', true );
  if ( preg_match( '~^\{"id":\d+~', $raw ) ) {
    wp_safe_redirect( add_query_arg( 'cb_keycrm', 'already', get_edit_post_link( $id, 'url' ) ) );
    exit;
  }
  $ok = cb_keycrm_send_order( new Order( $id ) );
  wp_safe_redirect( add_query_arg( 'cb_keycrm', $ok ? 'sent' : 'failed', get_edit_post_link( $id, 'url' ) ) );
  exit;
} );

function debug_wp_remote_post_and_get_request( $response, $context, $class, $r, $url ) {
  error_log( '------------------------------' );
  error_log( $url );
  error_log( json_encode( $response ) );
  error_log( $class );
  error_log( $context );
  error_log( json_encode( $r ) );
}


/**
 * ТЗ, розділ 7: після успішної оплати LiqPay статус «оплачено» автоматично передається в CRM.
 * Callback LiqPay (CB_Liqpay_Gateway::process_confirm_order) → do_action( 'cb_liqpay_status' ).
 * KeyCRM: PUT /order/{orderId}/payment/{paymentId} { status: paid }.
 * id замовлення в CRM — з відповіді на створення (meta keycrm_response), id оплати — звідти ж
 * або через GET /order/{id}?include=payments.
 */
add_action( 'cb_liqpay_status', function ( $order, $status, $data = [] ) {
  $crm = cb_keycrm_credentials();
  if ( $crm['token'] === '' || ! is_object( $order ) || empty( $order->ID ) ) {
    return;
  }
  // hold_wait — кошти заблоковані на картці клієнта (схема «hold»): для клієнта замовлення оплачене
  if ( ! in_array( $status, [ 'hold_wait', 'success', 'sandbox' ], true ) ) {
    return;
  }
  if ( get_post_meta( $order->ID, '_cbbf_crm_paid', true ) ) {
    return;
  }

  $raw     = (string) get_post_meta( $order->ID, 'keycrm_response', true );
  $created = json_decode( $raw, true );
  $crm_id  = (int) ( $created['id'] ?? 0 );
  if ( ! $crm_id && preg_match( '~^\{"id":(\d+)~', $raw, $m ) ) {
    // Старі замовлення: JSON збережено без «\» і він не парситься, але id на початку.
    $crm_id  = (int) $m[1];
    $created = [];
  }
  if ( ! $crm_id ) {
    error_log( '[keycrm] paid sync: no CRM order id for WP order ' . $order->ID );
    return;
  }

  $headers = [
    'Content-Type'  => 'application/json',
    'Accept'        => 'application/json',
    'Authorization' => 'Bearer ' . $crm['token'],
  ];

  $payment_id = (int) ( $created['payments'][0]['id'] ?? 0 );
  if ( ! $payment_id ) {
    $res  = wp_remote_get( $crm['url'] . '/order/' . $crm_id . '?include=payments', [ 'timeout' => 15, 'headers' => $headers ] );
    $body = is_wp_error( $res ) ? [] : json_decode( (string) wp_remote_retrieve_body( $res ), true );
    $payments   = $body['payments'] ?? ( $body['data']['payments'] ?? [] );
    $payment_id = (int) ( $payments[0]['id'] ?? 0 );
  }
  if ( ! $payment_id ) {
    error_log( '[keycrm] paid sync: no payment in CRM order ' . $crm_id );
    return;
  }

  $res  = wp_remote_request( $crm['url'] . '/order/' . $crm_id . '/payment/' . $payment_id, [
    'method'  => 'PUT',
    'timeout' => 15,
    'headers' => $headers,
    'body'    => wp_json_encode( [
      'status'      => 'paid',
      'description' => 'LiqPay: ' . $status . ( ! empty( $data['payment_id'] ) ? ', payment_id ' . $data['payment_id'] : '' ),
    ] ),
  ] );
  $code = is_wp_error( $res ) ? 0 : (int) wp_remote_retrieve_response_code( $res );

  if ( $code >= 200 && $code < 300 ) {
    update_post_meta( $order->ID, '_cbbf_crm_paid', current_time( 'mysql' ) );
  } else {
    error_log( '[keycrm] paid sync failed (' . $code . '): ' . ( is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_body( $res ) ) );
  }
}, 10, 3 );
