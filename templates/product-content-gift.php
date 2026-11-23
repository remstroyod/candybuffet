<? while(have_rows('блок_описания_подарка', 'option')): the_row() ?>
<div class="card__gift">
    <div class="gift-box">
        <span class="gift-box__image">
            <img src="<? the_sub_field('изображение') ?>" alt="" class="gift-box__image-i">
        </span>
        <span class="gift-box__content">
            <span class="gift-box__title"><? the_sub_field('заголовок') ?></span>
            <span class="gift-box__text"><? the_sub_field('описание') ?></span>
        </span>
    </div>
</div>
<? endwhile; ?>