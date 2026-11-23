<?php
/**
 * Template Name: Контакты
 */
?>
<? get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
	<div class="page-wrap page-wrap_simple">
		<div class="page-wrap__content">
			<div class="page-wrap__header">
				<? get_template_part('templates/part', 'styled-heding') ?>
			</div>
			<div class="contacts-box">
				<div class="contacts-box__list">
					<div class="contacts-box__list-item">
						<div class="contacts-box__list-title">Адреса</div>
						<div class="contacts-box__list-text"><? the_field('адрес') ?></div>
					</div>
					<div class="contacts-box__list-item">
						<div class="contacts-box__list-title">Графік роботи</div>
						<div class="contacts-box__list-text">
							<? the_field('график_работы') ?>
						</div>
					</div>
					<div class="contacts-box__list-item">
						<div class="contacts-box__list-title">Телефони</div>
						<div class="contacts-box__list-text">
							<? if ($phones = get_field('телефоны')) {
								$phones = explode(PHP_EOL, $phones);
								echo array_reduce($phones, function($result, $phone){
									$phone = trim($phone);

									return $phone ? $result . '<a href="callto:'.preg_replace('@[^+\d]+@', '', $phone).'">'.$phone.'</a><br>' : $result;
								});
							} ?>
						</div>
					</div>
					<div class="contacts-box__list-item">
						<div class="contacts-box__list-title">Email</div>
						<div class="contacts-box__list-text">
							<? the_work_email() ?>
						</div>
					</div>
				</div>
				<div class="contacts-box__map">
					<div class="contacts-box__map-header">
						<a rel="nofollow" href="<? the_location_link() ?>" target="_blank" class="contacts-box__map-link">відкрити на Google maps</a>
					</div>
					<div class="contacts-box__map-body">
						<div class="map-box">
							<div id="map-box-frame" class="map-box__inner"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
