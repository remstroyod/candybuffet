<?php while(have_rows('banner', get_queried_object())): the_row() ?>
    <?php if(get_sub_field('uvimknuty')): ?>
        <a href="<?php the_sub_field('posylannya'); ?>" class="products-box-banner">
            <span class="products-box-banner__image">
                <?= wp_get_attachment_image(get_sub_field('zobrazhennya')) ?>
            </span>
            <span class="products-box-banner__col">
                <span class="products-box-banner__title"><?php the_sub_field('zagolovok'); ?></span>
                <span class="products-box-banner__text"><?php the_sub_field('tekst'); ?></span>
            </span>
        </a>
    <?php endif; ?>
<?php endwhile; ?>
