<form id="catalog-params" class="products-box__panel">
    <? if(get_queried_object()->parent === 13 || get_queried_object_id() === 13): ?>
        <div class="products-box__panel-item">
            <div class="products-box__panel-control">
                <select onchange="location.href = this.value + location.search" data-placeholder="Категорія" class="select2">
                    <option data-placeholder></option>
	                <? $parent = get_queried_object()->parent ?: get_queried_object_id() ?>

                    <? if($parent !== get_queried_object_id()): ?>
                        <option value="<?= get_term_link($parent) ?>">Всі</option>
	                <? endif ?>

                    <? $subcats = get_terms([
                        'hide_empty' => 0,
                        'taxonomy'   => get_queried_object()->taxonomy,
                        'parent'     => $parent
                    ]);
                    foreach ($subcats as $subcat): ?>
                        <option <?= get_queried_object_id() == $subcat->term_id ? 'selected' : '' ?> value="<?= get_term_link($subcat) ?>"><?= $subcat->name ?></option>
                    <? endforeach ?>
                </select>
            </div>
        </div>
        <div class="products-box__panel-item">
            <div class="products-box__panel-control">
                <select onchange="document.getElementById('catalog-params').submit()" name="type" data-placeholder="Тип" class="select2">
                    <option value=""></option>
                    <? if($types = acf_get_field('тип')): ?>
                        <? foreach ($types['choices'] as $key => $type): ?>
                            <option <?= !empty($_REQUEST['type']) && $_REQUEST['type'] === $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $type ?></option>
                        <? endforeach ?>
                    <? endif ?>
                    <option value="all">Всі</option>
                </select>
            </div>
        </div>
    <? endif ?>
	<div class="products-box__panel-item">
		<div class="products-box__panel-control">
			<select onchange="document.getElementById('catalog-params').submit()" name="sortby" data-placeholder="Сортувати" class="select2">
				<option data-placeholder></option>
				<option <?= !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] === 'price_asc' ? 'selected' : '' ?> value="price_asc">Ціна : від найменшої</option>
				<option <?= !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] === 'price_desc' ? 'selected' : '' ?> value="price_desc">Ціна : від найбільшої</option>
				<option <?= !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] === 'popular' ? 'selected' : '' ?> value="popular">Популярні</option>
			</select>
		</div>
	</div>
</form>