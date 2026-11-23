<?php
/**
 * Template Name: 2-х колоночная страница
 */
?>
<? get_template_part('templates/page', 'left-title') ?>
<div class="container">
	<div class="page-wrap">
		<div class="page-wrap__content">
			<div class="data-list">
				<? while(have_rows('контент')): the_row() ?>
				<section class="data-list__item">
					<h2 class="data-list__title"><? the_sub_field('заголовок') ?></h2>
					<div class="data-list__text">
						<? the_sub_field('текст') ?>
					</div>
				</section>
				<? endwhile; ?>
			</div>
		</div>
	</div>
</div>

