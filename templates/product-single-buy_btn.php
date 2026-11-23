<div class="card__order">
    <?

    use SimpleBasket\Helper;

    $exist_in_cart = 0 && SimpleBasket\Cart::get_instance()->has_product(get_the_ID());
    $class = $exist_in_cart ? 'disabled' : '';
    $button_text = $exist_in_cart ? 'Додано у кошик' : 'В кошик';
    if (get_field('allow')) {
      printf(
        '<a href="#" data-product-id="%d" class="simplebasket_buy_button btn btn-default card__order-button %s">%s</a>',
        get_the_ID(),
        $class,
        $button_text
      );
    } else { ?>
      <a href="#" disabled="disabled" class="simplebasket_buy_button btn btn-disabled card__order-button">немає в наявності</a>
    <? } ?>
	<div class="card__order-price simplebasket__product_price">
        <? if ($post->product->on_sale()): ?>
        <span class="old-price"><?= Helper::format_currency('', $post->product->get_price('regular')) ?></span>
        <? endif ?>

        <span class="amount">
            <?= Helper::number_format(single_cakes_price_modify($post->product->get_price('lowest')), 2) ?>
        </span> <?= Helper::format_currency() ?>
    </div>
</div>

<?php
// GA4: add_to_cart для цього товару
if ( isset( $post->product ) && $post->product instanceof \SimpleBasket\Product ) {
    akweb_ga4_add_to_cart_script( $post->product );
}
?>