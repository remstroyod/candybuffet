<?php while ( have_rows( 'page-top-banner', 'option' ) ):
    the_row(); ?>
	<?php
	if ( get_sub_field( 'enable' ) ):
		$link = get_sub_field( 'link' );
		?>
        <div class="page-top-banner">
            <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.75961 10.1925V5.75615H8.5124C7.13126 5.75615 6.40301 4.8605 6.40301 3.98997C6.40301 3.08593 7.07268 2.55022 7.96833 2.55022C8.9979 2.55022 9.80147 3.34543 9.80147 4.67632V5.75615H11.1994V4.67632C11.1994 3.34543 12.0029 2.55022 13.0325 2.55022C13.9281 2.55022 14.6061 3.08593 14.6061 3.98997C14.6061 4.8605 13.8528 5.75615 12.4884 5.75615H11.2412V10.1925H17.1424C18.0549 10.1925 18.5487 9.84097 18.5487 8.94529V7.00336C18.5487 6.11607 18.0549 5.75615 17.1424 5.75615H15.1921C15.711 5.2874 16.0208 4.65122 16.0208 3.93136C16.0208 2.31586 14.7401 1.15236 13.1162 1.15236C11.9109 1.15236 10.9315 1.82197 10.5046 3.01061C10.0777 1.82197 9.08997 1.15236 7.88461 1.15236C6.26911 1.15236 4.98004 2.31586 4.98004 3.93136C4.98004 4.65122 5.28136 5.2874 5.80872 5.75615H3.8584C2.99622 5.75615 2.45215 6.11607 2.45215 7.00336V8.94529C2.45215 9.84097 2.95436 10.1925 3.8584 10.1925H9.75961ZM9.75961 18.8476V11.0881H3.64911V16.7215C3.64911 18.1194 4.46943 18.8476 5.86729 18.8476H9.75961ZM11.2412 11.0881V18.8476H15.1335C16.5314 18.8476 17.3517 18.1194 17.3517 16.7215V11.0881H11.2412Z" fill="white"/>
            </svg><div><?php the_sub_field( 'text' ); ?><?php printf( '<a target="%s" href="%s">%s</a>', $link['target'] ?? '_blank', $link['url'], $link['title'] ) ?></div>
        </div>
        <script>
          function updatePadding() {
            document.body.style.paddingTop = document.querySelector('header').offsetHeight + 'px';
          }

          window.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('resize', updatePadding);
            updatePadding();
          });
        </script>
    <?php endif; ?>
<?php endwhile; ?>

