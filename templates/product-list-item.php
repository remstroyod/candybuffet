<?php
use SimpleBasket\Helper;
use SimpleBasket\Product;

global $post;
?>

<a href="<?php the_permalink(); ?>"
   class="product-card"
   <?php
   if ( function_exists( 'akweb_ga4_select_item_attrs' )
        && isset( $post->product )
        && $post->product instanceof Product ) {
       akweb_ga4_select_item_attrs( $post->product );
   }
   ?>
>
    <?php if (is_tax()): ?>
	    <?php foreach (wp_get_post_terms(get_the_ID(), 'product_tag') as $term):  ?>
            <div class="products-box__list-item__tag"><?= $term->name ?></div>
	    <?php endforeach?>
    <?php endif ?>

  <? if ($post->product->on_sale()): ?>
    <span class="product-card__badge">Акція</span>
  <? endif ?>

  <span class="product-card__image">
        <? the_post_thumbnail('product-height') ?>
     </span>

  <span class="product-card__title"><? the_title() ?></span>
  <? if (get_field('allow', $post->product)): ?>
    <span class="product-card__price">
        <span class="old-price">
            <? if ($post->product->on_sale()): ?>
                <span class="old-price" style="text-decoration: line-through;color:#000;font-size: 13px;"><?= Helper::format_currency('', $post->product->get_price('regular')) ?></span>
		    <? endif ?>
        </span> <? $post->product->display_price() ?></span>
  <? else: ?>
  <span class="product-cards">
    <span class=" btn-disabled card__order-button">
      немає в наявності
    </span>
  </span>
  <? endif ?>
</a>
<?php if (get_field('allow', $post->product)): ?>
    <?php get_template_part('templates/product', 'list-buy-btn') ?>
<?php endif; ?>