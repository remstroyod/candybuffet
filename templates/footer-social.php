<? use Roots\Sage\Assets; ?>

<div class="footer__socials">
    <a href="<? the_field('tg', 'option') ?>" target="_blank" rel="nofollow" class="footer__socials-item">
        <img src="<?= Assets\asset_path('images/tg-icon.svg') ?>" alt="" class="footer__socials-image"/>
    </a>
    <a href="<? the_field('instagram', 'option') ?>" target="_blank" rel="nofollow" class="footer__socials-item">
        <img src="<?= Assets\asset_path('images/instagram-icon.svg') ?>" alt="" class="footer__socials-image"/>
    </a>
</div>