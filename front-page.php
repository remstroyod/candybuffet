<? use Roots\Sage\Assets; ?>
<div class="container">
    <div class="relative">
        <div class="promo-slider">
		    <? while(have_rows('баннер')):
			    the_row();
			    ?>

                <div class="promo-slider__item" style="background-color: <? the_sub_field('background_color') ?>">

                    <div class="promo-slider__content">
                        <div class="promo-slider__title">
                            <div class="promo-slider__title-inner"><? the_sub_field('заголовок') ?></div>
                        </div>
                        <div class="promo-slider__text">
                            <div class="promo-slider__text-inner"><? the_sub_field('краткое_описание') ?></div>
                        </div>
					    <? if($btn_text = get_sub_field('текст_кнопки')): ?>
                            <div class="promo-slider__footer">
                                <a href="<? the_sub_field('ссылка') ?>" class="btn btn-default promo-slider__button"><?= $btn_text ?></a>
                            </div>
					    <? endif ?>
                    </div>
	                <? while (have_rows('медиа')):
		                the_row();
		                $type = get_sub_field('тип_медиа');
		                ?>
                        <div class="promo-slider__<?= $type ?>">
			                <? if ($type === 'image') {
				                echo wp_get_attachment_image((int) get_sub_field($type), 'medium', false, 'class=promo-slider__image-i');
				                echo wp_get_attachment_image((int) get_sub_field($type.'_mobile'), 'medium', false, 'class=promo-slider__image-i mobile');
			                } elseif ($type === 'video') { ?>
                                <video src="<?= esc_attr(get_sub_field($type)) ?>" preload="none" loop muted></video>
			                <? } ?>
                        </div>
	                <? endwhile; ?>
                </div>
		    <? endwhile ?>
        </div>
    </div>

	<div class="info-box">
        <? while(have_rows('блок_сервисов')): the_row() ?>
		<section class="info-box__item">
			<div class="info-box__image">
                <a href="<? the_sub_field('ссылка') ?>">
                    <img src="<? the_sub_field('изображение') ?>" alt="<? bloginfo('name') ?>" class="info-box__image-i" />
                </a>
            </div>
            <div class="info-box__content">
                <div class="info-box__label"><? the_sub_field('заголовок_en') ?></div>
                <div class="info-box__title">
                    <a href="<? the_sub_field('ссылка') ?>"><? the_sub_field('заголовок') ?></a>
                </div>
                <div class="info-box__text"><? the_sub_field('краткое_описание') ?></div>
            </div>
		</section>
        <? endwhile ?>
	</div>
</div>
<?php if($new = get_field('новинки')): ?>
<section class="tiles-carousel">
	<div class="container">
		<div class="tiles-carousel__header">
			<div class="styled-heding styled-heding_pos-right">
				<div class="styled-heding__header-inner">
					<div class="styled-heding__label">New</div>
					<h2 class="styled-heding__title">Новинки</h2>
				</div>
			</div>
		</div>
		<div class="tiles-carousel__list">
            <? foreach($new as $post):
                setup_postdata($post);
                ?>
                <div class="tiles-carousel__list-item">
                    <a href="<? the_permalink() ?>" class="product-card">
                    <span class="product-card__image">
                        <? the_post_thumbnail('product-height', ['class' => 'product-card__image-i']) ?>
                    </span>
                        <span class="product-card__title"><? the_title() ?></span>
                        <span class="product-card__price"><?= $post->product->display_price() ?></span>
                    </a>
                </div>
            <? endforeach;wp_reset_postdata(); ?>
		</div>

        <div class="stats-box">
			<? while(have_rows('блок_с_цифрами')): the_row() ?>
                <div class="stats-box__item">
                    <div class="stats-box__value"><? the_sub_field('цифра') ?></div>
                    <div class="stats-box__label"><? the_sub_field('заголовок') ?></div>
                </div>
			<? endwhile ?>
        </div>
	</div>
</section>
<?php endif; ?>

