<?php
/**
 * Template Name: О нас
 */
?>
<? get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
    <div class="page-wrap page-wrap_wide">
        <div class="page-wrap__content">
            <div class="content-box content-box_bottom-offset">
                <? get_template_part('templates/page', 'top') ?>
            </div>
            <section class="reviews-carousel">
                <div class="reviews-carousel__header">
                    <div class="styled-heding">
                        <div class="styled-heding__header-inner">
                            <div class="styled-heding__label">Customer reviews</div>
                            <h2 class="styled-heding__title">Відгуки клієнтів</h2>
                        </div>
                    </div>
                </div>
                <div class="reviews-carousel__list">
                    <? while (have_rows('отзывы')): the_row() ?>
                    <div class="reviews-carousel__list-item">
                        <div class="review-card">
                            <div class="review-card__image">
                                <?= wp_get_attachment_image(get_sub_field('аватар'), 'thumbnail', false, ['class' => 'review-card__image-i']) ?>
                            </div>
                            <div class="review-card__title"><? the_sub_field('имя') ?></div>
                            <div class="review-card__position"><? the_sub_field('должность') ?></div>
                            <div class="review-card__text"><? the_sub_field('текст') ?></div>
                        </div>
                    </div>
                    <? endwhile; ?>
                </div>

                <div class="reviews-carousel__read-more mt60 text-center">
                    <a href="https://www.facebook.com/pg/CandyBuffet.ua/reviews/" rel="nofollow" target="_blank" class="btn btn-bordered">Читати відгуки на Facebook</a>
                </div>
            </section>
        </div>
    </div>
</div>
