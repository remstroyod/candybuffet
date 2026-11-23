<?php
/**
 * Шлюз LiqPay з поверненням покупця на сторінку подяки.
 *
 * Підключається з lib/thank-you.php на хуку simplebasket/load_plugins/include,
 * коли клас плагіна \SimpleBasket\Gateway\Liqpay уже завантажено.
 *
 * Відрізняється від плагіна тільки параметром result_url у process_payment().
 * Callback (process_confirm_order, /checkout/confirm/), налаштування, ключі, sandbox —
 * успадковуються з плагіна без змін.
 */

use SimpleBasket\Helper;
use SimpleBasket\Order;

class CB_Liqpay_Gateway extends \SimpleBasket\Gateway\Liqpay {

  /**
   * Копія \SimpleBasket\Gateway\Liqpay::process_payment() (simple-basket 1.0.0)
   * + result_url. Фільтра для параметрів LiqPay у плагіні немає.
   */
  function process_payment( $cart, $billing_info, $shipping_info ) {
    $order      = new Order;
    $return_url = Helper::store_page_url( 'confirm_order', false );

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

    $data = [
      'action'      => 'hold',
      'amount'      => $total,
      'currency'    => $this->currency,
      'description' => 'Оплата продукции в магазине "' . get_bloginfo( 'name' ) . '"',
      'order_id'    => $order->get_id(),
      'version'     => 3,
      'server_url'  => $return_url,
      'sandbox'     => $this->sandbox,
    ];

    // Додано: повернення на сторінку подяки (ключ створюється на simplebasket_order/new_order у $order->save())
    $result_url = $order->ID ? cb_ty_order_url( (int) $order->ID ) : '';
    if ( $result_url !== '' ) {
      $data['result_url'] = $result_url;
    }

    $html = $this->liqpay->cnb_form( $data );
    wp_die( $html );
  }
}
