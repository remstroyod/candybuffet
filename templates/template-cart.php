<?php
/**
 * Template Name: Cart
 *
 * Кошик + оформлення на одній сторінці (дві колонки). Нові блоки Black Friday — плагін cb-black-friday.
 * Порядок на мобільному: шкала → товари → підказка акції → «Обрати ще щось?» → пакет → «Собі / На подарунок»
 * → контакти → доставка → оплата; сума і кнопка закріплені внизу.
 */

use SimpleBasket\Cart;
use SimpleBasket\Product;
use CandyBuffet\BlackFriday\CartState;
use CandyBuffet\BlackFriday\Render;
use CandyBuffet\BlackFriday\Settings;

$cart   = Cart::get_instance();
$has_bf = class_exists( CartState::class );
$state  = ( $has_bf && $cart->get_items() ) ? CartState::state( 'page' ) : null;

// Способи доставки, дозволені для всіх товарів у кошику (ACF «dostavka» товару).
$allowed_delivery = array_keys( $cart->get_delivery_methods() );
foreach ( array_keys( $cart->get_items() ) as $pid ) {
  $product_delivery = get_field( 'dostavka', $pid );
  if ( is_array( $product_delivery ) ) {
    $allowed_delivery = array_intersect( $allowed_delivery, $product_delivery );
  }
}
$active_method = $has_bf ? CartState::delivery() : null;
$order_for     = $has_bf ? CartState::order_for() : 'self';
$selected_pack = $has_bf ? CartState::pack() : null;
$packs         = $has_bf ? CartState::packs() : [];
$gateways      = apply_filters( 'simplebasket/gateways', [] );
$card_max      = $has_bf ? (int) Settings::get( 'card_text_max' ) : 200;
$no_receipt    = $has_bf ? (bool) Settings::get( 'gift_no_receipt_default' ) : true;
$fragments     = $state ? Render::fragments( $state ) : [];
$card_on       = $has_bf && Settings::get( 'card_enabled' );
$card_price    = $has_bf ? (int) Settings::get( 'card_price' ) : 0;
$card_selected = $has_bf && CartState::card_selected();
// One textarea for the greeting card: JS moves it into the gift block («На подарунок») or the pack block («Собі»).
$card_field = '<div class="form-group cbbf-card-text" data-cbbf-card-field>'
  . '<textarea name="gift[card_text]" id="gift-card-text" rows="3" maxlength="' . $card_max . '" class="form-control" data-cbbf-card></textarea>'
  . '<label for="gift-card-text" class="control-label">Текст листівки (до ' . $card_max . ' символів)</label>'
  . '<span class="cbbf-counter" data-cbbf-card-counter>0 / ' . $card_max . '</span>'
  . '</div>';
$card_option = $card_on
  ? '<div class="cbbf-cardopt" data-cbbf-card-self>'
    . '<label class="cbbf-check radio" data-cbbf-card-toggle-wrap>'
    . '<input type="checkbox" name="extras[card]" value="1" data-cbbf-card-toggle' . ( $card_selected ? ' checked' : '' ) . '>'
    . '<span>' . esc_html( Settings::get( 'card_label' ) ) . ' — <strong>' . esc_html( $card_price > 0 ? Settings::money( $card_price ) : 'безкоштовно' ) . '</strong></span>'
    . '</label>'
    . '<div class="cbbf-cardopt__slot" data-cbbf-card-slot="self">' . $card_field . '</div>'
    . '</div>'
  : '';
