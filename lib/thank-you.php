<?php
/**
 * Сторінка «Дякуємо за замовлення».
 *
 * Етап 1: сторінка + верстка, показ замість попапа #modal-payment-transfer.
 * Етап 2: дані замовлення (номер, статус оплати, доставка) за посиланням ?order_id=…&key=…
 * Етап 3: GA4 purchase зі сторінки (дані замовлення, дедуплікація), без старого тригера на попапі.
 * Етап 4: LiqPay — result_url на сторінку подяки, стани оплати (оплачено / не завершено / обробляється).
 */

const CB_TY_TEMPLATE = 'templates/template-thank-you.php';

/**
 * URL сторінки з шаблоном «Thank You». Порожній рядок, якщо сторінку не створено.
 */
function cb_ty_page_url(): string {
  static $url = null;

  if ( $url !== null ) {
    return $url;
  }

  $ids = get_posts( [
    'post_type'   => 'page',
    'post_status' => 'publish',
    'numberposts' => 1,
    'fields'      => 'ids',
    'meta_key'    => '_wp_page_template',
    'meta_value'  => CB_TY_TEMPLATE,
  ] );

  $url = $ids ? (string) get_permalink( $ids[0] ) : '';

  return $url;
}

function cb_ty_is_page(): bool {
  return is_page_template( CB_TY_TEMPLATE );
}

/**
 * Телефон з налаштувань сайту: [текст, href для tel:].
 */
function cb_ty_site_phone(): array {
  $text   = trim( (string) get_field( 'телефон', 'option' ) );
  $digits = preg_replace( '/\D+/', '', $text );

  if ( strlen( $digits ) === 10 && $digits[0] === '0' ) {
    $digits = '38' . $digits;
  }

  return [ $text, $digits ? 'tel:+' . $digits : '' ];
}

/**
 * При створенні замовлення:
 * - ключ доступу до сторінки подяки (32 символи, meta _cb_ty_key);
 * - поля доставки окремо (плагін зберігає їх одним рядком через кому);
 * - кука cb_ty = "ID:KEY" на 1 год — з неї JS кошика будує URL сторінки.
 *
 * Пріоритет 1: раніше за решту обробників new_order у темі.
 */
add_action( 'simplebasket_order/new_order', function ( $order ) {
  $order_id = ( is_object( $order ) && isset( $order->ID ) ) ? (int) $order->ID : (int) $order;
  if ( $order_id <= 0 ) {
    return;
  }

  $key = wp_generate_password( 32, false );
  update_post_meta( $order_id, '_cb_ty_key', $key );

  $method = (string) get_post_meta( $order_id, 'simplebasket_delivery_method', true );
  $raw    = ( $method !== '' && isset( $_POST['delivery'][ $method ] ) ) ? (array) wp_unslash( $_POST['delivery'][ $method ] ) : [];
  $fields = array_map( static function ( $v ) {
    return is_scalar( $v ) ? sanitize_text_field( (string) $v ) : '';
  }, array_values( $raw ) );
  update_post_meta( $order_id, '_cb_ty_delivery_fields', $fields );

  if ( ! headers_sent() ) {
    setcookie( 'cb_ty', $order_id . ':' . $key, [
      'expires'  => time() + HOUR_IN_SECONDS,
      'path'     => ( defined( 'COOKIEPATH' ) && COOKIEPATH ) ? COOKIEPATH : '/',
      'domain'   => defined( 'COOKIE_DOMAIN' ) ? (string) COOKIE_DOMAIN : '',
      'secure'   => is_ssl(),
      'httponly' => false, // читається JS кошика
      'samesite' => 'Lax',
    ] );
  }
}, 1 );

/**
 * ВАЖЛИВО: дані замовлення читаються тільки через get_post_meta() по конкретних ключах.
 * Не використовувати \SimpleBasket\Order::get_meta() / get_cart(): вони розпаковують meta
 * simplebasket_cart_info (серіалізований об'єкт Cart), а Cart::__destruct() записує свої товари
 * в $_SESSION — кошик покупця знову наповнився б товарами оформленого замовлення.
 */

/**
 * ID замовлення, якщо пара order_id + key валідна, інакше 0.
 */
