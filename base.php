<?php
use Roots\Sage\Wrapper;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>
<?= do_shortcode('[shfs_body]') ?>
<?php
do_action('get_header');
get_template_part('templates/header');
?>
<main class="main">
    <? include Wrapper\template_path(); ?>
</main>
<?
do_action('get_footer');
get_template_part('templates/footer');
wp_footer();
?>
</body>
</html>
