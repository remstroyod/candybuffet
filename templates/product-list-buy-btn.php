<?php
global $post;

$exist_in_cart = 0; 
$class = $exist_in_cart ? 'disabled' : '';
$button_text = $exist_in_cart ? 'Додано у кошик' : 'В кошик';
?>

<form method="post" data-id="<?php the_ID() ?>" class="simplebasket__form">
    <div class="card__order" style="justify-content: center; margin-top: 8px;">
        <a href="#" data-product-id="<?php echo get_the_ID(); ?>" class="simplebasket_buy_button btn btn-default card__order-button <?php echo $class; ?>"><?php echo $button_text; ?></a>
    </div>
</form>

<?php
// GA4: add_to_cart для цього товару
if ( isset( $post->product ) && $post->product instanceof \SimpleBasket\Product ) {
    akweb_ga4_add_to_cart_script( $post->product );
}
?>