function cb_ty_find_order( int $order_id, string $key ): int {
  if ( $order_id <= 0 || strlen( $key ) < 16 ) {
    return 0;
  }

  if ( get_post_type( $order_id ) !== 'simplebasket_order' || get_post_status( $order_id ) === 'trash' ) {
    return 0;
  }

  $stored = (string) get_post_meta( $order_id, '_cb_ty_key', true );

  return ( $stored !== '' && hash_equals( $stored, $key ) ) ? $order_id : 0;
}

/**
 * Замовлення з URL (?order_id=…&key=…): ID або 0.
 */
function cb_ty_current_order(): int {
  static $order_id = null;

  if ( $order_id === null ) {
    $order_id = cb_ty_find_order(
      isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0,
      isset( $_GET['key'] ) ? (string) wp_unslash( $_GET['key'] ) : ''
    );
  }

  return $order_id;
}

/**
 * Посилання на сторінку подяки для замовлення (з ключем). Порожній рядок, якщо немає сторінки або ключа.
 */
function cb_ty_order_url( int $order_id ): string {
  $base = cb_ty_page_url();
  $key  = (string) get_post_meta( $order_id, '_cb_ty_key', true );

  if ( $base === '' || $key === '' ) {
    return '';
  }

  return add_query_arg( [ 'order_id' => $order_id, 'key' => $key ], $base );
}

/**
 * Значення meta замовлення з підтримкою шляху 'meta_key->sub_key'.
 */
function cb_ty_meta( int $order_id, string $path, $default = '' ) {
  $keys  = explode( '->', $path );
  $value = get_post_meta( $order_id, array_shift( $keys ), true );

  foreach ( $keys as $k ) {
    if ( ! is_array( $value ) || ! array_key_exists( $k, $value ) ) {
      return $default;
    }
    $value = $value[ $k ];
  }

  return ( $value === '' || $value === null || $value === false ) ? $default : $value;
}

function cb_ty_is_preview(): bool {
  return isset( $_GET['ty_preview'] ) && current_user_can( 'manage_options' );
}

/**
 * Маска телефону: +380 •• ••• •• 67
 */
function cb_ty_mask_phone( string $phone ): string {
  $digits = preg_replace( '/\D+/', '', $phone );

  if ( strlen( $digits ) < 4 ) {
    return '';
  }

  $last = substr( $digits, -2 );

  if ( strlen( $digits ) >= 12 ) {
    return '+' . substr( $digits, 0, 3 ) . ' •• ••• •• ' . $last;
  }

  return '••• •• ' . $last;
}

function cb_ty_payment_method( int $order_id ): string {
  return (string) cb_ty_meta( $order_id, 'simplebasket_payment_info->method' );
}

/**
 * Стан онлайн-оплати LiqPay за тим, що записав callback плагіна (Gateway/Liqpay.php):
 * - hold_wait → статус замовлення order_hold, payment_info->status = __('Hold');
 * - інші статуси → payment_info->status = статус LiqPay як є (success, failure, error, reversed, …).
 *
 * @return string paid | failed | pending
 */
function cb_ty_liqpay_state( int $order_id ): string {
  $post_status = (string) get_post_status( $order_id );
  $pay_status  = (string) cb_ty_meta( $order_id, 'simplebasket_payment_info->status' );

  if ( in_array( $pay_status, [ 'failure', 'error', 'reversed' ], true ) || $post_status === 'order_cancel' ) {
    return 'failed';
  }

  $paid_post = [ 'order_hold', 'order_paid', 'order_shipped', 'order_closed' ];
  $paid_pay  = [ 'success', 'hold_wait', 'sandbox', 'Hold', __( 'Hold', 'simple_basket' ) ];

  if ( in_array( $post_status, $paid_post, true ) || in_array( $pay_status, $paid_pay, true ) ) {
    return 'paid';
  }

  return 'pending';
}

function cb_ty_is_paid_online( int $order_id ): bool {
  return cb_ty_liqpay_state( $order_id ) === 'paid';
}

/**
 * Статус оплати для плашки в hero.
 */
function cb_ty_payment_status( int $order_id ): array {
  $method = cb_ty_payment_method( $order_id );

  if ( $method === 'liqpay' ) {
    switch ( cb_ty_liqpay_state( $order_id ) ) {
      case 'paid':
        return [ 'label' => 'Оплачено онлайн', 'tone' => 'success' ];
      case 'failed':
        return [ 'label' => 'Оплату не завершено', 'tone' => 'error' ];
      default:
        return [ 'label' => 'Оплата обробляється', 'tone' => 'pending' ];
    }
  }

  if ( $method === 'transfer' ) {
    return [ 'label' => 'Оплата після дзвінка менеджера', 'tone' => 'pending' ];
  }

  return [];
}

