<?php
$term = get_queried_object();  
$termId = $term instanceof WP_Term ? $term->term_id : 0;
$seoText = '';
if ($termId) {
	$seoText = get_field('seo_description', 'term_' . $termId);
	if (!empty($seoText)) {
	?>
		<div class="content-box__text" style="padding-top:65px;">
			<?php echo apply_filters('the_content', $seoText);?>
		</div>
<?php
	}
}
?>
