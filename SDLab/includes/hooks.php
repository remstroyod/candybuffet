<?php

use \SDLab\Core;

add_filter('site_transient_update_plugins', [Core::instance(), 'disable_plugin_updates']);
//remove_action('wp_head', 'wp_oembed_add_discovery_links');
//remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_head', 'wp_resource_hints', 2);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // index link
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'adjacent_posts_rel_link', 10); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'qtranxf_wp_head'); // Display the XHTML generator that is generated on the wp_head hook, WP version


remove_action('embed_head', 'print_emoji_detection_script');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');

// remove_action('wp_head', 'wp_print_scripts');
// add_action('wp_footer', 'wp_print_scripts', 5);
// remove_action('wp_head', 'wp_enqueue_scripts', 1);
// add_action('wp_footer', 'wp_enqueue_scripts', 5);
// remove_action('wp_head', 'wp_print_head_scripts', 9);
// add_action('wp_footer', 'wp_print_head_scripts', 5);

add_filter('rest_authentication_errors', [\SDLab\Core::instance(), 'rest_authentication_errors']);

add_action('login_enqueue_scripts', [Core::instance(), 'my_login_logo']);

add_action('init', [Core::instance(), 'include_custom_jquery'], 1001);

add_action('customize_register', [Core::instance(), 'customize_register']);

add_filter('intermediate_image_sizes_advanced', [Core::instance(), 'remove_default_image_sizes']);

add_filter('wp_trim_excerpt', 'sdlab_wp_trim_excerpt');
function sdlab_wp_trim_excerpt($text) {
    return wp_trim_words($text, 30);
}