/**
 * Доставка: повний рядок (desktop) і короткий (mobile, згорнутий акордеон).
 */
function cb_ty_delivery_text( int $order_id ): array {
  $method = (string) cb_ty_meta( $order_id, 'simplebasket_delivery_method' );
  $title  = (string) cb_ty_meta( $order_id, 'simplebasket_delivery_method_text' );
  $fields = get_post_meta( $order_id, '_cb_ty_delivery_fields', true );
  $fields = is_array( $fields ) ? array_map( 'trim', $fields ) : [];
  $info   = trim( (string) cb_ty_meta( $order_id, 'simplebasket_delivery_info' ) );

  if ( $title === '' && class_exists( '\SimpleBasket\Cart' ) ) {
    $title = \SimpleBasket\Cart::get_delivery_name_by_slug( $method );
  }

  $full  = [ $title ];
  $short = [ $title ];

  if ( $method === 'nv' && $fields ) {
    // Поля кошика (template-cart.php): [0] місто, [1] № відділення, [2] адреса (необов'язково)
    $city    = $fields[0] ?? '';
    $dept    = $fields[1] ?? '';
    $address = $fields[2] ?? '';

    if ( $dept !== '' ) {
      $full[]  = 'відділення № ' . $dept;
      $short[] = 'відділення № ' . $dept;
    }
    if ( $address !== '' ) {
      $full[] = $address;
      if ( $dept === '' ) {
        $short[] = 'адресна доставка';
      }
    }
    if ( $city !== '' ) {
      $full[] = $city;
    }
  } elseif ( $method === 'taxi' && $fields ) {
    // [0] адреса доставки
    if ( ( $fields[0] ?? '' ) !== '' ) {
      $full[] = $fields[0];
    }
  } elseif ( $method === 'samovyviz' ) {
    $shop = trim( wp_strip_all_tags( (string) get_field( 'адрес', 'option' ) ) );
    if ( $shop !== '' ) {
      $full[] = $shop;
    }
  } elseif ( $info !== '' ) {
    // Замовлення, створені до етапу 2: тільки склеєний рядок плагіна
    $full[] = $info;
  }

  $full  = array_values( array_filter( $full, 'strlen' ) );
  $short = array_values( array_filter( $short, 'strlen' ) );

  return [
    'full'  => implode( ', ', $full ),
    'short' => implode( ' · ', $short ),
  ];
}

/**
 * Дані для шаблону.
 *
 * state: ok | not_found
 * Адміністратор може переглянути верстку з демо-даними: ?ty_preview=1
 */
function cb_ty_get_view_data(): array {
  if ( cb_ty_is_preview() ) {
    return [
      'state'        => 'ok',
      'order_number' => '10245',
      'payment'      => [ 'label' => 'Оплачено онлайн', 'tone' => 'success' ],
      'delivery'     => [
        'recipient' => "[Ім'я Прізвище]",
        'phone'     => '+380 •• ••• •• 67',
        'full'      => 'Нова Пошта, відділення № [N], [місто]',
        'short'     => 'Нова Пошта · відділення № [N]',
      ],
    ];
  }

  $order_id = cb_ty_current_order();

  if ( ! $order_id ) {
    return [ 'state' => 'not_found', 'order_number' => '', 'payment' => [], 'delivery' => [] ];
  }

  // Отримувач — завжди покупець (billing), у т.ч. для замовлень «як подарунок»
  $recipient = (string) cb_ty_meta( $order_id, 'simplebasket_billing_info->name' );
  $phone     = (string) cb_ty_meta( $order_id, 'simplebasket_billing_info->phone' );

  $delivery = cb_ty_delivery_text( $order_id );

  return [
    'state'        => 'ok',
    'order_number' => (string) $order_id, // Order::get_order_id() = post ID — той самий номер, що в листі
    'payment'      => cb_ty_payment_status( $order_id ),
    'delivery'     => [
      'recipient' => trim( $recipient ),
      'phone'     => cb_ty_mask_phone( $phone ),
      'full'      => $delivery['full'],
      'short'     => $delivery['short'],
    ],
  ];
}

