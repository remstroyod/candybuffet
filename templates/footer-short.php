<?php
[ $phone_text, $phone_href ] = cb_ty_site_phone();
$schedule = trim( wp_strip_all_tags( (string) get_field( 'график_работы', 'option' ) ) );
?>
<footer class="footer ty-footer">
  <div class="container">
    <div class="ty-footer__inner">
      <?php echo str_replace(
        [ 'custom-logo-link', 'custom-logo' ],
        [ 'ty-footer__logo', 'ty-footer__logo-image' ],
        get_custom_logo()
      ) ?>
      <div class="ty-footer__contacts">
        <?php
        $parts = [];
        if ( $phone_href !== '' ) {
          $parts[] = '<a href="' . esc_attr( $phone_href ) . '">' . esc_html( $phone_text ) . '</a>';
        }
        if ( $schedule !== '' ) {
          $parts[] = esc_html( $schedule );
        }
        $parts[] = 'Київ';
        echo implode( ' · ', $parts );
        ?>
      </div>
      <div class="ty-footer__copy">© <?php bloginfo( 'name' ); ?>, <?php echo esc_html( date_i18n( 'Y' ) ); ?></div>
    </div>
  </div>
</footer>
