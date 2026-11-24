<?php
/**
 * Шлюз LiqPay з поверненням покупця на сторінку подяки.
 *
 * Підключається з lib/thank-you.php на хуку simplebasket/load_plugins/include,
 * коли клас плагіна \SimpleBasket\Gateway\Liqpay уже завантажено.
 *
 * Відмінності від плагіна:
 *  - result_url — сторінка «Дякуємо» з номером замовлення;
 *  - параметри LiqPay проходять через фільтр cb_liqpay_data (paytypes: Apple Pay / Google Pay першими);
 *  - payment_form_for_order() — повторна оплата того самого замовлення («Спробувати ще раз»),
 *    order_id для LiqPay — "<id>-r<N>", callback прибирає суфікс.
 * Налаштування, ключі, sandbox — успадковуються з плагіна без змін.
 */

use SimpleBasket\Helper;
use SimpleBasket\Order;

class CB_Liqpay_Gateway extends \SimpleBasket\Gateway\Liqpay {

  /**
   * Копія \SimpleBasket\Gateway\Liqpay::process_payment() (simple-basket 1.0.0)
   * + result_url + фільтр параметрів.
   */
  function process_payment( $cart, $billing_info, $shipping_info ) {
    $order = new Order;

    $delivery_method = Helper::get_post_value( 'delivery-method' );
    Helper::update_session_value( 'delivery_method', $delivery_method );

    //setup our payment details
    $total                                = $cart->getTotalWithShipping();
    $payment_info                         = array();
    $payment_info['gateway_public_name']  = $this->public_name;
    $payment_info['gateway_private_name'] = $this->admin_name;
    $payment_info['method']               = $this->plugin_name;
    $payment_info['transaction_id']       = $order->get_id();
    $payment_info['status']               = __( 'Not paid', 'simple_basket' );
    $payment_info['time']                 = time();
    $payment_info['total']                = $total;
    $payment_info['currency']             = $this->currency;

    $order->save( array(
      'cart'          => $cart,
      'payment_info'  => $payment_info,
      'billing_info'  => $billing_info,
      'shipping_info' => $shipping_info,
      'paid'          => false,
    ) );

    wp_die( $this->payment_form_for_order( $order, (float) $total ) );
  }

  /**
   * Форма LiqPay для замовлення. $attempt > 0 — повторна спроба оплати.
   */
  public function payment_form_for_order( Order $order, float $total, int $attempt = 0 ): string {
    $data = [
      'action'      => 'hold',
      'amount'      => $total,
      'currency'    => $this->currency,
      'description' => 'Оплата продукции в магазине "' . get_bloginfo( 'name' ) . '"',
      'order_id'    => $order->get_id() . ( $attempt > 0 ? '-r' . $attempt : '' ),
      'version'     => 3,
      'server_url'  => Helper::store_page_url( 'confirm_order', false ),
      'sandbox'     => $this->sandbox,
    ];

    // повернення на сторінку подяки (ключ створюється на simplebasket_order/new_order у $order->save())
    $result_url = $order->ID ? cb_ty_order_url( (int) $order->ID ) : '';
    if ( $result_url !== '' ) {
      $data['result_url'] = $result_url;
    }

    $data = (array) apply_filters( 'cb_liqpay_data', $data, $order );

    return $this->liqpay->cnb_form( $data );
  }

  /**
   * Callback LiqPay. Копія батьківського методу + прибирання суфікса "-rN" повторної спроби.
   */
  public function process_confirm_order() {
    $raw       = isset( $_POST['data'] ) ? (string) $_POST['data'] : '';
    $signature = isset( $_POST['signature'] ) ? (string) $_POST['signature'] : '';

    if ( $raw === '' || ! hash_equals( $this->liqpay->str_to_sign( $this->secret_key . $raw . $this->secret_key ), $signature ) ) {
      wp_redirect( home_url() );
      exit;
    }

    $data = $this->liqpay->decode_params( $raw );
    file_put_contents( ABSPATH . 'post.log', sprintf( "%s: %s \n\n", date( 'H:i:s d-m-Y' ), json_encode( $data ) ), FILE_APPEND );

    $order_id = preg_replace( '/-r\d+$/', '', (string) ( $data['order_id'] ?? '' ) );
    $order    = new Order( $order_id );

    if ( $order->exists() ) {
      if ( ( $data['status'] ?? '' ) === 'hold_wait' ) {
        $order->change_status( 'hold', true );
        $order->update_meta( 'simplebasket_payment_info->status', __( 'Hold', 'simple_basket' ) );
      } else {
        $order->update_meta( 'simplebasket_payment_info->status', $data['status'] ?? '' );
      }
      if ( ! empty( $data['order_id'] ) && $data['order_id'] !== $order_id ) {
        // для hold_completion / refund потрібен саме той order_id, під яким пройшла оплата
        update_post_meta( $order->ID, '_cbbf_liqpay_order_id', sanitize_text_field( $data['order_id'] ) );
      }

      /**
       * Статус оплати LiqPay змінився (hold_wait / success / failure …) — напр. для синхронізації з CRM.
       *
       * @param Order  $order
       * @param string $status
       * @param array  $data   розшифрований callback LiqPay
       */
      do_action( 'cb_liqpay_status', $order, (string) ( $data['status'] ?? '' ), $data );
    }

    wp_die( '', 200 );
  }

  function order_paid( $order ) {
    $this->hold_action( $order, 'hold_completion' );
  }

  function order_cancel( $order ) {
    $this->hold_action( $order, 'refund' );
  }

  private function hold_action( $order, string $action ) {
    $liqpay_id = (string) get_post_meta( $order->ID, '_cbbf_liqpay_order_id', true );
    $response  = $this->liqpay->api( 'request', array(
      'action'   => $action,
      'version'  => '3',
      'order_id' => $liqpay_id !== '' ? $liqpay_id : $order->get_id(),
    ) );

    $response = @json_decode( $response );
    file_put_contents( ABSPATH . 'post.log', sprintf( "%s: %s \n\n", date( 'H:i:s d-m-Y' ), json_encode( $response ) ), FILE_APPEND );

    $order->update_meta( 'simplebasket_payment_info->status', $response->status ?? 'API Error' );
  }
}
