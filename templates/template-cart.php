<?
/**
 * Template Name: Cart
 */

use SimpleBasket\Cart;
use SimpleBasket\Helper;
use SimpleBasket\Product;

$cart = Cart::get_instance();

$packs = [];
if (function_exists('get_field')) {
    $group = get_field('paket_z_foto', 'option'); 
    if (!empty($group['pakety']) && is_array($group['pakety'])) {
		$packsTitle  = isset($group['title'])  ? trim((string)$group['title']) : '';
        foreach ($group['pakety'] as $row) {
            $name  = isset($row['name'])  ? trim((string)$row['name']) : '';
            $price = isset($row['price']) ? (float)$row['price'] : 0.0;
            if ($name !== '' && $price >= 0) {
                $packs[] = ['label' => $name, 'price' => $price];
            }
        }
    }
}
?>
<? get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
  <div class="page-wrap">
    <div class="page-wrap__content">
      <div class="page-wrap__header">
        <? get_template_part('templates/part', 'styled-heding') ?>
      </div>
      <div class="cart-box">
        <? if (Cart::get_instance()->get_items()): ?>
          <div class="validation-wrap">
            <form novalidate="novalidate" class="simplebox__checkout_form cart-box__inner">
              <input type="hidden" name="action" value="simplebasket_process_checkout">
              <input type="hidden" name="nonce" value="<?= wp_create_nonce() ?>">

              <div class="cart-box__content">
                <div class="cart-box__widgets">
                  <div class="cart-box__widget">
                    <div class="cart-box__products">
                      <?

                      $allowed_delivery = array_keys($cart->get_delivery_methods());

                      foreach ($cart->get_items() as $id => $item):
                        $post = get_post($id);
                        setup_postdata($post);

                        $product_delivery = get_field('dostavka');

                        $allowed_delivery = array_intersect($allowed_delivery, $product_delivery);

                        $current_type = Product::get_parent_product_category('slug');
                        ?>
                        <div data-id="<?= $id ?>" class="cart-box__products-item">
                          <div class="cart-item">
                            <div class="cart-item__wrap">
                              <div class="cart-item__remove simplebasket-action-remove"></div>
                              <div class="cart-item__image">
                                <a href="<? the_permalink($id) ?>"><?= get_the_post_thumbnail($id, 'thumbnail', ['class' => 'cart-item__image-i']) ?></a>
                              </div>
                              <div class="cart-item__weight">
                                <? if (1 || $current_type === 'candies' || $current_type === 'tistechko'): ?>
                                  <div class="cart-item__weight-label">Кільк. коробок</div>
                                  <?
                                  $weights = range(1, 10);
                                  if (is_array($weights)) { ?>
                                    <select class="select2-simple simplebasket__qty"
                                            tabindex="-1" aria-hidden="true">
                                      <? foreach ($weights as $val) { ?>
                                        <option <?= $item[$cart::QUO] == $val ? 'selected' : '' ?>
                                          value="<?= $val ?>"><?= $val ?></option>
                                      <? } ?>
                                    </select>
                                  <? } elseif (is_string($weights)) {
                                    echo $weights;
                                  } ?>
                                <? else: ?>
                                  <div class="cart-item__weight-label">вага, кг
                                  </div>
                                  <?
                                  $weights = get_weight($id);
                                  if (is_array($weights)) { ?>
                                    <select class="select2-simple simplebasket__qty"
                                            tabindex="-1" aria-hidden="true">
                                      <? foreach ($weights as $val) {
                                        $val_normalize = get_product_price_by_weight($val);
                                        ?>

                                        <option <?= $item[$cart::QUO] == $val_normalize ? 'selected' : '' ?>
                                          value="<?= $val ?>"><?= $val_normalize ?></option>

                                      <? } ?>
                                    </select>
                                  <? } elseif (is_string($weights)) {
                                    echo $weights;
                                  } ?>
                                <? endif ?>
                              </div>
                              <div class="cart-item__price simplebasket__product_price">
                                <span class="cart-item__price-value amount"><?= $cart->getPrice($item) ?></span>
                                <span class="cart-item__price-currency"><?= Helper::format_currency() ?></span>
                              </div>
                            </div>
                            <a href="<? the_permalink($id) ?>"
                               class="cart-item__title"><?= $item[$cart::TITLE] ?></a>
                          </div>
                        </div>
                      <? endforeach;wp_reset_postdata() ?>
                    </div>
                  </div>				  
					<div class="cart-box__widget" id="cart-photo-packs">
					  <div class="cart-box__widget-title"><?=$packsTitle?></div> 
						<div class="delivery-chooser">
							<div class="delivery-chooser__items">
								<?php foreach ($packs as $i => $p): ?>
									<div class="delivery-chooser__item radio">
									  <label class="delivery-chooser__item-wrap">
										<input type="radio"
											   name="extras[photo_pack][key]"
											   class="js-photo-pack"
											   value="<?php echo (int)$i; ?>"
											   data-label="<?php echo esc_attr($p['label']); ?>"
											   data-price="<?php echo (int)$p['price']; ?>">

										<span class="delivery-chooser__item-content">
											<span class="delivery-chooser__item-label"><?php echo esc_html($p['label']); ?></span>
											<span class="delivery-chooser__item-value"><?php echo (int)$p['price']; ?> <?php echo \SimpleBasket\Helper::format_currency(); ?></span>
										</span>
									  </label>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<!-- hidden для бекенду -->
						<input type="hidden" name="extras[photo_pack][label]" id="photo-pack-label">
						<input type="hidden" name="extras[photo_pack][price]" id="photo-pack-price">
					</div>
                  <div class="cart-box__widget cart-box__widget_simple">
                    <div class="cart-box__total">
                      <div class="cart-box__total-label">Разом</div>
                      <div class="cart-box__total-price">
                        <span class="cart-box__total-price-value simplebasket__cart-total"><?= $cart->getTotal() ?></span>
                        <span class="cart-box__total-price-currency"><?= Helper::format_currency() ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="cart-box__buttons">
                    <div class="cart-box__buttons-item">
                      <a href="<?= get_post_type_archive_link(Product::get_post_type()) ?>"
                         class="btn btn-bordered cart-box__button">Продовжити покупки</a>
                    </div>
                    <div class="cart-box__buttons-item is-mobile-hide">
                      <button type="submit" class="simplebasket__checkout btn btn-default cart-box__button">
                        Оплатити
                      </button>
                    </div>
                  </div>
                  <div class="cart-box__list">
                    <div>
                      <div class="cart-box__list-item">
                        <a target="_blank" href="<?= get_the_permalink(81) ?>" class="cart-box__list-link">Більше про
                          оплату та доставку</a>
                      </div>
                    </div>
                    <div>
                      <div class="cart-box__list-item">
                        <a target="_blank" href="<?= get_the_permalink(940) ?>" class="cart-box__list-link">Договіг
                          оферти</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cart-box__sidebar">
                <div class="cart-box__widget">
                  <div class="cart-box__widget-title">Контактні дані</div>
                  <div class="cart-box__contacts">
                    <div class="form-group">
                      <input name="billing[name]" id="field-question-name" type="text"
                             aria-required class="form-control">
                      <label for="field-cart-contact-name" class="control-label">ПІБ</label>
                    </div>
                    <div class="form-group">
                      <input name="billing[phone]" id="field-cart-contact-phone" type="tel"
                             aria-required data-mask="+38(099) 999 99 99"
                             class="form-control">
                      <label for="field-cart-contact-phone"
                             class="control-label">Телефон</label>
                    </div>
                    <div class="form-group">
                      <input name="billing[email]" id="field-cart-contact-email" type="email"
                             aria-required class="form-control">
                      <label for="field-cart-contact-email"
                             class="control-label">Email</label>
                    </div>
                  </div>
                </div>
				<div class="cart-box__widget" id="cart-gift">
					<div class="delivery-chooser">
						<div class="delivery-chooser__items">
							<div class="delivery-chooser__item radio">
							  <label class="delivery-chooser__item-wrap">
								<input type="checkbox" id="gift-toggle" name="gift[enabled]" value="1">

								<span class="delivery-chooser__item-content">
									<span class="delivery-chooser__item-label">Оформити як подарунок</span>
									<span class="delivery-chooser__item-value"></span>
								</span>
							  </label>
							</div>
						</div>
					</div>
					<div id="gift-fields" style="display:none;">
						<div class="form-group">
						  <input name="gift[to_name]" type="text" class="form-control" id="gift-to-name">
						  <label class="control-label" for="gift-to-name">ПІБ отримувача</label>
						</div>
						<div class="form-group">
						  <input name="gift[to_phone]" type="tel" class="form-control" id="gift-to-phone" data-mask="+38(099) 999 99 99">
						  <label class="control-label" for="gift-to-phone">Телефон отримувача</label>
						</div>
					</div>
				</div>
                <div class="cart-box__widget chooser-required">
                  <div class="cart-box__widget-title">Виберіть спосіб доставки</div>
                  <div class="delivery-chooser">
                    <div class="delivery-chooser__items">
                      <?php
                      $active_method = $cart->get_current_delivery_methods();
                      foreach ($cart->get_delivery_methods() as $method_key => $method):
                        ?>

                        <div data-target="#delivery-method-<?= $method_key ?>" class="delivery-chooser__item radio">
                          <label class="delivery-chooser__item-wrap">
                            <input <?= !in_array($method_key, $allowed_delivery) ? 'disabled' : '' ?>
                              type="radio" name="delivery-method" value="<?= $method_key ?>"
                              data-price="<?= (int)preg_replace('/[^0-9]/', '', $method['price']) ?>"
                              <?= $active_method === $method_key ? 'checked_' : '' ?>>

                            <span class="delivery-chooser__item-content">
                                <span class="delivery-chooser__item-label"><?= $method['title'] ?></span>
                                <span class="delivery-chooser__item-value"><?= $method['price'] ?></span>
                            </span>
                          </label>
                        </div>
                      <? endforeach ?>
                    </div>

                    <div class="delivery-chooser__body">
                      <? foreach (Cart::get_instance()->get_delivery_methods() as $method_key => $method): ?>
                      <div id="delivery-method-<?= $method_key ?>" class="delivery-chooser__wrap <?= $active_method === $method_key ? 'active__' : '' ?>">
                        <div class="delivery-chooser__text <?= $method_key == 'taxi' ? 'delivery-chooser__text--small' : '' ?>">
                          <?= $method['description'] ?>
                        </div>
                        <div class="delivery-chooser__row">
                          <? if($method_key === 'taxi'): ?>
                            <div class="form-group">
                              <input name="delivery[<?= $method_key ?>][]" id="field-delivery-address" type="text"
                                     class="form-control required-field" aria-required="true">
                              <label for="field-delivery-address" class="control-label">Адреса доставки</label>
                            </div>
                          <? elseif($method_key === 'nv'): ?>
                            <div class="delivery-chooser__col">
                              <div class="form-group">
                                <input name="delivery[<?= $method_key ?>][]" id="field-delivery-city"
                                       type="text" class="form-control required-field" aria-required="true">
                                <label for="field-delivery-city" class="control-label">Місто</label>
                              </div>
                            </div>

                            <div class="delivery-chooser__col">
                              <div class="form-group">
                                <input name="delivery[<?= $method_key ?>][]"
                                       id="field-delivery-department" type="number"
                                       class="form-control required-field" aria-required="true">
                                <label for="field-delivery-department"
                                       class="control-label">№ відділення</label>
                              </div>
                            </div>
                          <? endif; ?>
                        </div>

                        <? if($method_key === 'nv'): ?>
                        <div class="form-group">
                          <input name="delivery[<?= $method_key ?>][]" id="field-delivery-address" class="form-control">
                          <label for="field-delivery-address" class="control-label">Адресна доставка (необов’язково)</label>
                        </div>
                        <? endif ?>
                      </div>

                      <? endforeach; ?>
                      <? //do_action('simplebasket/checkout/section_shipping/chooser'); ?>
                    </div>

                    <div class="delivery-chooser__comment">
                      <label class="control-label-static">Лишіть свій коментар
                        (необов’язково)</label>
                      <textarea name="order_notes" rows="3" placeholder="Ваш коментар..."
                                class="form-control"></textarea>
                    </div>

                    <div style="display:none;" class="order-agree radio">
                      <label for="agree" class="delivery-chooser__item-wrap">
                        <input type="checkbox" id="agree">

                        <span class="delivery-chooser__item-content">
                                <span class="delivery-chooser__item-label">* Я погоджуююсь з умовами магазину</span>
                            </span>
                      </label>
                    </div>

                  </div>
                </div>
				<div class="cart-box__widget chooser-required">
                  <div class="cart-box__widget-title">Виберіть спосіб Оплати</div>
                  <div class="payment-chooser">
                    <div class="delivery-chooser__items">
                      <?php $gateways = apply_filters('simplebasket/gateways', []) ?>

                      <?php if (array_key_exists('liqpay', $gateways)): ?>
                        <div class="payment-chooser__item radio">
                        <label class="delivery-chooser__item-wrap">
                          <input type="radio" name="payment_method" value="liqpay">
                          <span class="delivery-chooser__item-content">
                              <span class="dea livery-chooser__item-label">Оплата онлайн картами Visa та Mastercard <br> через систему Liq Pay (комісія відсутня)</span>
                              <span class="delivery-chooser__item-value">
                                  <span class="payment-chooser__images">
                                      <img src="<?= \Roots\Sage\Assets\asset_path('images/visa-logo.svg') ?>" alt="visa">
                                      <img src="<?= \Roots\Sage\Assets\asset_path('images/master-card-logo.svg') ?>" alt="maser card">
                                  </span>
                              </span>
                          </span>
                          <span class="payment-chooser__text">* Перехід на сторінку з миттєвою оплатою</span>
                        </label>
                      </div>
                      <? endif ?>

                      <?php if (array_key_exists('transfer', $gateways)): ?>
                        <div class="payment-chooser__item radio radio-required">
                        <label class="delivery-chooser__item-wrap">
                          <input type="radio" name="payment_method" class="" value="transfer" <?= count($gateways)===1?'checked_':'' ?>>
                          <span class="delivery-chooser__item-content">
                                                          <span class="delivery-chooser__item-label">Оплата на карту Приватбанка</span>
                                                          <span class="delivery-chooser__item-value">
                                                              <span class="payment-chooser__images">
                                                                  <img src="<?= \Roots\Sage\Assets\asset_path('images/privat24-logo.svg') ?>" alt="privat24">
                                                              </span>
                                                          </span>
                                                      </span>
                          <span class="payment-chooser__text">* Після дзвінка менеджера за реквізитами</span>
                        </label>
                      </div>
                      <? endif ?>

                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        <? else: ?>
          <p class="styled-heding__title">Ваш кошик порожній :( </p>
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
// GA4: remove_from_cart на сторінці кошика
if ( function_exists( 'akweb_ga4_remove_from_cart_script' ) ) {
    akweb_ga4_remove_from_cart_script();
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
?>


<script>
(function(){
  // ---- helpers ----
  function qs(sel, root){ return (root||document).querySelector(sel); }
  function qsa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }
  function parseAmount(txt){
    if (txt === null || txt === undefined) return 0;
    var s = (''+txt).replace(/\u00a0/g,' ').replace(/[^0-9,.\-]/g,'').replace(',', '.');
    var n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  }
  function fmtInt(n){ return String(Math.round(n)); }

  // ---- DOM refs ----
  var packRadios     = qsa('#cart-photo-packs .js-photo-pack');
  var packLabelInput = qs('#photo-pack-label');
  var packPriceInput = qs('#photo-pack-price');

  // батько тоталу (стабільний контейнер) - щоб переживати ремоунти
  var initialTotal = qs('.simplebasket__cart-total');
  if (!initialTotal) return;
  var totalParent  = initialTotal.parentNode;

  // ---- state ----
  var packPrice     = 0;
  var deliveryPrice = 0;

  // ---- завжди беремо актуальний тотал з DOM ----
  function getTotalEl(){ return qs('.simplebasket__cart-total'); }

  // ---- створюємо/відновлюємо оверлей поверх тоталу ----
  function ensureOverlay(){
    var el = getTotalEl();
    if (!el) return null;

    // якщо оверлей живий і прив'язаний - все ок
    if (el._akOverlay && document.body.contains(el._akOverlay)) return el;

    // інакше - створюємо обгортку та оверлей заново
    var wrap = document.createElement('span');
    wrap.className = 'ak-total-wrap';
    wrap.style.position = 'relative';
    wrap.style.display  = 'inline-block';

    el.parentNode.insertBefore(wrap, el);
    wrap.appendChild(el);

    var st = window.getComputedStyle(el);
    var overlay = document.createElement('span');
    overlay.className = 'ak-total-overlay';
    overlay.style.position = 'absolute';
    overlay.style.inset = '0';
    overlay.style.pointerEvents = 'none';
    overlay.style.whiteSpace = 'nowrap';
    overlay.style.font = st.font || '';
    overlay.style.lineHeight = st.lineHeight || '';
    overlay.style.letterSpacing = st.letterSpacing || '';
    overlay.style.textAlign = st.textAlign || '';
    overlay.style.color = st.color || '';
    wrap.appendChild(overlay);

    el.style.opacity = '0';
    el._akOverlay = overlay;
    return el;
  }

  function readBase(){
    var el = getTotalEl();
    return el ? parseAmount(el.textContent) : 0;
  }

  function paint(val){
    var el = ensureOverlay();
    if (!el || !el._akOverlay) return;
    var out = fmtInt(val);
    if (el._akOverlay.textContent !== out){
      el._akOverlay.textContent = out;
    }
  }

  function applyOnce(){
    var base = readBase();
    paint(base + (packPrice||0) + (deliveryPrice||0));
  }

  // ===== фото-пакети =====
  function selectedPack(){
    for (var i=0;i<packRadios.length;i++) if (packRadios[i].checked) return packRadios[i];
    return null;
  }
  function updatePackFromUI(){
    var sel = selectedPack();
    if (sel){
      packPrice = parseAmount(sel.getAttribute('data-price'));
      if (packLabelInput) packLabelInput.value = sel.getAttribute('data-label') || '';
      if (packPriceInput) packPriceInput.value = sel.getAttribute('data-price') || '';
    } else {
      packPrice = 0;
      if (packLabelInput) packLabelInput.value = '';
      if (packPriceInput) packPriceInput.value = '';
    }
  }
  packRadios.forEach(function(r){
    r.addEventListener('change', function(){
      updatePackFromUI();
      applyOnce();
    });
  });

  // ===== доставка (плагін не додає її в total - рахуємо самі) =====
  function updateDeliveryFromUI(){
    var sel = qs('input[name="delivery-method"]:checked');
    deliveryPrice = sel ? parseAmount(sel.getAttribute('data-price')) : 0;
  }
  // делегація - переживає ремоунти
  document.addEventListener('change', function(e){
    var t = e.target;
    if (t && t.name === 'delivery-method'){
      updateDeliveryFromUI();
      applyOnce();
    }
  });

  // ===== MutationObserver на батька тоталу =====
  // ловить і зміну тексту, і ремоунт самого <span>
  new MutationObserver(applyOnce).observe(totalParent, {
    characterData: true,
    childList: true,
    subtree: true
  });

  // ===== подарунок: вимкнення полів + зміна type (маску НЕ чіпаємо) =====
  var giftToggle      = qs('#gift-toggle');
  var giftFieldsWrap  = qs('#gift-fields');
  var giftInputs      = qsa('#gift-fields input, #gift-fields select, #gift-fields textarea');

  function cacheOriginal(el){
    if (!el.dataset.origName)      el.dataset.origName      = el.name || '';
    if (!el.dataset.origType)      el.dataset.origType      = el.getAttribute('type') || '';
    if (!el.dataset.origPattern)   el.dataset.origPattern   = el.getAttribute('pattern') || '';
    if (!el.dataset.origMinlen)    el.dataset.origMinlen    = el.getAttribute('minlength') || '';
    if (!el.dataset.origMaxlen)    el.dataset.origMaxlen    = el.getAttribute('maxlength') || '';
    if (!el.dataset.origRequired)  el.dataset.origRequired  = el.hasAttribute('required') ? '1' : '0';
    if (!el.dataset.origReqClass)  el.dataset.origReqClass  = el.classList.contains('required-field') ? '1' : '0';
    if (!el.dataset.origAriaReq)   el.dataset.origAriaReq   = el.getAttribute('aria-required') || '';
  }
  function clearFieldErrors(el){
    var fg = el.closest('.form-group');
    if (fg){
      fg.classList.remove('has-error','is-invalid','error','invalid');
      var err = fg.querySelector('.error, .validation-error, .field-error');
      if (err) err.remove();
    }
  }
  function disableGiftInputs(){
    if (giftFieldsWrap) giftFieldsWrap.style.display = 'none';
    giftInputs.forEach(function(el){
      cacheOriginal(el);
      if (el.dataset.origPattern) el.removeAttribute('pattern');
      if (el.dataset.origMinlen)  el.removeAttribute('minlength');
      if (el.dataset.origMaxlen)  el.removeAttribute('maxlength');
      if (el.dataset.origAriaReq) el.removeAttribute('aria-required');
      el.removeAttribute('required');
      el.classList.remove('required-field');
      if (el.dataset.origType && el.dataset.origType.toLowerCase() !== 'text') {
        try { el.setAttribute('type','text'); } catch(e){}
      }
      el.disabled = true;
      if (el.name) el.removeAttribute('name');
      clearFieldErrors(el);
    });
  }
  function enableGiftInputs(){
    if (giftFieldsWrap) giftFieldsWrap.style.display = 'block';
    giftInputs.forEach(function(el){
      if (el.dataset.origName) el.name = el.dataset.origName;
      el.disabled = false;
      if (el.dataset.origType)     { try { el.setAttribute('type', el.dataset.origType); } catch(e){} }
      if (el.dataset.origPattern)  el.setAttribute('pattern', el.dataset.origPattern);
      if (el.dataset.origMinlen)   el.setAttribute('minlength', el.dataset.origMinlen);
      if (el.dataset.origMaxlen)   el.setAttribute('maxlength', el.dataset.origMaxlen);
      if (el.dataset.origAriaReq)  el.setAttribute('aria-required', el.dataset.origAriaReq);
      if (el.dataset.origRequired === '1') el.setAttribute('required','');
      if (el.dataset.origReqClass === '1') el.classList.add('required-field');
    });
  }
  function setGiftState(enabled){
    if (enabled) enableGiftInputs(); else disableGiftInputs();
  }
  if (giftToggle){
    setGiftState(!!giftToggle.checked);
    giftToggle.addEventListener('change', function(){ setGiftState(this.checked); });
  }

  // ---- стартовий рендер ----
  ensureOverlay();
  updatePackFromUI();
  updateDeliveryFromUI();
  applyOnce();

  function syncQtySelects(){
      qsa('.simplebasket__qty').forEach(function(sel){
          var opt = sel.querySelector('option[selected]');
          if (opt && sel.value !== opt.value){
              sel.value = opt.value;
              if (window.jQuery) jQuery(sel).trigger('change.select2');
          }
      });
  }
  window.addEventListener('pageshow', syncQtySelects);
  syncQtySelects();


})();
</script>

<? do_action('simplebasket/related_products'); ?>
