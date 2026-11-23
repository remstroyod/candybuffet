<?php
use SimpleBasket\Product;
$ga4_products = [];
?>

<div class="products-box__list">
	<? while(have_posts()): the_post(); ?>
		<div class="products-box__list-item">
            <?php 
			global $post;

            if ( isset( $post->product ) && $post->product instanceof Product ) {
                $ga4_products[] = $post->product;
            }
			get_template_part('templates/product', 'list-item') ?>
		</div>
	<?php endwhile ?>
</div>
<div class="products-box__footer">
	<div class="products-box__more">
		<div class="products-box__more-icon"></div>
	</div>
</div>

<?php
if ( ! empty( $ga4_products ) && function_exists( 'akweb_ga4_view_item_list' ) ) {

    $list_id   = null;
    $list_name = null;

    if ( is_tax( 'product_category' ) ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $list_id   = 'cat_' . $term->term_id;
            $list_name = $term->name;
        }
    }

    akweb_ga4_view_item_list( $ga4_products, $list_id, $list_name );
}

// GA4: select_item – JS-обробник кліків
if ( function_exists( 'akweb_ga4_select_item_script' ) ) {
    akweb_ga4_select_item_script();
}
?>