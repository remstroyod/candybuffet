<?php
/**
 * Template Name: Доставка
 */
?>
<? get_template_part('templates/page', 'left-title') ?>
<div class="container">
    <div class="page-wrap">
        <div class="page-wrap__content">
            <div class="delivery-box">
                <? while(have_rows('блоки_инфо')): the_row(); ?>
                <section class="delivery-box__section">
                    <div class="delivery-box__content">
                        <h2 class="delivery-box__title"><? the_sub_field('заголовок') ?></h2>
                        <div class="delivery-box__text">
	                        <? the_sub_field('контент') ?>
                        </div>
                    </div>
                    <div class="delivery-box__aside">
                        <div class="delivery-box__list">
	                        <? while(have_rows('иконки')): the_row(); ?>
                                <div class="delivery-box__list-item">
                                    <div class="delivery-box__list-image">
                                        <img src="<? the_sub_field('иконка') ?>" class="delivery-box__list-image-i" />
                                    </div>
                                    <div class="delivery-box__list-label"><? the_sub_field('описание') ?></div>
                                </div>
	                        <? endwhile ?>
                        </div>
                    </div>
                </section>
                <? endwhile; ?>
            </div>
        </div>
    </div>
</div>