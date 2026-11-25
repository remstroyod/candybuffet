<?php

/**
 * @var SimpleBasket\Order $order
 */

use SimpleBasket\Helper;

$cart = $order->get_cart();

?>

<table style="border-collapse:collapse;display:table;padding:0;width:100%">
  <tbody>
  <tr>
    <td style="margin:0;padding:0">
      <table align="center" style="border-collapse:collapse;margin:0 auto;padding:0;width:650px">
        <tbody>
        <tr>
          <td height="16" style="line-height:16px;height:16px;padding:0">&nbsp;</td>
        </tr>
        <tr>
          <td style="margin:0;padding:0 16px">
            <table style="border-collapse:collapse;display:table;padding:0;width:100%">
              <tbody>
              <tr>

                <th style="margin:0 auto;padding:0;width:230px;text-align:center">
                  <span style="color:#ff7878;font-family:Arial,Helvetica,sans-serif;font-size:14px;margin:0;padding:0">
                    <a href="https://candybuffet.kiev.ua/"><img src="https://candybuffet.kiev.ua/wp-content/uploads/2024/03/group-291-1.png" width="230" height="101" border="0" alt="candyBuffet.kiev.ua" style="display:block;margin:0 auto;max-width:237px;width:auto"></a>
                  </span>
                </th>

                <th style="margin:0 auto;padding:0;text-align:center;display:none;overflow:hidden"></th>

                <th style="margin:0 auto;padding:0;padding-left:24px;text-align:right"></th>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td height="16" style="line-height:16px;height:16px;padding:0">&nbsp;</td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>

  <tr>
    <td height="16" style="line-height:16px;height:16px;padding:0">&nbsp;</td>
  </tr>

  <tr>
    <td style="margin:0;padding:0 16px;vertical-align:middle;text-align:left">
      <span style="display:block;font-size:20px;line-height:28px;width:100%;font-weight:400;font-family:Arial,Helvetica,sans-serif">CUSTOMERNAME, дякуємо за ваше замовлення!</span>
    </td>
  </tr>

  <tr>
    <td height="16" style="line-height:16px;height:16px;padding:0">
      &nbsp;
    </td>
  </tr>

  <tr>
    <td style="margin:0;padding:0 16px">
      <table style="border-collapse:collapse;display:table;padding:0;width:100%">

        <tbody>
        <tr>
          <td style="margin:0;padding:0">
            <table style="border-collapse:collapse;display:table;padding:0;width:100%">
              <tbody>
              <tr>
                <td style="margin:0;padding:0;display:block">
                  <table style="border-collapse:collapse;display:table;padding:0;width:100%">

                    <tbody>
                    <tr>
                      <td style="margin:0;padding:0">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <tr>
                            <th style="margin:0;padding:16px 0 8px;width:auto">
                              <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                <tbody>
                                <tr>

                                  <th style="margin:0 auto;padding:0;width:360px;vertical-align:middle">
                                    <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                      <tbody>
                                      <tr>
                                        <td style="margin:0;padding:0;text-align:left">
                                          <span style="padding:0;color:#2e2e2e;font-size:18px;line-height:24px;font-weight:600;font-family:Arial,Helvetica,sans-serif">Замовлення №<?= $order->get_order_id() ?></span>
                                        </td>
                                      </tr>
                                      </tbody>
                                    </table>
                                  </th>

                                  <th style="margin:0;padding:0;vertical-align:middle;padding-left:16px">
                                    <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                      <tbody>
                                      <tr>
                                        <td style="margin:0;padding:0;text-align:right">
                                          <span style="padding:0;color:#2e2e2e;font-size:18px;line-height:24px;font-weight:600;font-family:Arial,Helvetica,sans-serif">Дата CURRENTDATETIME</span>
                                        </td>
                                      </tr>
                                      </tbody>
                                    </table>
                                  </th>

                                </tr>
                                </tbody>
                              </table>
                            </th>
                          </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>

                    <!-- Product list -->
                    <?php foreach ( $order->get_cart()->get_items_as_objects() as $product ):  ?>
                      <tr>
                        <th style="margin:0 auto;padding:20px 0;vertical-align:top;border-top:#2e2e2e 1px solid">
                          <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                            <tbody>
                            <tr>
                              <th style="margin:0 auto;padding:0;width:160px;vertical-align:top">
                                <table style="border-collapse:collapse;padding:0;width:100%">
                                  <tbody>
                                  <tr>
                                    <td style="margin:0;padding:0;height:160px">
                                      <img src="<?php echo get_the_post_thumbnail_url( $product ) ?>" border="0"
                                           style="max-width:160px;width:auto;max-height:160px;height:auto;display:block;margin:0 auto">
                                    </td>
                                  </tr>
                                  </tbody>
                                </table>
                              </th>

                              <th style="margin:0 auto;padding:0;vertical-align:bottom;padding-left:16px;">
                                <table style="border-collapse:collapse;padding:0;width:100%">

                                  <tbody>
                                  <tr>
                                    <td height="8" style="line-height:8px;height:8px;padding:0">&nbsp;</td>
                                  </tr>

                                  <tr>
                                    <td style="margin:0;padding:0;text-align:left">
                                      <span style="display:block;width:100%;color:#2e2e2e;margin:0;padding:0;text-decoration:none;font-weight:400;font-size:16px;line-height:19px;font-family:Arial,Helvetica,sans-serif"><?= get_the_title($product) ?></span>
                                    </td>
                                  </tr>

                                  <tr>
                                    <td height="6" style="line-height:6px;height:6px;padding:0">
                                      &nbsp;
                                    </td>
                                  </tr>

                                  <tr>
                                    <td style="margin:0;padding:0;text-align:left">
                                      <span style="display:block;width:100%;padding:0;font-size:12px;line-height:14px;font-weight:400;font-family:Arial,Helvetica,sans-serif">Ціна: <span><?= $product->price_lowest() ?></span> <?= Helper::format_currency() ?></span>
                                    </td>
                                  </tr>

                                  </tbody>
                                </table>
                              </th>

                              <th style="margin:0 auto;padding:0;width:90px;vertical-align:bottom;padding-left:16px">
                                <table style="border-collapse:collapse;padding:0;width:100%">
                                  <tbody>
                                  <tr>
                                    <th style="margin:0;padding:0;text-align:left;display:block">
                                      <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                        <tbody>
                                        <tr>
                                          <th style="margin:0;padding:0;display:block;text-align:left">
                                          <span style="display:inline-block;text-align:center;padding:0;color:#2e2e2e;font-size:16px;line-height:19px;font-weight:400;font-family:Arial,Helvetica,sans-serif"><?php echo format_weight($product->ID,  $product->qty) ?></span>
                                          </th>
                                        </tr>
                                        </tbody>
                                      </table>
                                    </th>
                                  </tr>
                                  </tbody>
                                </table>
                              </th>

                              <th style="margin:0 auto;padding:0;width:90px;vertical-align:bottom;padding-left:16px">
                                <table style="border-collapse:collapse;padding:0;width:100%">
                                  <tbody>
                                  <tr>
                                    <th style="margin:0;padding:0;text-align:left;display:block">
                                      <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                        <tbody>
                                        <tr>
                                          <th style="margin:0;padding:0;display:block;text-align:right">
                                            <span style="display:inline-block;text-align:center;padding:0;color:#2e2e2e;font-size:16px;line-height:19px;font-weight:400;font-family:Arial,Helvetica,sans-serif"><?= $product->total() ?></span>
                                          </th>
                                        </tr>
                                        </tbody>
                                      </table>
                                    </th>
                                  </tr>
                                  </tbody>
                                </table>
                              </th>
                            </tr>
                            </tbody>
                          </table>
                        </th>
                      </tr>
                    <?php endforeach; ?>
                    <!-- // END Product list -->

                    <?php // Black Friday: знижка, пакет, доставка, подарунок (плагін cb-black-friday)
                    if ( class_exists( '\CandyBuffet\BlackFriday\Checkout' ) ):
                      $cbbf_rows = \CandyBuffet\BlackFriday\Checkout::order_summary( (int) $order->ID );
                      unset( $cbbf_rows['Разом до сплати'] );
                      if ( $cbbf_rows ): ?>
                    <tr>
                      <th style="margin:0 auto;padding:16px 0 4px;vertical-align:top;border-top:#2e2e2e 1px solid">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <?php foreach ( $cbbf_rows as $cbbf_k => $cbbf_v ): ?>
                            <tr>
                              <td style="margin:0;padding:4px 0;text-align:left;color:#2e2e2e;font-size:14px;line-height:18px;font-weight:400;font-family:Arial,Helvetica,sans-serif"><?= esc_html( $cbbf_k ) ?></td>
                              <td style="margin:0;padding:4px 0 4px 16px;text-align:right;color:<?= ( strpos( $cbbf_k, 'Знижка' ) === 0 || strpos( $cbbf_k, 'Промокод' ) === 0 ) ? '#3c7a48' : '#2e2e2e' ?>;font-size:14px;line-height:18px;font-weight:400;font-family:Arial,Helvetica,sans-serif"><?= nl2br( esc_html( $cbbf_v ) ) ?></td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </th>
                    </tr>
                    <?php endif; endif; ?>

                    <tr>
                      <th style="margin:0 auto;padding:20px 0;vertical-align:top;border-top:#2e2e2e 1px solid">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <tr>

                            <th style="margin:0 auto;padding:0">
                              <table style="border-collapse:collapse;padding:0;width:100%">

                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:right">
                                    <span style="display:block;width:100%;color:#2e2e2e;padding:0;font-size:18px;line-height:22px;font-weight:600;font-family:Arial,Helvetica,sans-serif">Разом: </span>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </th>

                            <th style="margin:0 auto;padding:0;width:90px;vertical-align:top;padding-left:16px">
                              <table style="border-collapse:collapse;padding:0;width:100%">
                                <tbody>
                                <tr>
                                  <th style="margin:0;padding:0;text-align:left;display:block">
                                    <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                                      <tbody>
                                      <tr>
                                        <th style="margin:0;padding:0;display:block;text-align:right">
                                          <span style="display:inline-block;text-align:center;padding:0;color:#2e2e2e;font-size:20px;line-height:24px;font-weight:400;font-family:Arial,Helvetica,sans-serif">TOTAL</span>
                                        </th>
                                      </tr>
                                      </tbody>
                                    </table>
                                  </th>
                                </tr>
                                </tbody>
                              </table>
                            </th>
                          </tr>
                          </tbody>
                        </table>
                      </th>
                    </tr>

                    <tr>
                      <th style="margin:0 auto;padding:20px 0;vertical-align:top;">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <tr>
                            <th style="margin:0 auto;padding:0;width:160px;vertical-align:top">
                              <table style="border-collapse:collapse;padding:0;width:100%">
                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;color:#2e2e2e;padding:0;font-size:18px;line-height:1;font-weight:600;font-family:Arial,Helvetica,sans-serif">Доставка</span>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </th>

                            <th style="margin:0 auto;padding:0;padding-left:16px">
                              <table style="border-collapse:collapse;padding:0;width:100%">

                                <tbody>

                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;padding:0;font-size:14px;line-height:16px;font-weight:400;font-family:Arial,Helvetica,sans-serif">DELIVERY_METHOD</span>
                                  </td>
                                </tr>

                                <tr>
                                  <td height="8" style="line-height:8px;height:8px;padding:0">
                                    &nbsp;
                                  </td>
                                </tr>

                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;color:#2e2e2e;margin:0;padding:0;text-decoration:none;font-weight:400;font-size:14px;line-height:16px;font-family:Arial,Helvetica,sans-serif">DELIVERY_INFO</span>
                                  </td>
                                </tr>

                                </tbody>
                              </table>
                            </th>

                            <th style="margin:0 auto;padding:0;width:105px;vertical-align:top;padding-left:16px">&nbsp;</th>
                          </tr>
                          </tbody>
                        </table>
                      </th>
                    </tr>

                    <tr>
                      <th style="margin:0 auto;padding:20px 0;vertical-align:top;">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <tr>
                            <th style="margin:0 auto;padding:0;width:160px;vertical-align:top">
                              <table style="border-collapse:collapse;padding:0;width:100%">

                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;color:#2e2e2e;padding:0;font-size:18px;line-height:1;font-weight:600;font-family:Arial,Helvetica,sans-serif">Оплата</span>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </th>

                            <th style="margin:0 auto;padding:0;padding-left:16px">
                              <table style="border-collapse:collapse;padding:0;width:100%">
                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;padding:0;font-size:14px;line-height:16px;font-weight:400;font-family:Arial,Helvetica,sans-serif">PAYMENT_METHOD</span>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </th>
                          </tr>
                          </tbody>
                        </table>
                      </th>
                    </tr>

                    <tr>
                      <th style="margin:0 auto;padding:20px 0;vertical-align:top;">
                        <table style="border-collapse:collapse;display:table;padding:0;width:100%">
                          <tbody>
                          <tr>
                            <th style="margin:0 auto;padding:0;width:160px;vertical-align:top">
                              <table style="border-collapse:collapse;padding:0;width:100%">

                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;color:#2e2e2e;padding:0;font-size:18px;line-height:1;font-weight:600;font-family:Arial,Helvetica,sans-serif">Покупець</span>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </th>


                            <th style="margin:0 auto;padding:0;padding-left:16px">
                              <table style="border-collapse:collapse;padding:0;width:100%">

                                <tbody>
                                <tr>
                                  <td style="margin:0;padding:0;text-align:left">
                                    <span style="display:block;width:100%;padding:0;font-size:14px;line-height:16px;font-weight:400;font-family:Arial,Helvetica,sans-serif">CUSTOMERNAME</span>
                                  </td>
                                </tr>

                                SHIPPINGINFO
                                </tbody>
                              </table>
                            </th>
                          </tr>
                          </tbody>
                        </table>
                      </th>
                    </tr>

                    </tbody>
                  </table>
                </td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>

  </tbody>
</table>
