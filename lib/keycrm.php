<?php

use SimpleBasket\Order;
use SimpleBasket\Product;

add_action( 'simplebasket_order/new_order', function ( Order $order ) {
  if ( ! defined( 'KEYCRM_TOKEN' ) ) {
    return;
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
  $delivery_method_id    = $cart->get_delivery_methods()[ $delivery_method ]['crm_method_id'];

  $jayParsedAry = [
    'source_id'     => 1,
    'source_name'   => 'Candy Buffet',
    'source_uuid'   => $order->ID,
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
    ],
    'products'      => $products,
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
    'Authorization' => 'Bearer ' . KEYCRM_TOKEN,
  );

  add_action( 'http_api_debug', 'debug_wp_remote_post_and_get_request', 10, 5 );

  $response = wp_remote_post( 'https://openapi.keycrm.app/v1/order', [
    'timeout' => 20,
    'headers' => $headers,
    'body'    => json_encode( $jayParsedAry ),
  ] );

  remove_action( 'http_api_debug', 'debug_wp_remote_post_and_get_request', 10 );

  if ( ! is_wp_error( $response ) ) {
    $response_body = wp_remote_retrieve_body( $response );

    update_post_meta( $order->ID, 'keycrm_response', $response_body );
  }
} );

function debug_wp_remote_post_and_get_request( $response, $context, $class, $r, $url ) {
  error_log( '------------------------------' );
  error_log( $url );
  error_log( json_encode( $response ) );
  error_log( $class );
  error_log( $context );
  error_log( json_encode( $r ) );
}

