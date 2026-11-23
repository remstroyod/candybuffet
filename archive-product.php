<? get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
	<div class="page-wrap">
		<div class="page-wrap__content">
			<div class="cat-tiles">
				<? foreach (get_terms(['taxonomy' => 'product_category', 'parent' => 0, 'hide_empty' => 0]) as $term): ?>
					<div class="cat-tiles__item">
						<a href="<?= get_term_link($term) ?>" class="cat-tiles__box">
						<span class="cat-tiles__box-image">
							<?= wp_get_attachment_image(get_field('изображение', $term), 'big-square', false, ['class' => 'big-square']) ?>
						</span>
						<span class="cat-tiles__box-content">
							<span class="cat-tiles__box-label"><? the_field('заголовок_en', $term) ?></span>
							<span class="cat-tiles__box-title"><?= $term->name ?></span>
						</span>
						</a>
					</div>
				<? endforeach ?>
			</div>
		</div>
	</div>
</div>