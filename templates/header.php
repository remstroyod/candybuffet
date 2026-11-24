<? use Roots\Sage\Assets; ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5S6B9CC3"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<header class="header">
	<?php do_action( 'cbbf_header_bar' ); // плашка Black Friday (плагін cb-black-friday) ?>
	<?php get_template_part('templates/page-banner-top') ?>

    <div class="container">
        <div class="header__inner">
            <div class="header__content">
              <?php echo str_replace(
                [ 'custom-logo-link', 'custom-logo' ],
                [ 'header__logo', 'header__logo-image', ],
                get_custom_logo()
              ) ?>
              <? wp_nav_menu([
	                    'container'      => false,
                        'menu_class'     => 'header__submenu',
                        'theme_location' => 'top_navigation'
	            ]) ?>
            </div>
            <div class="header__aside">
                <div class="header__socials">
                    <a href="<? the_field('tg', 'option') ?>" rel="nofollow" target="_blank" class="header__socials-item">
                        <img src="<?= Assets\asset_path('images/tg-icon.svg') ?>" alt="" class="header__socials-image">
                    </a>
                    <a href="<? the_field('instagram', 'option') ?>" rel="nofollow" target="_blank" class="header__socials-item">
                        <img src="<?= Assets\asset_path('images/instagram-icon.svg') ?>" alt="" class="header__socials-image">
                    </a>
                </div>
                <div class="header__contacts">
                    <div class="header__contacts-row">
	                    <? the_work_phone('header__contacts-link') ?>
                    </div>
                    <div class="header__contacts-row">
                        <div class="header__contacts-text"><?= get_field('график_работы', 'option') ?></div>
                    </div>
                </div>
                <div class="header__cart">
                    <a href="/cart/" class="header__cart-link">
                        <img src="<?= Assets\asset_path('images/cart-icon.svg') ?>" alt="" width="21" class="header__cart-icon">
                        <span class="header__cart-count simplebasket__cart-count"></span>
                    </a>
                </div>
            </div>
        </div>
        <div class="header__overlay"></div>
        <div class="header__toggle">
            <div class="header__toggle-icon"></div>
        </div>
        <nav class="header__menu">
            <h3 class="sr-only">Головне меню</h3>
            <? wp_nav_menu(['theme_location' => 'primary_navigation', 'container' => false, 'menu_class' => 'header__menu-list']) ?>
        </nav>
    </div>
</header>
