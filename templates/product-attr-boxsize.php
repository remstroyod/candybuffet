<? if (isset($field) && have_rows($field)): ?>
	<div class="card__info">
		<div class="card__info-label">В цій коробці:</div>
		<div class="info-widget">
			<? while(have_rows($field)): the_row(); ?>
			<div class="info-widget__item">
				<div class="info-widget__image info-widget__image_sm">
					<?= wp_get_attachment_image(get_sub_field('изображение'), 'thumbnail', false, ['class' => 'info-widget__image-i']) ?>
				</div>
				<div class="info-widget__popup">
					<div class="info-widget__image info-widget__image_md">
						<?= wp_get_attachment_image(get_sub_field('изображение'), 'medium', false, ['class' => 'info-widget__image-i']) ?>
					</div>
					<div class="info-widget__popup-content">
						<div class="info-widget__title"><? the_sub_field('название_конфет') ?></div>
						<div class="info-widget__text"><? the_sub_field('описание') ?></div>
					</div>
				</div>
			</div>
			<? endwhile ?>
		</div>
	</div>
<? endif ?>