/**
 * GA4 purchase: дані беруться із замовлення, не з кошика.
 *
 * Позиції — meta simplebasket_cart_items (Cart::toArray() на момент оформлення, тільки масиви).
 * Формат items — як у плагіні AKWEB GA4 (item_id, item_name, item_category, price, quantity),
 * щоб теги GTM, налаштовані на подію purchase, працювали без змін.
 */
function cb_ty_purchase_payload( int $order_id ): array {
  // Ключі рядка кошика: константи \SimpleBasket\Cart::QUO / PRICE / TOTAL / DECOR / TITLE
  $cart = get_post_meta( $order_id, 'simplebasket_cart_items', true );
  $rows = is_array( $cart ) ? ( $cart['_items'] ?? $cart['items'] ?? [] ) : [];

  $items = [];
  $sum   = 0.0;

  foreach ( (array) $rows as $product_id => $row ) {
    $product_id = (int) $product_id;
    if ( $product_id <= 0 || ! is_array( $row ) ) {
      continue;
    }

    $qty  = (float) ( $row['SIMPLE_BASKET_QUO'] ?? 1 );
    $qty  = $qty > 0 ? $qty : 1;
    $line = ( isset( $row['SIMPLE_BASKET_TOTAL'] ) && is_numeric( $row['SIMPLE_BASKET_TOTAL'] ) )
      ? (float) $row['SIMPLE_BASKET_TOTAL']
      : (float) ( $row['SIMPLE_BASKET_PRICE'] ?? 0 ) * $qty + (float) ( $row['SIMPLE_BASKET_DECOR'] ?? 0 );

    // GA4 очікує цілу кількість. Для товарів на вагу (кг) — 1 позиція з ціною всього рядка.
    $is_int   = abs( $qty - round( $qty ) ) < 0.0001;
    $quantity = $is_int ? (int) round( $qty ) : 1;

    $category = null;
    if ( class_exists( '\SimpleBasket\Product' ) && method_exists( '\SimpleBasket\Product', 'get_parent_product_category' ) ) {
      $slug = \SimpleBasket\Product::get_parent_product_category( null, $product_id );
      $term = $slug ? get_term_by( 'slug', $slug, 'product_category' ) : null;
      $category = ( $term && ! is_wp_error( $term ) ) ? $term->name : null;
    }

    $name = get_the_title( $product_id );
    if ( $name === '' ) {
      $name = (string) ( $row['SIMPLE_BASKET_TITLE'] ?? '' );
    }

    $items[] = [
      'item_id'       => (string) $product_id,
      'item_name'     => $name,
      'item_category' => $category,
      'price'         => round( $line / $quantity, 2 ),
      'quantity'      => $quantity,
    ];

    $sum += $line;
  }

  // Сума, яку платить покупець (товари + фото-пакет, без доставки — так рахує плагін)
  $value = (float) cb_ty_meta( $order_id, 'simplebasket_payment_info->total', 0 );
  if ( $value <= 0 ) {
    $value = $sum;
  }

  return [
    'transaction_id' => (string) $order_id,
    'value'          => round( $value, 2 ),
    'currency'       => (string) cb_ty_meta( $order_id, 'simplebasket_payment_info->currency', 'UAH' ),
    'payment_type'   => cb_ty_payment_method( $order_id ),
    'items'          => $items,
  ];
}

/**
 * Чи треба відправити purchase для цього замовлення.
 * - вже відправлено (meta _cb_ty_purchase_sent) — ні;
 * - LiqPay — тільки після підтвердження оплати (order_hold і далі);
 * - переказ — одразу.
 */
function cb_ty_should_send_purchase( int $order_id ): bool {
  if ( ! $order_id || get_post_meta( $order_id, '_cb_ty_purchase_sent', true ) ) {
    return false;
  }

  if ( cb_ty_payment_method( $order_id ) === 'liqpay' && ! cb_ty_is_paid_online( $order_id ) ) {
    return false;
  }

  return true;
}

/**
 * Позначка «purchase відправлено» — викликається з браузера після dataLayer.push.
 */
function cb_ty_ajax_purchase_sent() {
  $order_id = cb_ty_find_order(
    isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0,
    isset( $_POST['key'] ) ? (string) wp_unslash( $_POST['key'] ) : ''
  );

  if ( ! $order_id ) {
    wp_send_json_error( null, 403 );
  }

  add_post_meta( $order_id, '_cb_ty_purchase_sent', time(), true );
  wp_send_json_success();
}