<section class="presentation-box">
    <div class="container">
        <div class="presentation-box__inner">
            <div class="presentation-box__aside">
                <div class="presentation-box__image">
                    <img src="<?= Assets\asset_path('images/corp.jpg') ?>" alt="" class="presentation-box__image-i">
                </div>
           </div>
            <div class="presentation-box__content">
                <div class="presentation-box__header">
                    <div class="styled-heding">
                        <div class="styled-heding__header-inner">
                            <div class="styled-heding__label">Gift for clients</div>
                            <h2 class="styled-heding__title">Корпоративні<br>замовлення</h2>
                        </div>
                    </div>
                </div>
                <div class="presentation-box__text">Ми маємо великий досвід в роботі з корпоративними кліентами. Зможемо підібрати вам подарунок будь-якої складності та допоможемо з дизайном.</div>
                <div class="validation-wrap">
                    <form novalidate="novalidate" class="question-box__form js__form">
                        <input type="hidden" name="action" value="corporateForm">
                        <input type="hidden" name="page" value="<?php the_ID() ?>">
                        <div class="question-box__form-row">
                            <div class="question-box__form-col">
                                <div class="form-group">
                                    <input name="cf_name" id="field-name" type="text" aria-required="" class="form-control">
                                    <label for="field-name" class="control-label">Ім’я</label>
                                </div>
                                <div class="form-group">
                                    <input name="cf_phone" id="field-question-phone" type="tel" aria-required="" data-mask="+38(099) 999 99 99" class="form-control" im-insert="true">
                                    <label for="field-question-phone" class="control-label">Телефон</label>
                                </div>
                            </div>
                        </div>
                        <div class="question-box__form-footer">
                            <button type="submit" class="btn btn-bordered question-box__form-button">Замовити Презентацію</button>
                        </div>
                    </form>
                    <div class="question-box__success">
                        <div class="question-box__success-title">Дякуємо!</div>
                        <div class="question-box__success-text">Ваш запит надіслано!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if($best = get_field('populyarni_produkty')): ?>
    <section class="tiles-carousel">
        <div class="container">
            <div class="tiles-carousel__header">
                <div class="styled-heding styled-heding_pos-right">
                    <div class="styled-heding__header-inner">
                        <div class="styled-heding__label">bestsellers</div>
                        <h2 class="styled-heding__title">Популярні</h2>
                    </div>
                </div>
            </div>
            <div class="tiles-carousel__list">
				<? foreach($best as $post): setup_postdata($post); ?>
                    <div class="tiles-carousel__list-item">
                        <a href="<? the_permalink() ?>" class="product-card">
                            <span class="product-card__image">
                                <? the_post_thumbnail('product-height', ['class' => 'product-card__image-i']) ?>
                            </span>
                            <span class="product-card__title"><? the_title() ?></span>
                            <span class="product-card__price"><?= $post->product->display_price() ?></span>
                        </a>
                    </div>
				<? endforeach;wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<div class="container">
	<section class="trust-box">
		<div class="trust-box__header">
			<div class="styled-heding">
				<div class="styled-heding__header-inner">
					<div class="styled-heding__label">Why candy buffet?</div>
					<h2 class="styled-heding__title">Чому нам довіряють</h2>
				</div>
			</div>
		</div>
        <? while (have_rows('блок_почему_нам_доверяют')): the_row() ?>
		<div class="trust-box__body">
			<? $trust_content = get_field('блок_почему_нам_доверяют') ?>
			<div class="trust-box__content">
				<div class="trust-box__list">
					<? $i = 0;
					while (have_rows('преимущества')): $i++;
						the_row();
						?>
					<div class="trust-box__list-item">
						<div class="trust-box__list-icon">
                            <img src="<? the_sub_field('иконка') ?>" alt="" width="70" class="trust-box__list-icon-i" />
                        </div>
						<div class="trust-box__list-wrap">
							<div class="trust-box__list-num"><?= sprintf('%02d', $i) ?></div>
							<div class="trust-box__list-text"><? the_sub_field('краткое_описание') ?></div>
						</div>
					</div>
					<? endwhile; ?>
				</div>
			</div>
			<div class="trust-box__aside">
				<? if ($img = get_sub_field('изображение_1')): ?>
                <div class="trust-box__image trust-box__image_big"><img src="<?= $img ?>" alt="" class="trust-box__image-i" /></div>
                <? endif ?>
				<? if ($img = get_sub_field('изображение_2')): ?>
				<div class="trust-box__image trust-box__image_sm"><img src="<?= $img ?>" alt="" class="trust-box__image-i" /></div>
                <? endif ?>
            </div>
		</div>
        <? endwhile; ?>
	</section>
    <? get_template_part('templates/part', 'clients') ?>
</div>

<div class="container">
	<section class="question-box">
		<div class="validation-wrap">
			<form novalidate="novalidate" class="question-box__form js__form">
                <input type="hidden" name="action" value="contactForm">
				<div class="question-box__form-row">
					<div class="question-box__form-col">
						<? get_template_part('templates/form', 'base') ?>
					</div>
					<div class="question-box__form-col">
						<div class="question-box__header">
							<h2 class="question-box__title">Залишились запитання?</h2>
							<div class="question-box__text">Якшо ви не знайшли відповіді на всі свої запитання, тоді пишіть нам. Ми завжди раді допомогти.</div>
						</div>
						<div class="form-group">
                            <textarea name="cf_message" rows="5" placeholder="Ваше питання..." aria-required class="form-control"></textarea>
                        </div>
					</div>
				</div>
				<div class="question-box__form-footer">
                    <button type="submit" class="btn btn-bordered question-box__form-button">Відправити</button>
                </div>
			</form>
			<div class="question-box__success">
				<div class="question-box__success-title">Дякуємо!</div>
				<div class="question-box__success-text">Ваше питання надіслано!</div>
			</div>
		</div>
	</section>
</div>
