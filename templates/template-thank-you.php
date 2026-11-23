<?php
/**
 * Template Name: Thank You
 */

$ty    = cb_ty_get_view_data();
$found = $ty['state'] === 'ok';

[ $phone_text, $phone_href ] = cb_ty_site_phone();
$tg_url   = trim( (string) get_field( 'tg', 'option' ) );
$schedule = trim( wp_strip_all_tags( (string) get_field( 'график_работы', 'option' ) ) );
// У кроках макет використовує компактний запис: «Пн–Пт 10:00–18:00»
$schedule_compact = preg_replace( '/\s*([–—-])\s*/u', '$1', $schedule );

$icon_tg    = '<svg class="ty-btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M21.5 3.5 2.5 11l7 2.5m12-10-4 17-8-7.5m12-9.5-12 9.5m0 0V20l3.5-4"/></svg>';
$icon_phone = '<svg class="ty-btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2Z"/></svg>';
?>
<div class="ty">

  <!-- 1. Hero -->
  <section class="ty-hero">
    <div class="container">
      <?php if ( ! $found ): ?>
        <h1 class="ty-hero__title">Замовлення не знайдено</h1>
        <p class="ty-hero__text ty-hero__text--visible">Посилання застаріло або містить помилку. Зв'яжіться з нами — допоможемо.</p>
      <?php else: ?>

      <div class="ty-hero__check" aria-hidden="true">
        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"><path d="M10 23l8 8 16-17" stroke="currentColor" stroke-width="2"/></svg>
      </div>

      <div class="ty-label ty-label--center">Thank you</div>
      <h1 class="ty-hero__title">Дякуємо за замовлення!</h1>

      <?php if ( $ty['order_number'] !== '' ): ?>
        <p class="ty-hero__order">
          Замовлення <span class="ty-hero__number"><span class="ty-hero__no">№</span>&nbsp;<?php echo esc_html( $ty['order_number'] ); ?></span> прийнято
        </p>
      <?php endif; ?>

      <?php if ( ! empty( $ty['payment'] ) ): ?>
        <div class="ty-status ty-status--<?php echo esc_attr( $ty['payment']['tone'] ); ?>">
          <span class="ty-status__dot" aria-hidden="true"></span>
          <?php echo esc_html( $ty['payment']['label'] ); ?>
        </div>
      <?php endif; ?>

      <p class="ty-hero__text">Менеджер зв'яжеться з вами в робочий час, щоб підтвердити відправку.</p>

      <?php endif; ?>
    </div>
  </section>

  <?php if ( $found ): ?>
  <!-- 2–3. Доставка + Що буде далі -->
  <section class="ty-info">
    <div class="container">
      <div class="ty-info__grid<?php echo empty( $ty['delivery'] ) ? ' ty-info__grid--single' : ''; ?>">

        <?php if ( ! empty( $ty['delivery'] ) ): ?>
          <details class="ty-card ty-delivery" open>
            <summary class="ty-delivery__summary">
              <span class="ty-card__title">Доставка</span>
              <span class="ty-delivery__short"><?php echo esc_html( $ty['delivery']['short'] ); ?></span>
              <span class="ty-delivery__toggle" aria-hidden="true"></span>
            </summary>
            <dl class="ty-delivery__list">
              <?php foreach ( [ 'Отримувач' => 'recipient', 'Телефон' => 'phone', 'Доставка' => 'full' ] as $label => $field ): ?>
                <?php if ( ( $ty['delivery'][ $field ] ?? '' ) !== '' ): ?>
                  <div class="ty-delivery__row">
                    <dt><?php echo esc_html( $label ); ?></dt>
                    <dd><?php echo esc_html( $ty['delivery'][ $field ] ); ?></dd>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </dl>
          </details>
          <script>
            (function () {
              try {
                if (window.matchMedia('(max-width: 767px)').matches) {
                  var d = document.currentScript.previousElementSibling;
                  if (d && d.tagName === 'DETAILS') d.removeAttribute('open');
                }
              } catch (e) {}
            })();
          </script>
        <?php endif; ?>

        <div class="ty-card ty-steps">
          <h2 class="ty-card__title">Що буде далі</h2>
          <ol class="ty-steps__list">
            <li class="ty-steps__item is-active">
              <span class="ty-steps__num">01</span>
              <div class="ty-steps__body">
                <div class="ty-steps__title">Замовлення прийнято</div>
                <div class="ty-steps__text"><span class="ty-desk">Ми вже отримали ваше замовлення.</span><span class="ty-mob">Ми вже його отримали</span></div>
              </div>
            </li>
            <li class="ty-steps__item">
              <span class="ty-steps__num">02</span>
              <div class="ty-steps__body">
                <div class="ty-steps__title">Підтвердження</div>
                <div class="ty-steps__text">
                  <span class="ty-desk">Менеджер зв'яжеться в робочий час<?php echo $schedule !== '' ? ': <span class="ty-nowrap">' . esc_html( $schedule_compact ) . '</span>' : ''; ?>.</span>
                  <span class="ty-mob"><?php echo $schedule !== '' ? esc_html( $schedule_compact ) : "Менеджер зв'яжеться в робочий час"; ?></span>
                </div>
              </div>
            </li>
            <li class="ty-steps__item">
              <span class="ty-steps__num">03</span>
              <div class="ty-steps__body">
                <div class="ty-steps__title">Відправка</div>
                <div class="ty-steps__text"><span class="ty-desk">Номер ТТН надішлемо в SMS / Viber.</span><span class="ty-mob">ТТН — у SMS / Viber</span></div>
              </div>
            </li>
          </ol>
        </div>

      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- 4. Питання щодо замовлення -->
  <section class="ty-help">
    <div class="container">
      <div class="ty-help__inner">
        <div class="ty-help__content">
          <div class="ty-label">Need help?</div>
          <h2 class="ty-help__title">Питання щодо замовлення?</h2>
          <p class="ty-help__text">Назвіть номер замовлення — і ми швидко допоможемо.</p>
        </div>

        <div class="ty-help__actions">
          <?php if ( $tg_url !== '' ): ?>
            <a class="ty-btn" href="<?php echo esc_url( $tg_url ); ?>" target="_blank" rel="nofollow noopener">
              <?php echo $icon_tg; ?><span>Telegram</span>
            </a>
          <?php endif; ?>
          <?php if ( $phone_href !== '' ): ?>
            <a class="ty-btn" href="<?php echo esc_attr( $phone_href ); ?>">
              <?php echo $icon_phone; ?><span><?php echo esc_html( $phone_text ); ?></span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Mobile sticky-панель -->
  <?php if ( $tg_url !== '' || $phone_href !== '' ): ?>
    <div class="ty-sticky">
      <?php if ( $tg_url !== '' ): ?>
        <a class="ty-sticky__btn" href="<?php echo esc_url( $tg_url ); ?>" target="_blank" rel="nofollow noopener">
          <?php echo $icon_tg; ?><span>Telegram</span>
        </a>
      <?php endif; ?>
      <?php if ( $phone_href !== '' ): ?>
        <a class="ty-sticky__btn ty-sticky__btn--primary" href="<?php echo esc_attr( $phone_href ); ?>">
          <?php echo $icon_phone; ?><span>Подзвонити</span>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>
