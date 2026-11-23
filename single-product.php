<?php

use SimpleBasket\Product;

the_post();

get_template_part('templates/header', 'breadcrumbs');
$images = get_field('изображения');
?>

<div class="page-wrap page-wrap_wide">
  <div class="container">
    <div class="page-wrap__content">
      <div class="page-wrap__product-nav">
        <?php
        $next_product = get_next_prev_product();
        $prev_product = get_next_prev_product(true);

        if ($next_product || $prev_product): ?>
          <div class="product-nav">
            <div class="product-nav__image">
              <?php $thumb_post_id = $next_product ? $next_product->ID : $prev_product->ID ?>
              <?= get_the_post_thumbnail($thumb_post_id, 'product-nav', ['class' => 'product-nav__image-i']) ?>
            </div>
            <?php if ($next_product): ?>
              <a href="<?= get_permalink($next_product) ?>"
                 class="product-nav__button product-nav__button_next"></a>
            <?php endif ?>
            <?php if ($prev_product): ?>
              <a href="<?= get_permalink($prev_product) ?>"
                 class="product-nav__button product-nav__button_prev"></a>
            <?php endif ?>
          </div>
        <?php endif ?>
      </div>
      <div class="card">
        <div class="card__header card__header_mobile">
          <div class="card__title">
            <?php the_title() ?>

            <?php if ($post->product->on_sale()): ?>
              <span class="onsale-title">акція</span>
            <?php endif ?>
          </div>
        </div>

        <div class="card__image">
          <?php if (is_array($images)): ?>
            <div class="preview">
              <div class="preview__thumbs">
                <?php foreach ($images as $i => $img): ?>
                  <div data-target=".preview__image_<?= $i ?>"
                       class="<?= $i === 0 ? 'active' : '' ?> preview__thumbs-item">
                    <?= wp_get_attachment_image($img['ID']) ?>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="preview__content">
                <?php foreach ($images as $i => $img): ?>
                  <div data-zoom="<?= $img['url'] ?>"
                       class="<?= $i === 0 ? 'active' : '' ?> preview__image preview__image_<?= $i ?>">
                    <div class="preview__image-inner">
                      <?= wp_get_attachment_image($img['ID'], 'big-square') ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif ?>

          <?php do_action('simplebasket/single/after_preview_image') ?>
        </div>
        <div class="card__content">
          <form method="post" data-id="<?php the_ID() ?>" class="simplebasket__form">
            <div class="card__header card__header_main">
              <h1 class="card__title"><?php the_title() ?>
                <?php if ($post->product->on_sale()): ?>
                      <span class="onsale-title">акція</span>
                <?php endif ?>
              </h1>
              <?php if( Product::get_parent_product_category('slug') === 'cakes'): ?>
                 <div class="card__label"><?php $post->product->display_price() ?></div>
              <?php endif ?>
            </div>

            <div class="card__data <?= is_object_in_term(get_the_ID(), 'product_category', 'cakes') ? 'single_card_data_class' : '' ?>">
              <?php do_action('simplebasket/single/start_attributes') ?>

              <?php // do_action( 'simplebasket/single/attributes/' . Product::get_parent_product_category('slug')) ?>
              <?php do_action( 'simplebasket/single/attributes/candies' ) ?>

              <?php do_action('simplebasket/single/end_attributes') ?>
            </div>

            <?php do_action('simplebasket/single/before_buy_btn') ?>

            <?php do_action('simplebasket/single/buy_btn') ?>

            <?php do_action('simplebasket/single/after_buy_btn') ?>
          </form>
        </div>
      </div>

      <?php if($text = get_the_content()): ?>
        <section class="info-content">
          <input type="checkbox" id="info-content__readmore-input">
          <div class="info-content__text">
            <?= apply_filters('the_content', $text) ?>
          </div>
          <label for="info-content__readmore-input" class="info-content__readmore">Весь текст</label>
        </section>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
// GA4: view_item
global $post;

if ( isset( $post->product ) && $post->product instanceof \SimpleBasket\Product ) {
    akweb_ga4_view_item( $post->product );
}
?>

<?php do_action('simplebasket/related_products') ?>