add_action( 'wp_ajax_cb_ty_purchase_sent', 'cb_ty_ajax_purchase_sent' );
add_action( 'wp_ajax_nopriv_cb_ty_purchase_sent', 'cb_ty_ajax_purchase_sent' );

add_action( 'wp_footer', function () {
  if ( ! cb_ty_is_page() || cb_ty_is_preview() ) {
    return;
  }

  $order_id = cb_ty_current_order();
  if ( ! cb_ty_should_send_purchase( $order_id ) ) {
    return;
  }

  $payload = cb_ty_purchase_payload( $order_id );
  ?>
  <script>
    (function () {
      var ecommerce = <?php echo wp_json_encode( $payload ); ?>;
      var lsKey = 'cb_ty_purchase_' + ecommerce.transaction_id;

      try { if (window.localStorage && localStorage.getItem(lsKey)) return; } catch (e) {}

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ecommerce: null});
      window.dataLayer.push({event: 'purchase', ecommerce: ecommerce});

      try { localStorage.setItem(lsKey, '1'); } catch (e) {}

      try {
        var fd = new FormData();
        fd.append('action', 'cb_ty_purchase_sent');
        fd.append('order_id', ecommerce.transaction_id);
        fd.append('key', <?php echo wp_json_encode( isset( $_GET['key'] ) ? (string) wp_unslash( $_GET['key'] ) : '' ); ?>);
        var url = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
        if (!(navigator.sendBeacon && navigator.sendBeacon(url, fd))) {
          fetch(url, {method: 'POST', body: fd, keepalive: true, credentials: 'same-origin'});
        }
      } catch (e) {}
    })();
  </script>
  <?php
}, 20 );

/**
 * LiqPay: свій підклас шлюзу з result_url на сторінку подяки (lib/thank-you-liqpay.php).
 * Хук спрацьовує в Core::load_plugins() після підключення класів шлюзів і до їх ініціалізації,
 * register_gateway() перезаписує клас для коду 'liqpay'. Налаштування шлюзу — ті самі.
 */
add_action( 'simplebasket/load_plugins/include', function () {
  if ( ! class_exists( '\SimpleBasket\Gateway\Liqpay' ) || ! class_exists( '\SimpleBasket\GatewayApi' ) ) {
    return;
  }

  require_once __DIR__ . '/thank-you-liqpay.php';

  \SimpleBasket\GatewayApi::register_gateway_plugin( 'CB_Liqpay_Gateway', 'liqpay', __( 'Liqpay', 'simple_basket' ) );
} );

/**
 * Поточний стан оплати LiqPay — для опитування зі сторінки подяки, поки callback не прийшов.
 */
function cb_ty_ajax_status() {
  $order_id = cb_ty_find_order(
    isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0,
    isset( $_GET['key'] ) ? (string) wp_unslash( $_GET['key'] ) : ''
  );

  if ( ! $order_id ) {
    wp_send_json_error( null, 403 );
  }

  nocache_headers();
  wp_send_json_success( [ 'state' => cb_ty_liqpay_state( $order_id ) ] );
}

add_action( 'wp_ajax_cb_ty_status', 'cb_ty_ajax_status' );
add_action( 'wp_ajax_nopriv_cb_ty_status', 'cb_ty_ajax_status' );

add_action( 'wp_footer', function () {
  if ( ! cb_ty_is_page() || cb_ty_is_preview() ) {
    return;
  }

  $order_id = cb_ty_current_order();
  if ( ! $order_id || cb_ty_payment_method( $order_id ) !== 'liqpay' || cb_ty_liqpay_state( $order_id ) !== 'pending' ) {
    return;
  }

  $url = add_query_arg( [
    'action'   => 'cb_ty_status',
    'order_id' => $order_id,
    'key'      => isset( $_GET['key'] ) ? (string) wp_unslash( $_GET['key'] ) : '',
  ], admin_url( 'admin-ajax.php' ) );
  ?>
  <script>
    (function () {
      // Чекаємо callback LiqPay: раз на 3 с, до 30 с. Зміна стану → перезавантаження (сервер рендерить новий статус і purchase).
      var url = <?php echo wp_json_encode( $url ); ?>;
      var tries = 0;

      function check() {
        if (++tries > 10 || !window.fetch) return;
        fetch(url, {credentials: 'same-origin', cache: 'no-store'})
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (res && res.success && res.data && res.data.state !== 'pending') {
              window.location.reload();
            } else {
              setTimeout(check, 3000);
            }
          })
          .catch(function () { setTimeout(check, 3000); });
      }

      setTimeout(check, 3000);
    })();
  </script>
  <?php
}, 25 );

