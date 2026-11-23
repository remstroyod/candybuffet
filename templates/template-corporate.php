<?php
/**
 * Template Name: Корпоративные заказы
 */
?>
<?php get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
  <div class="page-wrap page-wrap_wide">
    <div class="page-wrap__content">
      <div class="content-box">
        <?php get_template_part('templates/page', 'top') ?>
      </div>
      <?php if (have_rows('як_це_працює')): ?>
        <section class="how-it-works">
          <div class="how-it-works__header">
            <div class="styled-heding">
              <div class="styled-heding__header-inner">
                <div class="styled-heding__label">How it works</div>
                <h2 class="styled-heding__title">Як це працює</h2>
              </div>
            </div>
          </div>
          <div class="stats-box anim-disabled">
            <?php $i = 0;
            while (have_rows('як_це_працює')): the_row();
              $i++; ?>
              <div class="stats-box__item">
                <div class="stats-box__value"><?= $i ?></div>
                <div class="stats-box__label"><?php the_sub_field('описание') ?></div>
              </div>
            <?php endwhile; ?>
          </div>
        </section>
      <?php endif ?>
    </div>
  </div>
</div>

<div id="form" class="container">
  <div class="request-box">
    <div class="request-box__heading">
      <div class="styled-heding">
        <div class="styled-heding__header-inner">
          <div class="styled-heding__label">Send request</div>
          <h2 class="styled-heding__title">Відправити заявку</h2>
        </div>
      </div>
    </div>
    <div class="request-box__inner">
        <div class="validation-wrap">
          <form novalidate="novalidate" enctype="multipart/form-data" class="request-box__form js__form">
            <input type="hidden" name="action" value="corporateForm">
            <input type="hidden" name="page" value="<?php the_ID() ?>">
            <div class="question-box__form-row">
              <div class="question-box__form-col">
                <div class="form-group">
                  <input name="cf_name" id="field-question-name" aria-required autocomplete="name" class="form-control">
                  <label for="field-question-name" class="control-label">Ім’я</label>
                </div>
                <div class="form-group">
                  <input type="tel" name="cf_phone" id="field-question-phone" aria-required autocomplete="tel" data-mask="+38(099) 999 99 99" class="form-control">
                  <label for="field-question-phone" class="control-label">Телефон</label>
                </div>
              </div>
              <div class="question-box__form-col">
                <div class="question-box__header">
                  <div class="question-box__text">
                    <?php the_field('form'); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="question-box__form-footer">
              <button type="submit" class="btn btn-bordered question-box__form-button"><?php the_field('form_submit_label'); ?></button>
            </div>
          </form>
          <div class="request-box__success">
           <?php the_field('form_submit') ?>
          </div>
        </div>
      </div>
  </div>
</div>
