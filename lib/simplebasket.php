<?php

namespace Roots\Sage\SimpleBasket;

use SDLab\View;
use SimpleBasket\Order;

add_filter( 'simplebasket/checkout/email/new_order', static function ( string $template, Order $order ) {
  $view_content = View::returnView( 'email/user-order-new', compact( 'order' ) );

  return View::returnView( 'email/layout', compact( 'view_content' ) );
}, 10, 2 );
