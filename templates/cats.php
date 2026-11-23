<?php
// Отримати поточну категорію
$current_category = get_queried_object();

if (isset($current_category->term_id)) {
	$child_categories = get_categories([
		'taxonomy'   => 'product_category',
		'child_of'   => $current_category->term_id
	]);

	// Перевірка, чи є підкатегорії
	if (!empty($child_categories)): ?>
        <div class="products-box-cats">
            <?php foreach ($child_categories as $child): ?>
                <a href="<?= get_category_link($child->term_id) ?>" class="products-box-cats__item"><?= $child->name ?></a>
           <?php endforeach; ?>
        </div>
<?php endif; ?>
<?php } ?>