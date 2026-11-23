<div class="content-box__inner">
  <?php if (($img_1 = get_field('изображение_1')) && ($img_2 = get_field('изображение_2'))): ?>
    <div class="content-box__aside">
      <div class="content-box__images">
        <div class="content-box__image content-box__image_big">
          <img src="<?= $img_1 ?>" alt="" class="content-box__image-i"/>
        </div>
        <div class="content-box__image content-box__image_small">
          <img src="<?= $img_2 ?>" alt="" class="content-box__image-i"/>
        </div>
      </div>
    </div>
  <?php endif ?>

  <div class="content-box__content">
    <div class="content-box__header">
      <?php get_template_part('templates/part', 'styled-heding') ?>
    </div>
    <div class="content-box__text">
      <?php the_content() ?>
    </div>

    <?php
    $file_btn = get_field('presentation_file');
    if (isset($file_btn['file'])):
      $file = $file_btn['file'];
      ?>
      <div class="content-box__footer">
        <a href="#form" class="btn btn-bordered presentation-box__button"><?= $file_btn['label'] ?></a>
        <div class="content-box__footer-message">Файл <?= strtoupper($file['subtype']) ?> <?= size_format($file['filesize']) ?></div>
      </div>
    <?php endif ?>
  </div>
</div>
