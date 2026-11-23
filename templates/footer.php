<? use Roots\Sage\Assets; ?>

<?php if ( function_exists( 'cb_ty_is_page' ) && cb_ty_is_page() ): ?>
  <?php get_template_part( 'templates/footer', 'short' ); ?>
<?php else: ?>
<footer class="footer">
    <div class="container">
        <div class="footer__inner">
            <div class="footer-horizontal">
              <?php echo str_replace(
                [ 'custom-logo-link', 'custom-logo' ],
                [ 'footer__logo', 'footer__logo-image', ],
                get_custom_logo()
              ) ?>
            </div>
            <div class="footer__content">
                <div class="footer__col">
                    <div class="footer__col-title">Контакти</div>
                    <div class="footer__contacts">
                        <div class="footer__contacts-item">
							<? $location = get_field('адрес', 'option'); ?>
                            <a class="footer__contacts-link" rel="nofollow" target="_blank" href="<? the_location_link() ?>"><?= $location ?></a>
                        </div>
                        <div class="footer__contacts-item">
							<? the_work_phone('footer__contacts-link') ?>
                        </div>
                        <div class="footer__contacts-item">
							<? the_work_email('footer__contacts-link') ?>
                        </div>
                    </div>

                </div>
                <div class="footer__col">
                    <div class="footer__col-title">Оплата/доставка</div>
					<? wp_nav_menu([
						'container'      => false,
						'menu_class'     => 'footer__submenu',
						'theme_location' => 'footer_navigation'
					]) ?>
                </div>
                <div class="footer__col">
                    <div class="footer__col-title">Підписатись</div>
                   <? get_template_part('templates/footer', 'social') ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>
<div class="modals">
    <div id="modal-payment-success" tabindex="-1" class="modal fade modal-wide">
        <div class="modal-dialog">
            <div class="modal-content"><button data-dismiss="modal" type="button" class="modal-close close"></button>
                <div class="success-box">
                    <div class="success-box__title">Вітаємо, оплата пройшла успіно</div>
                    <div class="success-box__text">В найближчий час з Вами зв’яжеться наш менеджер для уточнення замовлення</div>
                    <div class="success-box__footer">
                        <a href="<? bloginfo('url') ?>" class="btn btn-bordered success-box__button">Повернутись на сайт</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-payment-transfer" tabindex="-1" class="modal fade modal-transfer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="success-box">
                    <div class="success-box__title">Дякуємо за вишукане замовлення!</div>
                    <div class="success-box__text">Наш менеджер зв'яжеться з Вами для уточнення деталей.
                        <br>До солодкої зустрічі!</div>
                    <div class="success-box__footer">
                        <a href="<? bloginfo('url') ?>" class="btn btn-bordered success-box__button">Повернутись на сайт</a>
                    </div>
                    <div class="success-box__social">
                       <p><b>Приєднайтесь до нас </b>в соціальних мережах</p>
                        <? get_template_part('templates/footer', 'social') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <div id="modal-help-weight" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content"><button data-dismiss="modal" type="button" class="modal-close close"></button>
        <div class="modal-title">Як розрахувати вагу</div>
        <div class="weight-info">
          <div class="weight-info__row">
            <div class="weight-info__col">
              <div class="weight-info__title">Гості</div>
              <div class="weight-info__image"><img src="<?= Assets\asset_path('images/peoples-icon.svg') ?>" alt="" class="weight-info__image-i"></div>
              <div class="weight-info__list">
                <div class="weight-info__list-item">10-15</div>
                <div class="weight-info__list-item">15-20</div>
                <div class="weight-info__list-item">20-30</div>
                <div class="weight-info__list-item">30-40</div>
                <div class="weight-info__list-item">40-50</div>
                <div class="weight-info__list-item">50-70</div>
                <div class="weight-info__list-item">70-90</div>
                <div class="weight-info__list-item">100+</div>
              </div>
            </div>
            <div class="weight-info__col">
              <div class="weight-info__title">Вага, кг</div>
              <div class="weight-info__image"><img src="<?= Assets\asset_path('images/scale-icon.svg') ?>" alt="" class="weight-info__image-i"></div>
              <div class="weight-info__list">
                <div class="weight-info__list-item">2-3</div>
                <div class="weight-info__list-item">3-4</div>
                <div class="weight-info__list-item">4-6</div>
                <div class="weight-info__list-item">6-8</div>
                <div class="weight-info__list-item">8-9</div>
                <div class="weight-info__list-item">9-11</div>
                <div class="weight-info__list-item">11-14</div>
                <div class="weight-info__list-item">15+</div>
              </div>
            </div>
          </div>
          <div class="weight-info__data">
            <div class="weight-info__data-item">
              <div class="weight-info__data-label">До 50 гостей</div>
              <div class="weight-info__data-value">200 г на людину</div>
            </div>
            <div class="weight-info__data-item">
              <div class="weight-info__data-label">Більше 50 гостей</div>
              <div class="weight-info__data-value">150 г на людину</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