/**
 * Заголовки і кеш для сторінки.
 */
add_action( 'template_redirect', function () {
  if ( ! cb_ty_is_page() ) {
    return;
  }

  if ( ! defined( 'DONOTCACHEPAGE' ) ) {
    define( 'DONOTCACHEPAGE', true );
  }

  if ( ! cb_ty_is_preview() && ! cb_ty_current_order() ) {
    status_header( 404 );
  }

  nocache_headers();
  header( 'Cache-Control: no-store, private' );
  header( 'X-Robots-Tag: noindex, nofollow' );
  header( 'Referrer-Policy: strict-origin-when-cross-origin' );
} );

add_filter( 'wpseo_robots', function ( $robots ) {
  return cb_ty_is_page() ? 'noindex, nofollow' : $robots;
} );

add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function ( $ids ) {
  $page_ids = get_posts( [
    'post_type'   => 'page',
    'post_status' => 'any',
    'numberposts' => -1,
    'fields'      => 'ids',
    'meta_key'    => '_wp_page_template',
    'meta_value'  => CB_TY_TEMPLATE,
  ] );

  return array_merge( (array) $ids, $page_ids );
} );

add_action( 'wp_head', function () {
  if ( cb_ty_is_page() ) {
    echo '<meta name="referrer" content="strict-origin-when-cross-origin">' . "\n";
  }
}, 1 );

/**
 * Стилі сторінки.
 */
add_action( 'wp_enqueue_scripts', function () {
  if ( ! cb_ty_is_page() ) {
    return;
  }

  // На сайті підключено Montserrat 400/500 (templates/head.php); макет використовує 600 для статусу і кнопок.
  wp_enqueue_style( 'cb/thank-you-font', 'https://fonts.googleapis.com/css?family=Montserrat:600&subset=cyrillic&display=swap', [], null );

  $file = '/assets/thank-you/thank-you.css';

  wp_enqueue_style(
    'cb/thank-you',
    get_template_directory_uri() . $file,
    [ 'sage/main' ],
    filemtime( get_template_directory() . $file )
  );
}, 101 );

/**
 * Кошик: замість попапа #modal-payment-transfer — перехід на сторінку подяки.
 *
 * Плагін (Gateway/Transfer.php) повертає <script>$("#modal-payment-transfer").modal("show")</script>.
 * Bootstrap 3.3.7 дозволяє скасувати показ через preventDefault() на show.bs.modal.
 * ID і ключ замовлення беруться з куки cb_ty (ставиться на new_order у тій самій AJAX-відповіді).
 *
 * purchase тепер відправляє сторінка подяки. Якщо куки немає — показується старий попап,
 * і purchase відправляє, як і раніше, akweb_ga4_purchase_on_modal_script_impl() на shown.bs.modal.
 */
add_action( 'wp_footer', function () {
  if ( ! is_page_template( 'templates/template-cart.php' ) ) {
    return;
  }

  $url = cb_ty_page_url();
  if ( $url === '' ) {
    return; // сторінку не створено — лишається попап
  }
  ?>
  <script>
    (function ($) {
      if (!$) return;

      var base = <?php echo wp_json_encode( $url ); ?>;

      function readTicket() {
        var m = document.cookie.match(/(?:^|;\s*)cb_ty=([^;]*)/);
        if (!m) return null;
        var parts = decodeURIComponent(m[1]).split(':');
        if (parts.length !== 2 || !/^\d+$/.test(parts[0]) || !/^[A-Za-z0-9]{16,}$/.test(parts[1])) return null;
        return {id: parts[0], key: parts[1]};
      }

      $('#modal-payment-transfer').on('show.bs.modal', function (e) {
        var t = readTicket();
        if (!t) return; // фолбек: попап

        e.preventDefault();
        window.location.href = base + (base.indexOf('?') === -1 ? '?' : '&') +
          'order_id=' + encodeURIComponent(t.id) + '&key=' + encodeURIComponent(t.key);
      });
    })(window.jQuery);
  </script>
  <?php
}, 100 );
