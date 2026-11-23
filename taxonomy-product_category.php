<?php
$term = get_queried_object();
?>
<?php get_template_part('templates/header', 'breadcrumbs') ?>
<div class="container">
  <div class="page-wrap">
    <div class="page-wrap__content">
		<?php
		if ($term instanceof WP_Term) {
		?>
			<div class="content-box__header">
				<div class="styled-heding">
					<div class="styled-heding__header-inner">		
						<h1 class="styled-heding__title"><?php echo esc_html( $term->name );?></h1>
					</div>
				</div>            
			</div>
		<?php
		}
		?>
		<div class="products-box">
			<?php get_template_part('templates/cat-banner') ?>
			<?php get_template_part('templates/cats') ?>
			<?php get_template_part('templates/filter') ?>
			<?php get_template_part('templates/product', 'list') ?>
			<?php get_template_part('templates/cat-seotext') ?>
		</div>
    </div>
  </div>
</div>
