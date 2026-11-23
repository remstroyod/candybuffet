<?php
/**
 * Template Name: Вкусы
 */
?>

<? get_template_part('templates/header', 'breadcrumbs') ?>

<div class="container">
    <div class="page-wrap page-wrap_simple">
        <div class="page-wrap__content">
            <div class="page-wrap__header">
	            <? get_template_part('templates/part', 'styled-heding') ?>
                <div class="page-wrap__header-wrap">
                    <div class="page-wrap__header-text"><? the_content() ?></div>
                    <div class="page-wrap__header-aside">
                        <a href="<?= get_term_link('cakes', 'product_category') ?>" class="btn btn-default page-wrap__header-button"><? the_field('текст_кнопки') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?
$data = [];
foreach (get_terms(['taxonomy' => 'product_taste', 'hide_empty' => 0]) as $term) {
  $data[get_field('вкусы_тип', $term)][] = [
    'thumb_id' => get_field('изображение', $term),
    'title' => $term->name,
    'description' => $term->description,
  ];
}
?>

<div class="tiles-box">
    <div class="container">
        <div class="tiles-box__inner">
            <div class="tiles-box__nav">
                <ul>
                    <?
                    $key = 0;
                    foreach ($data as $taste => $item): ?>
                        <li class="<?= !$key ? 'active' : '' ?> tiles-box__nav-item">
                            <a href="#tastes-content-<?= $key++ ?>" data-toggle="tab" class="tiles-box__nav-link"><?= $taste ?></a>
                        </li>
                    <? endforeach ?>
                </ul>
            </div>
            <div class="tab-content tiles-box__body">
	            <?
	            $key = 0;
                foreach ($data as $taste => $item): ?>
                    <div id="tastes-content-<?= $key ?>" class="tab-pane fade in <?= !$key++ ? 'active' : '' ?>">
                        <div class="tiles-box__list">
                            <? foreach ($item as $product): ?>
                            <div class="tiles-box__list-item">
                                <div class="info-card">
                                    <span class="info-card__image">
                                        <?= wp_get_attachment_image($product['thumb_id'], 'big-square', ['class' => 'info-card__image-i']) ?>
                                    </span>
                                    <span class="info-card__content">
                                        <span class="info-card__title"><?= $product['title'] ?></span>
                                        <span class="info-card__text"><?= $product['description'] ?></span>
                                    </span>
                                </div>
                            </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
</div>