$f             = static fn( string $n ) => $fragments[ $n ] ?? '';
?>
<? get_template_part( 'templates/header', 'breadcrumbs' ) ?>
<div class="container">
  <div class="page-wrap">
    <div class="page-wrap__content">
      <div class="page-wrap__header">
        <? get_template_part( 'templates/part', 'styled-heding' ) ?>
      </div>
      <div class="cart-box cbbf-cart" data-cbbf-cart>
        <? if ( $state ): ?>
          <?= \CandyBuffet\BlackFriday\Checkout::retry_banner() ?>
          <?= Render::promo_bar() ?>

          <form novalidate class="simplebox__checkout_form cart-box__inner cbbf-form" data-cbbf-form>
            <input type="hidden" name="action" value="simplebasket_process_checkout">
            <input type="hidden" name="nonce" value="<?= wp_create_nonce() ?>">

            <!-- ===================== Ліва колонка: товари ===================== -->
            <div class="cart-box__content">
              <div class="cart-box__widgets">

                <div class="cart-box__widget cbbf-widget-scale" data-cbbf-frag="scale"><?= $f( 'scale' ) ?></div>

                <div class="cart-box__widget cbbf-widget-lines">
                  <div class="cbbf-lines" data-cbbf-frag="lines"><?= $f( 'lines' ) ?></div>
                  <div data-cbbf-frag="hint"><?= $f( 'hint' ) ?></div>
                </div>

                <div class="cart-box__widget cbbf-widget-upsell" data-cbbf-frag="upsell"><?= $f( 'upsell' ) ?></div>

                <? if ( $packs ): ?>
                  <div class="cart-box__widget cbbf-packs" id="cart-photo-packs">
                    <div class="cart-box__widget-title"><?= esc_html( CartState::packs_title() ) ?></div>
                    <div class="cbbf-packs__list">
                      <label class="cbbf-pack radio">
                        <input type="radio" name="extras[pack]" value="none" data-cbbf-pack <?= checked( $selected_pack, null, false ) ?>>
                        <span class="cbbf-pack__content">
                          <span class="cbbf-pack__image cbbf-pack__image--none" aria-hidden="true"></span>
                          <span class="cbbf-pack__label">Без пакета</span>
                          <span class="cbbf-pack__price">0&nbsp;₴</span>
                        </span>
                      </label>
                      <? foreach ( $packs as $p ): ?>
                        <label class="cbbf-pack radio">
                          <input type="radio" name="extras[pack]" value="<?= (int) $p['key'] ?>" data-cbbf-pack
                                 data-label="<?= esc_attr( $p['label'] ) ?>" <?= checked( $selected_pack && $selected_pack['key'] === $p['key'], true, false ) ?>>
                          <span class="cbbf-pack__content">
                            <span class="cbbf-pack__image">
                              <? if ( $p['image'] ): ?>
                                <img src="<?= esc_url( $p['image'] ) ?>" alt="<?= esc_attr( $p['label'] ) ?>" loading="lazy" width="120" height="120">
                              <? else: ?>
                                <span class="cbbf-pack__placeholder">Фото</span>
                              <? endif ?>
                            </span>
                            <span class="cbbf-pack__label"><?= esc_html( $p['label'] ) ?></span>
                            <span class="cbbf-pack__price"><?= esc_html( Settings::money( $p['price'] ) ) ?></span>
                          </span>
                        </label>
                      <? endforeach ?>
                    </div>
                    <?= $card_option ?>
                  </div>
                <? elseif ( $card_option ): ?>
                  <div class="cart-box__widget cbbf-packs" id="cart-photo-packs"><?= $card_option ?></div>
                <? endif ?>

                <div class="cbbf-coupon-box cbbf-desktop-only" data-cbbf-frag="coupon"><?= $f( 'coupon' ) ?></div>

                <div class="cart-box__widget cart-box__widget_simple cbbf-summary-box cbbf-desktop-only" data-cbbf-frag="summary"><?= $f( 'summary' ) ?></div>

                <div class="cart-box__buttons cbbf-desktop-only">
                  <div class="cart-box__buttons-item">
                    <a href="<?= esc_url( get_post_type_archive_link( Product::get_post_type() ) ) ?>" class="btn btn-bordered cart-box__button">Продовжити покупки</a>
                  </div>
                  <div class="cart-box__buttons-item">
                    <button type="submit" class="btn btn-default cart-box__button" data-cbbf-submit>Оформити</button>
                  </div>
                </div>
                <div class="cart-box__list">
                  <div>
                    <div class="cart-box__list-item">
                      <a target="_blank" href="<?= get_the_permalink( 81 ) ?>" class="cart-box__list-link">Більше про оплату та доставку</a>
                    </div>
                  </div>
                  <div>
                    <div class="cart-box__list-item">
                      <a target="_blank" href="<?= get_the_permalink( 940 ) ?>" class="cart-box__list-link">Договір оферти</a>
                    </div>
                  </div>
                  <div>
                    <div class="cart-box__list-item">
                      <button type="button" class="cbbf-link-btn cart-box__list-link" data-cbbf-clear>Очистити кошик</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ===================== Права колонка: оформлення ===================== -->
            <div class="cart-box__sidebar">

              <div class="cart-box__widget cbbf-orderfor" id="cart-gift">
                <div class="cart-box__widget-title">Для кого замовлення?</div>
                <div class="cbbf-segment" role="radiogroup" aria-label="Для кого замовлення">
                  <label class="cbbf-segment__item">
                    <input type="radio" name="order_for" value="self" data-cbbf-orderfor <?= checked( $order_for, 'self', false ) ?>>
                    <span>Собі</span>
                  </label>
                  <label class="cbbf-segment__item">
                    <input type="radio" name="order_for" value="gift" data-cbbf-orderfor <?= checked( $order_for, 'gift', false ) ?>>
                    <span>На подарунок</span>
                  </label>
                </div>

                <fieldset class="cbbf-gift" data-cbbf-gift <?= $order_for === 'gift' ? '' : 'hidden disabled' ?>>
                  <legend class="sr-only">Дані отримувача</legend>
                  <div class="form-group">
                    <input name="gift[to_name]" id="gift-to-name" type="text" class="form-control" autocomplete="off" aria-required="true">
                    <label for="gift-to-name" class="control-label">Ім’я отримувача</label>
                  </div>
                  <div class="form-group">
                    <input name="gift[to_phone]" id="gift-to-phone" type="tel" inputmode="tel" class="form-control" autocomplete="off"
                           data-mask="+380 99 999 99 99" aria-required="true">
                    <label for="gift-to-phone" class="control-label">Телефон отримувача</label>
                  </div>
                  <div class="cbbf-gift__card-slot" data-cbbf-card-slot="gift"><?= $card_on ? '' : $card_field ?></div>
                  <? if ( $packs ): ?>
                    <p class="cbbf-gift__tip" data-cbbf-pack-tip <?= $selected_pack ? 'hidden' : '' ?>>Фірмовий пакет? <a href="#cart-photo-packs">Оберіть розмір</a></p>
                  <? endif ?>
                  <div class="form-group cbbf-date">
                    <input name="gift[date]" id="gift-date" type="date" class="form-control" min="<?= esc_attr( wp_date( 'Y-m-d' ) ) ?>">
                    <label for="gift-date" class="control-label cbbf-label-static">Бажана дата доставки</label>
                  </div>
                  <label class="cbbf-check radio">
                    <input type="checkbox" name="gift[no_receipt]" value="1" <?= checked( $no_receipt, true, false ) ?>>
                    <span>Не вкладати чек з ціною</span>
                  </label>
                </fieldset>
              </div>

              <div class="cart-box__widget">
                <div class="cart-box__widget-title">Контактні дані</div>
                <div class="cart-box__contacts">
                  <div class="form-group">
                    <input name="billing[name]" id="field-cart-contact-name" type="text" autocomplete="name" aria-required="true" class="form-control">
                    <label for="field-cart-contact-name" class="control-label">Ім’я</label>
                  </div>
                  <div class="form-group">
                    <input name="billing[phone]" id="field-cart-contact-phone" type="tel" inputmode="tel" autocomplete="tel"
                           aria-required="true" data-mask="+380 99 999 99 99" class="form-control">
                    <label for="field-cart-contact-phone" class="control-label">Телефон</label>
                  </div>
                  <div class="form-group">
                    <input name="billing[email]" id="field-cart-contact-email" type="email" inputmode="email" autocomplete="email" class="form-control">
                    <label for="field-cart-contact-email" class="control-label">E-mail — для чеку і статусу (необов’язково)</label>
                    <? if ( $has_bf && \CandyBuffet\BlackFriday\Abandoned::enabled() && Settings::get( 'abandoned_note' ) ): ?>
                      <span class="cbbf-field-note"><?= esc_html( Settings::get( 'abandoned_note' ) ) ?></span>
                    <? endif ?>
                  </div>
                </div>
              </div>

              <div class="cart-box__widget chooser-required" data-cbbf-delivery-box>
                <div class="cart-box__widget-title">Виберіть спосіб доставки</div>
                <div class="delivery-chooser">
                  <div class="delivery-chooser__items">
                    <? foreach ( $cart->get_delivery_methods() as $method_key => $method ):
                      $is_allowed = in_array( $method_key, $allowed_delivery, true );
                      $labels     = [
                        'samovyviz' => 'Безкоштовно',
                        'taxi'      => $has_bf ? (string) Settings::get( 'delivery_label_taxi' ) : 'за тарифом таксі',
                        'nv'        => $has_bf ? (string) Settings::get( 'delivery_label_nv' ) : $method['price'],
                      ];
                      $price_text = ( $labels[ $method_key ] ?? '' ) !== '' ? $labels[ $method_key ] : $method['price'];
                      $title_text = $method_key === 'taxi' && mb_stripos( $method['title'], 'Києв' ) === false ? $method['title'] . ' (по Києву)' : $method['title'];
                      ?>
                      <div data-target="#delivery-method-<?= esc_attr( $method_key ) ?>" class="delivery-chooser__item radio">
                        <label class="delivery-chooser__item-wrap">
                          <input <?= $is_allowed ? '' : 'disabled' ?> type="radio" name="delivery-method" value="<?= esc_attr( $method_key ) ?>"
                                 data-cbbf-delivery <?= checked( $active_method, $method_key, false ) ?>>
                          <span class="delivery-chooser__item-content">
                            <span class="delivery-chooser__item-label"><?= esc_html( $title_text ) ?></span>
                            <span class="delivery-chooser__item-value" data-cbbf-delivery-price="<?= esc_attr( $method_key ) ?>"><?= esc_html( $price_text ) ?></span>
                          </span>
                        </label>
                      </div>
                    <? endforeach ?>
                  </div>

                  <div data-cbbf-frag="scale_short"><?= $f( 'scale_short' ) ?></div>

                  <div class="delivery-chooser__body">
                    <? foreach ( $cart->get_delivery_methods() as $method_key => $method ): ?>
                      <div id="delivery-method-<?= esc_attr( $method_key ) ?>" class="delivery-chooser__wrap <?= $active_method === $method_key ? 'active' : '' ?>">
                        <? if ( $method['description'] ): ?>
                          <div class="delivery-chooser__text <?= $method_key === 'taxi' ? 'delivery-chooser__text--small' : '' ?>">
                            <?= wp_kses_post( $method['description'] ) ?>
                          </div>
                        <? endif ?>
                        <? if ( $method_key === 'taxi' ): ?>
                          <div class="delivery-chooser__row">
                            <div class="form-group">
                              <input name="delivery[taxi][]" id="field-delivery-address" type="text" autocomplete="street-address" class="form-control">
                              <label for="field-delivery-address" class="control-label">Адреса доставки (Київ)</label>
                            </div>
                          </div>
                        <? elseif ( $method_key === 'nv' ): ?>
                          <div class="cbnp" data-cbnp>
                            <div class="form-group cbnp-field">
                              <input id="cbnp-city" type="text" class="form-control" data-cbnp-city autocomplete="off" spellcheck="false">
                              <label for="cbnp-city" class="control-label">Місто</label>
                              <input type="hidden" name="np[city]">
                              <input type="hidden" name="np[city_ref]">
                            </div>
                            <div class="cbbf-segment cbbf-segment--small" role="radiogroup" aria-label="Тип пункту">
                              <label class="cbbf-segment__item">
                                <input type="radio" name="np[type]" value="branch" checked>
                                <span>Відділення</span>
                              </label>
                              <label class="cbbf-segment__item">
                                <input type="radio" name="np[type]" value="postomat">
                                <span>Поштомат</span>
                              </label>
                            </div>
                            <div class="form-group cbnp-field">
                              <input id="cbnp-wh" type="text" class="form-control" data-cbnp-wh autocomplete="off" spellcheck="false" disabled>
                              <label for="cbnp-wh" class="control-label" data-cbnp-wh-label>Відділення</label>
                              <input type="hidden" name="np[warehouse]">
                              <input type="hidden" name="np[warehouse_ref]">
                            </div>
                          </div>
                        <? endif ?>
                      </div>
                    <? endforeach ?>
                  </div>

                  <div class="delivery-chooser__comment">
                    <label class="control-label-static" for="order-notes">Ваш коментар (необов’язково)</label>
                    <textarea name="order_notes" id="order-notes" rows="3" placeholder="Ваш коментар…" class="form-control"></textarea>
                  </div>
                </div>
              </div>

              <div class="cart-box__widget chooser-required" data-cbbf-payment-box>
                <div class="cart-box__widget-title">Виберіть спосіб оплати</div>
                <div class="payment-chooser">
                  <div class="delivery-chooser__items">
                    <? if ( array_key_exists( 'liqpay', $gateways ) ): ?>
                      <div class="payment-chooser__item radio">
                        <label class="delivery-chooser__item-wrap">
                          <input type="radio" name="payment_method" value="liqpay" <?= count( $gateways ) === 1 ? 'checked' : '' ?>>
                          <span class="delivery-chooser__item-content">
                            <span class="delivery-chooser__item-label">Онлайн-оплата через LiqPay</span>
                            <span class="delivery-chooser__item-value">
                              <span class="payment-chooser__images cbbf-paylogos">
                                <img src="<?= esc_url( plugins_url( 'assets/img/liqpay.svg', CBBF_FILE ) ) ?>" alt="LiqPay" height="16">
                              </span>
                            </span>
                          </span>
                          <span class="payment-chooser__text cbbf-paytext">
                            Visa / Mastercard · Apple Pay · Google Pay · Privat24
                            <span class="cbbf-paylogos cbbf-paylogos--row" aria-hidden="true">
                              <img src="<?= \Roots\Sage\Assets\asset_path( 'images/visa-logo.svg' ) ?>" alt="" height="14">
                              <img src="<?= \Roots\Sage\Assets\asset_path( 'images/master-card-logo.svg' ) ?>" alt="" height="14">
                              <img src="<?= esc_url( plugins_url( 'assets/img/apple-pay.svg', CBBF_FILE ) ) ?>" alt="" height="16">
                              <img src="<?= esc_url( plugins_url( 'assets/img/google-pay.svg', CBBF_FILE ) ) ?>" alt="" height="16">
                            </span>
                          </span>
                        </label>
                      </div>
                    <? endif ?>

                    <? if ( array_key_exists( 'transfer', $gateways ) ): ?>
                      <div class="payment-chooser__item radio">
                        <label class="delivery-chooser__item-wrap">
                          <input type="radio" name="payment_method" value="transfer" <?= count( $gateways ) === 1 ? 'checked' : '' ?>>
                          <span class="delivery-chooser__item-content">
                            <span class="delivery-chooser__item-label">Оплата на рахунок</span>
                            <span class="delivery-chooser__item-value">
                              <span class="payment-chooser__images">
                                <img src="<?= \Roots\Sage\Assets\asset_path( 'images/privat24-logo.svg' ) ?>" alt="Приват24">
                              </span>
                            </span>
                          </span>
                          <span class="payment-chooser__text">Реквізити надішле менеджер після дзвінка</span>
                        </label>
                      </div>
                    <? endif ?>
                  </div>
                </div>
              </div>

              <div class="cbbf-coupon-box cbbf-mobile-only" data-cbbf-frag="coupon"><?= $f( 'coupon' ) ?></div>

              <div class="cart-box__widget cart-box__widget_simple cbbf-summary-box cbbf-mobile-only" data-cbbf-frag="summary"><?= $f( 'summary' ) ?></div>

              <div class="cbbf-confirm">
                <div class="cbbf-errors" data-cbbf-errors role="alert" hidden></div>
                <button type="submit" class="btn btn-default cbbf-confirm__btn" data-cbbf-submit>Підтвердити замовлення</button>
                <p class="cbbf-confirm__note">Натискаючи кнопку, ви погоджуєтесь з <a target="_blank" href="<?= get_the_permalink( 940 ) ?>">умовами оферти</a>.</p>
              </div>
            </div>
          </form>

          <div class="cbbf-sticky" data-cbbf-sticky>
            <div data-cbbf-frag="sticky"><?= $f( 'sticky' ) ?></div>
            <button type="button" class="btn btn-default cbbf-sticky__btn" data-cbbf-sticky-btn>Оформити замовлення</button>
          </div>
        <? else: ?>
          <p class="styled-heding__title">Ваш кошик порожній :(</p>
        <? endif ?>
      </div>
    </div>
  </div>
</div>

<?php
// GA4: begin_checkout — перший крок оформлення, якщо в кошику є товари
if ( ! empty( $cart->get_items() ) && function_exists( 'akweb_ga4_begin_checkout_script' ) ) {
  akweb_ga4_begin_checkout_script();
}
// GA4: add_payment_info — вибір способу оплати
if ( ! empty( $cart->get_items() ) && function_exists( 'akweb_ga4_add_payment_info_script' ) ) {
  akweb_ga4_add_payment_info_script();
}
// GA4: інфа з бекенду ecommerce (items + value) у JS
if ( function_exists( 'akweb_ga4_checkout_ecommerce_inline' ) ) {
  akweb_ga4_checkout_ecommerce_inline();
}
// GA4: purchase helper + тригер по модалкам
if ( function_exists( 'akweb_ga4_purchase_helper_script' ) ) {
  akweb_ga4_purchase_helper_script();
}
if ( function_exists( 'akweb_ga4_purchase_on_modal_script' ) ) {
  akweb_ga4_purchase_on_modal_script();
}
// «Вас також зацікавить» прибрано (ТЗ, розділ 3): його роль виконує «Обрати ще щось?».
