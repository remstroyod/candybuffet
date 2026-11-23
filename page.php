<? get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
	<div class="page-wrap">
		<div class="page-wrap__content">
            <div class="content-box__header">
                <? get_template_part('templates/part', 'styled-heding') ?>
            </div>
            <div class="content-box__text">
                <? the_content() ?>
            </div>
		</div>
	</div>
</div>