<?php

use SDLab\Core;
use SDLab\View;
use SimpleBasket\Helper;
use SimpleBasket\Product;
use SimpleBasket\Cart;

include 'vendor/autoload.php';

// show_admin_bar(false);

function dev(...$args)
{

  if (get_current_user_id() !== 1) {
    return;
  }

  dd(...func_get_args());
}

if (class_exists('\SDLab\Core')) {
  Core::instance()->init();
}

/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer

  'lib/simplebasket.php',

  'lib/forms.php', // Theme customizer

  'lib/keycrm.php', // KeyCRM

  'lib/thank-you.php', // Сторінка «Дякуємо за замовлення»
  'lib/phone.php', // Телефон з кодом країни в кошику
  'lib/packs.php', // Фірмові пакети в кошику
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

add_filter('body_class', function ($classes) {
  if ($pos = array_search('page', $classes)) {
    unset($classes[$pos]);
    $classes[] = 'is-page';
  }

  if ($pos = array_search('cart', $classes)) {
    unset($classes[$pos]);
    $classes[] = 'page-cart';
  }

  return $classes;
});

add_action('publish_recipe', 'select_parent_terms', 10, 2); // automatically select parent terms
function select_parent_terms($post_id, $post)
{
  if (!wp_is_post_revision($post_id)) {
    $taxonomies = get_taxonomies(['_builtin' => false]);
    foreach ($taxonomies as $taxonomy) {
      $terms = wp_get_object_terms($post->ID, $taxonomy);
      foreach ($terms as $term) {
        $parenttags = get_ancestors($term->term_id, $taxonomy);
        wp_set_object_terms($post->ID, $parenttags, $taxonomy, true);
      }
    }
  }
}

function my_acf_get_terms($args = [])
{

  global $wpdb;

  $sql = "
      SELECT term_id
      FROM {$wpdb->prefix}termmeta
      WHERE meta_key LIKE %s
      AND meta_value LIKE %s
      ";

  $rows = $wpdb->get_results($wpdb->prepare($sql, $args['meta_key'], empty($args['meta_value']) ? '%' : $args['meta_value']));

  $unit_ids = $terms = [];

  if (!$rows) {
    return $terms;
  }

  foreach ($rows as $row) {
    $unit_ids[] = $row->term_id;
  }

  $terms = get_terms([
    'taxonomy' => $args['taxonomy_slug'],
    'hide_empty' => false,
    'include' => $unit_ids,
  ]);

  return $terms;
}

add_filter('acf/settings/google_api_key', function () {
  return 'AIzaSyBZS5bdaqx7RZOY2WNp-BXCCE8HEfAOZPw';
});

function get_next_prev_product($previous = false)
{
  $tax = 'product_category';
  $post = get_adjacent_post(true, '', $previous, $tax);

  return $post;

}

function wpa_show_permalinks($post_link, $post)
{
  if (is_object($post) && $post->post_type == Product::get_post_type()) {
    return str_replace('%product_category%', Product::get_parent_product_category('slug', $post), $post_link);
  }

  return $post_link;
}

add_filter('post_type_link', 'wpa_show_permalinks', 1, 2);

function get_location_link()
{
  return sprintf('https://www.google.com/maps/search/%s/', urlencode(str_replace(',', '', get_field('адрес', 'option'))));
}

function the_location_link()
{
  echo get_location_link();
}

function the_work_phone($link_class = '')
{
  $orig_phone = get_field('телефон', 'option');
  $link_phone = preg_replace('@\D+@', '', $orig_phone);

  echo '<a href="tel://' . $link_phone . '" class="' . $link_class . '">' . $orig_phone . '</a>';
}

function the_work_email($class = '')
{
  $email = get_field('email', 'option');
  echo '<a href="mailto:' . $email . '" class="' . $class . '">' . $email . '</a>';
}

add_filter('nav_menu_css_class', function ($classes, $item, $args) {
  if ($args->theme_location === 'primary_navigation') {
    $classes[] = 'header__menu-item';
  }
  if ($args->theme_location === 'top_navigation') {
    $classes[] = 'header__submenu-item';
  }
  if ($args->theme_location === 'footer_navigation') {
    $classes[] = 'footer__submenu-item';
  }

  return $classes;
}, 10, 3);

add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
  if ($args->theme_location === 'primary_navigation') {
    $atts['class'] = 'header__menu-link';
  }
  if ($args->theme_location === 'top_navigation') {
    $atts['class'] = 'header__submenu-link';
  }
  if ($args->theme_location === 'footer_navigation') {
    $atts['class'] = 'footer__submenu-link';
  }

  if ( in_array( 'icon', $item->classes, true ) !== false) {
	  $args->link_after .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_50_4030" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_50_4030)"><path d="M7.30021 8.46667L6.33355 7.51667C6.21132 7.39445 6.05855 7.33334 5.87521 7.33334C5.69188 7.33334 5.53355 7.4 5.40021 7.53334C5.27799 7.65556 5.21688 7.81111 5.21688 8C5.21688 8.18889 5.27799 8.34445 5.40021 8.46667L6.83355 9.9C6.96688 10.0333 7.12243 10.1 7.30021 10.1C7.47799 10.1 7.63354 10.0333 7.76688 9.9L10.6002 7.06667C10.7335 6.93334 10.7974 6.77778 10.7919 6.6C10.7863 6.42223 10.7224 6.26667 10.6002 6.13334C10.4669 6 10.3085 5.93056 10.1252 5.925C9.94188 5.91945 9.78355 5.98334 9.65021 6.11667L7.30021 8.46667ZM5.43355 14.5L4.46688 12.8667L2.63354 12.4667C2.46688 12.4333 2.33355 12.3472 2.23355 12.2083C2.13355 12.0694 2.09466 11.9167 2.11688 11.75L2.30021 9.86667L1.05021 8.43334C0.9391 8.31111 0.883545 8.16667 0.883545 8C0.883545 7.83334 0.9391 7.68889 1.05021 7.56667L2.30021 6.13334L2.11688 4.25C2.09466 4.08334 2.13355 3.93056 2.23355 3.79167C2.33355 3.65278 2.46688 3.56667 2.63354 3.53334L4.46688 3.13334L5.43355 1.5C5.52243 1.35556 5.64466 1.25834 5.80021 1.20834C5.95577 1.15834 6.11132 1.16667 6.26688 1.23334L8.00021 1.96667L9.73355 1.23334C9.8891 1.16667 10.0447 1.15834 10.2002 1.20834C10.3558 1.25834 10.478 1.35556 10.5669 1.5L11.5335 3.13334L13.3669 3.53334C13.5335 3.56667 13.6669 3.65278 13.7669 3.79167C13.8669 3.93056 13.9058 4.08334 13.8835 4.25L13.7002 6.13334L14.9502 7.56667C15.0613 7.68889 15.1169 7.83334 15.1169 8C15.1169 8.16667 15.0613 8.31111 14.9502 8.43334L13.7002 9.86667L13.8835 11.75C13.9058 11.9167 13.8669 12.0694 13.7669 12.2083C13.6669 12.3472 13.5335 12.4333 13.3669 12.4667L11.5335 12.8667L10.5669 14.5C10.478 14.6444 10.3558 14.7417 10.2002 14.7917C10.0447 14.8417 9.8891 14.8333 9.73355 14.7667L8.00021 14.0333L6.26688 14.7667C6.11132 14.8333 5.95577 14.8417 5.80021 14.7917C5.64466 14.7417 5.52243 14.6444 5.43355 14.5Z" fill="#D95629"/></g></svg>';
  } else {
	  $args->link_after = '';
  }

  return $atts;
}, 10, 3);

add_filter('pre_get_posts', function ($query) {

  if (count($_REQUEST) && !defined('DISABLE_SUPERCACHE')) {
    define('DISABLE_SUPERCACHE', true);
  }

  if (!is_admin() && is_tax('product_category') && $query->is_main_query()) {
    $meta_query = ['relation' => 'AND'];

    $meta_query['allow'] = [
      'key' => 'allow',
      'type' => 'NUMERIC',
      'compare' => 'EXISTS',
    ];

    if (!empty($_REQUEST['type']) && $_REQUEST['type'] !== 'all') {
      $meta_query['type'] = [
        'key' => 'тип',
        'value' => $_REQUEST['type']
      ];
    }

    if (!empty($_REQUEST['sortby'])) {
      $sortby = explode('_', $_REQUEST['sortby']);

      if ('price' === $sortby[0]) {
        $meta_query['sort'] = [
          'key' => 'sort_price',
          'type' => 'NUMERIC',
          'compare' => 'EXISTS',
        ];

        $sort = $sortby[1] ?? 'ASC';
      } elseif ('popular' === $sortby[0]) {
        $meta_query['sort'] = [
          'key' => 'simplebasket_sales_count',
          'type' => 'NUMERIC',
          'compare' => 'EXISTS',
        ];

        $sort = 'DESC';
      }

      $query->set('orderby', ['allow' => 'DESC', 'sort' => $sort]);
    } else {
      $query->set('orderby', ['allow' => 'DESC', 'menu_order' => 'ASC']);
    }

    $query->set('meta_query', $meta_query);
    $query->set('posts_per_page', -1);
  }

  return $query;
});

function changeTaxonomyCheckboxlistOrder($args)
{
  if (isset($args['taxonomy'])) {
    $args['checked_ontop'] = false;
  }

  return $args;
}

add_filter('wp_terms_checklist_args', 'changeTaxonomyCheckboxlistOrder');

add_filter('simplebasket_price_format', function () {
  return 'frd';
});


add_action('set_object_terms', 'auto_set_parent_terms', 9999, 6); // automatically select parent terms
function auto_set_parent_terms($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids)
{

  foreach ($tt_ids as $tt_id) {
    if ($parenttags = get_ancestors($tt_id, $taxonomy)) {
      wp_set_object_terms($object_id, $parenttags, $taxonomy, true);
    }
  }
}

remove_action( 'template_redirect', 'redirect_canonical' );

//remove_action('init', 'register_qm_collectors_debug_bar');

add_action('simplebasket/single/attributes/cakes', 'attributes_cakes_taste', 5);
add_action('simplebasket/single/attributes/cakes', 'attributes_cakes_weight', 10);
add_action('simplebasket/single/attributes/cakes', 'attributes_cakes_decor', 15);
add_action('simplebasket/single/attributes/cakes', 'attributes_min_order_date', 20);
add_action('simplebasket/single/attributes/cakes', 'single_after_buy_btn', 20);

function single_after_buy_btn()
{
  add_action('simplebasket/single/after_buy_btn', 'attributes_cakes_tabs', 25);
}


add_action('simplebasket/single/attributes/candies', 'attributes_candies_count', 5);
add_action('simplebasket/single/attributes/candies', 'attributes_candies_qty', 6);
add_action('simplebasket/single/attributes/candies', 'attributes_candies_weight', 10);
add_action('simplebasket/single/attributes/candies', 'attributes_candies_termin', 15);
add_action('simplebasket/single/attributes/candies', 'attributes_candies_boxsize', 20);
add_action('simplebasket/single/attributes/candies', 'attributes_min_order_date', 25);
add_action('simplebasket/single/attributes/candies', 'attributes_candies_boxinner', 30);

add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_count', 5);
add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_qty', 6);
add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_weight', 10);
add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_termin', 15);
add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_boxsize', 20);
add_action('simplebasket/single/attributes/tistechko', 'attributes_min_order_date', 25);
add_action('simplebasket/single/attributes/tistechko', 'attributes_candies_boxinner', 30);

add_action('simplebasket/single/buy_btn', 'single_buy_btn');

add_action('simplebasket/single/after_preview_image', 'single_after_preview_image');

add_action('simplebasket/single/before_buy_btn', function () {
  if (1 || Product::is_product_in_term('candies') || Product::is_product_in_term('tistechko')) {
    View::render('product-attr-boxsize', ['field' => 'содержание_коробки']);
  }
});

function single_cakes_price_modify($price)
{

  if (Product::is_product_in_term('candies') || Product::is_product_in_term('tistechko')) {
    // return $price;
  }

  if ($weight = get_weight()) {
    $first_weight = is_string($weight) ?: array_shift($weight);
    $price *= get_product_price_by_weight($first_weight);
  }

  $price += (float)get_field('декор');

  return $price;
}

function single_cakes_before_content()
{
  return 'single_card_data_class';
}

function get_all_product_tastes()
{
  $data = [];

  foreach (get_terms(['taxonomy' => 'product_taste', 'hide_empty' => 0]) as $term) {
    $data[] = $term->name;
  }

  sort($data);

  return $data;
}

function get_product_tastes()
{
  $tastes = [];

  if (!$current_type = get_field('тип')) {
    return;
  }

  $terms = get_terms([
    'taxonomy' => 'product_taste',
    'hide_empty' => false,
    'meta_query' => [
      [
        'key' => 'вкусы_тип',
        'value' => $current_type['value']
      ]
    ]
  ]);

  foreach ($terms as $term) {
    $tastes[$term->name] = [
      'ID' => $term->term_id,
      'name' => $term->name,
      'content' => $term->description,
      'image' => get_field('изображение', $term),
    ];
  }


  return $tastes;
}

function attributes_cakes_tabs()
{
  $data['tabs'] = [];

  if ($tastes = get_product_tastes()) {
    $tastes = array_reduce($tastes, function ($initial, $item) {
      $is_active = empty($initial) ? 'active' : '';
      $html = '
          <div class="card__add-wrap taste taste-id-%s %s">
            <div class="card__add-image">%s</div>
            <div class="card__add-text">%s</div>
          </div>';

      $img_html = wp_get_attachment_image($item['image'], 'thumbnail', false, ['class' => 'card__add-image-i']);

      return $initial . sprintf($html, $item['ID'], $is_active, $img_html, $item['content']);
    }, '');

    $data['tabs'][] = [
      'name' => 'Смак',
      'content' => $tastes
    ];
  }

  if ($info = get_field('информация_декор')) {
    $data['tabs'][] = [
        'name' => 'Декор'
      ] + $info;
  }

  View::render('product-single-tabs', $data);
}

function attributes_cakes_taste()
{
  if ($tastes = get_product_tastes()) {
    $data = [
      'field' => array_column($tastes, 'name', 'ID'),
      'label' => 'Смак',
      'tooltip' => 'taste',
      'input_name' => 'product_attr_taste',
    ];

    View::render('product-content-attribute', $data);
  }
}

function attributes_cakes_decor()
{
  $data = [
    'field' => Helper::format_currency('', get_field('декор')),
    'label' => 'Декор',
    'input_name' => 'decor',
  ];

  View::render('product-content-attribute', $data);
}

function attributes_min_order_date() {
  $cook_days = get_field( 'cook_days' ) ?: 0;

  $data = [
    'field'      => date( 'd.m.Y', strtotime( '+' . (  $cook_days + 1 ) . ' DAYS' ) ),
    'label'      => 'Дата замовлення',
    'input_name' => 'product_attr_delivery_date'
  ];

  if ( Helper::get_setting( 'include_due_date', false ) ) {
    View::render( 'product-content-attribute', $data );
  }
}

function attributes_candies_qty_range(): array {
  return range( 1, 20 );
}

function attributes_candies_qty() {
  $data = [
    'field'      => attributes_candies_qty_range(),
    'label'      => 'Кількість коробок',
    'input_name' => 'product_quantity',
  ];

  View::render( 'product-content-attribute', $data );
}

function attributes_candies_count()
{
  $data = [
    'field' => get_field('количество_конфет_в_коробке'),
    'label' => 'Кількість цукерок',
    'input_name' => 'candies_count',
  ];

  View::render('product-content-attribute', $data);
}

function get_weight($id = null)
{
  $field = 'вес';

  return get_field($field, $id);
}

function format_weight( int $id, $qty ): string {
  $current_type  = Product::get_parent_product_category( 'slug', $id );
  $weight = '';

  if ( in_array( $current_type, [ 'candies', 'tistechko' ], true ) ) {
    return sprintf( 'К-сть: %d шт.', $qty );
  }


    $label = 'Вага, кг';

    $weights = get_weight( $id );


    foreach ( $weights as $val ) {
      $val_normalize = get_product_price_by_weight( $val );

      if ($val_normalize == $qty) {
        $weight = $val_normalize;
        break;
      }

    }


  return "$label: $weight";
}

function attributes_cakes_weight()
{
  $data = [
    'field' => get_weight(),
    'label' => 'Вага',
    'tooltip' => 'weight',
    'input_name' => 'product_quantity',
  ];

  View::render('product-content-attribute', $data);
}

function attributes_candies_weight()
{
  $data = [
    'field' => get_weight(),
    'label' => 'Вага',
    'input_name' => 'product_quantity',
  ];

  View::render('product-content-attribute', $data);
}

function attributes_candies_termin()
{
  $data = [
    'field' => get_field('срок_годности'),
    'label' => 'Термін придатності'
  ];

  View::render('product-content-attribute', $data);
}

function attributes_candies_boxsize()
{
  $data = [
    'field' => get_field('размер_коробки'),
    'label' => 'Розмір коробки',
  ];

  View::render('product-content-attribute', $data);
}

function attributes_candies_boxinner()
{

}

function single_buy_btn()
{
  get_template_part('templates/product', 'single-buy_btn');
}

function single_after_content()
{

}

function single_after_preview_image()
{
  // if ((Product::is_product_in_term('candies') || Product::is_product_in_term('tistechko')) && have_rows('блок_описания_подарка', 'option') && !get_field('disable_gift')) {
    get_template_part('templates/product', 'content-gift');
  // }

  get_template_part('templates/product', 'content-more-text');
}

add_action('simplebasket/related_products', 'single_related_products');
function single_related_products()
{
  $args = [
    'post_type' => Product::get_post_type(),
    'posts_per_page' => 12,
    'meta_key' => 'allow',
    'meta_value' => 1
  ];

  if (is_single()) {
    $args += [
      'post__not_in' => [get_the_ID()],
      //'product_category' => Product::get_parent_product_category('slug')
    ];
  }

  if ($posts = get_posts($args)) {
    View::render('product-single-related', ['posts' => $posts]);
  }
  wp_reset_postdata();
}

function get_product_price_by_weight($qty)
{
  return mb_strpos($qty, 'гр') ? 0.001 * (float)$qty : (float)$qty;
}


add_filter('simplebasket__qty_normalize', 'get_product_price_by_weight');

add_action('simplebasket/cart/shipping_methods', '\\SimpleBasket\\Shipping\\Api::load_active_plugins');

add_shortcode('corporate_form', function () {
  return View::returnView('part-main-form', ['form' => View::returnView('corporate-form')]);
});

add_shortcode('design_form', function () {
  return View::returnView('part-main-form', ['form' => View::returnView('design-form')]);
});

//add_filter('simplebasket_delivery_price', '__return_zero');

//add_filter('simplebasket/allow_sending_emails', '__return_false');

add_action('template_redirect', function () {
  if ((is_single() && get_post_type() === Product::get_post_type()) || isset($_POST['data'], $_POST['signature'])) {
    define('DONOTCACHEPAGE', true);
  }
});

add_filter('simplebasket_product/display_price', function ($price) {
  if (Product::get_parent_product_category('slug') === 'cakes') {
    $price = $price . ' / 1 кг';
  }

  return $price;
});

add_filter('acf/load_field/name=dostavka', function ($field) {
  $choices = \SimpleBasket\Cart::get_instance()->get_delivery_methods();
  $field['choices'] = array_combine(array_keys($choices), array_column($choices, 'title'));

  return $field;
});

add_action('admin_init', function () {
  remove_action('admin_notices', 'update_nag', 3);
});

add_filter('acf/load_value/name=form', 'form_html');
add_filter('acf/load_value/name=form_submit', 'form_html');
function form_html($html)
{
  return preg_replace('@<h\d[^>]*>(.+)</h\d>@u', '<h2 class="question-box__title">$1</h2>', $html);
}

remove_action( 'wp_head', 'wp_site_icon', 99 );
add_action( 'wp_head', 'wp_site_icon', 0 );

add_filter( 'wpseo_breadcrumb_links', function ( $crumbs ) {
  if ( is_singular( "product" ) || is_tax('product_category') ) {
    unset( $crumbs[1] );
  }

  return $crumbs;
} );

/**
 * Фірмовий пакет і дані подарунка тепер обробляє плагін cb-black-friday
 * (ціна пакета береться з налаштувань на сервері, а не з форми; дані отримувача — CandyBuffet\BlackFriday\Checkout).
 * Попередня версія коду — _backup_bf_20261123/.../functions.php.
 */

/**
 * Після створення замовлення чистимо кошик у сесії/кукі,
 * щоб наступне замовлення стартувало «з нуля».
 */
add_action('simplebasket_order/new_order', function () {
    // сесія
    if (class_exists(Helper::class)) {
        Helper::update_session_value(Cart::ITEMS, []);
    } else {
        if (!session_id()) @session_start();
        $_SESSION[Cart::ITEMS] = [];
    }

    // cookie глобального кошика (якщо використовується)
    $cookie = 'simplebasket_globalcart_' . COOKIEHASH;
    if (isset($_COOKIE[$cookie]) && !headers_sent()) {
        setcookie($cookie, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        unset($_COOKIE[$cookie]);
    }
}, 100);

// Скидання DECOR і метабокс «Додаткові опції» замінено плагіном cb-black-friday (метабокс «Black Friday: розрахунок і подарунок»).

if ( ! function_exists( 'akweb_ga4_view_item' ) ) {
    function akweb_ga4_view_item( $product ) {
        if ( function_exists( 'akweb_ga4_view_item_impl' ) ) {
            akweb_ga4_view_item_impl( $product );
        }
    }
}
if ( ! function_exists( 'akweb_ga4_view_item_list' ) ) {
    function akweb_ga4_view_item_list( array $products, $list_id = null, $list_name = null ) {
        if ( function_exists( 'akweb_ga4_view_item_list_impl' ) ) {
            akweb_ga4_view_item_list_impl( $products, $list_id, $list_name );
        }
    }
}
if ( ! function_exists( 'akweb_ga4_select_item_attrs' ) ) {
    function akweb_ga4_select_item_attrs( $product ) {
        if ( function_exists( 'akweb_ga4_select_item_attrs_impl' ) ) {
            akweb_ga4_select_item_attrs_impl( $product );
        }
    }
}
if ( ! function_exists( 'akweb_ga4_select_item_script' ) ) {
    function akweb_ga4_select_item_script() {
        if ( function_exists( 'akweb_ga4_select_item_script_impl' ) ) {
            akweb_ga4_select_item_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_add_to_cart_script' ) ) {
    function akweb_ga4_add_to_cart_script( $product ) {
        if ( function_exists( 'akweb_ga4_add_to_cart_script_impl' ) ) {
            akweb_ga4_add_to_cart_script_impl( $product );
        }
    }
}
if ( ! function_exists( 'akweb_ga4_remove_from_cart_script' ) ) {
    function akweb_ga4_remove_from_cart_script() {
        if ( function_exists( 'akweb_ga4_remove_from_cart_script_impl' ) ) {
            akweb_ga4_remove_from_cart_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_begin_checkout_script' ) ) {
    function akweb_ga4_begin_checkout_script() {
        if ( function_exists( 'akweb_ga4_begin_checkout_script_impl' ) ) {
            akweb_ga4_begin_checkout_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_add_payment_info_script' ) ) {
    function akweb_ga4_add_payment_info_script() {
        if ( function_exists( 'akweb_ga4_add_payment_info_script_impl' ) ) {
            akweb_ga4_add_payment_info_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_purchase_helper_script' ) ) {
    function akweb_ga4_purchase_helper_script() {
        if ( function_exists( 'akweb_ga4_purchase_helper_script_impl' ) ) {
            akweb_ga4_purchase_helper_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_purchase_on_modal_script' ) ) {
    function akweb_ga4_purchase_on_modal_script() {
        if ( function_exists( 'akweb_ga4_purchase_on_modal_script_impl' ) ) {
            akweb_ga4_purchase_on_modal_script_impl();
        }
    }
}
if ( ! function_exists( 'akweb_ga4_checkout_ecommerce_inline' ) ) {
    function akweb_ga4_checkout_ecommerce_inline() {
        if ( function_exists( 'akweb_ga4_build_checkout_ecommerce' ) ) {
            $ec = akweb_ga4_build_checkout_ecommerce();
            if ( $ec ) {
                ?>
                <script>
                  window.akwebGa4CheckoutEcommerce = <?php echo wp_json_encode( $ec ); ?>;
                </script>
                <?php
            }
        }
    }
}
/**
 * Після створення замовлення пишемо його ID в куку для GA4 purchase.
 */
add_action('simplebasket_order/new_order', function ($order) {

    $order_id = (is_object($order) && isset($order->ID))
        ? (int) $order->ID
        : (int) $order;

    if ($order_id <= 0) {
        return;
    }

    if (headers_sent()) {
        return; 
    }

    $cookie_name  = 'akweb_ga4_last_order_id';
    $cookie_value = (string) $order_id;

    $path   = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
    $domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';

    setcookie(
        $cookie_name,
        $cookie_value,
        time() + 3600,          
        $path,
        $domain,
        is_ssl(),
        false                   
    );
}, 5);

/**
 * Зберігання UTM-міток у браузері (Cookie) на 90 днів з перезаписом.
 */
add_action('wp_footer', function () {
    ?>
    <script>
        (function() {
            const utmParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];
            const urlParams = new URLSearchParams(window.location.search);
            const cookieName = "utm_data";
            const expiryDays = 90;

            let hasNewUtm = false;
            const newData = {};

            // Перевіряємо наявність параметрів у URL
            utmParams.forEach(function(param) {
                if (urlParams.has(param)) {
                    newData[param] = urlParams.get(param);
                    hasNewUtm = true;
                }
            });

            // Якщо є нові мітки — створюємо/перезаписуємо куку
            if (hasNewUtm) {
                const d = new Date();
                d.setTime(d.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
                const expires = "expires=" + d.toUTCString();
                
                // JSON рядок з новими даними
                const cookieValue = encodeURIComponent(JSON.stringify(newData));
                document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
            }
        })();
    </script>
    <?php
});
/**
 * Збереження UTM-міток з Cookie в мета-поля замовлення SimpleBasket
 */
add_action('simplebasket_order/new_order', function ($order) {
    $order_id = (is_object($order) && isset($order->ID)) ? (int)$order->ID : (int)$order;

    if ($order_id <= 0) {
        return;
    }

    if (isset($_COOKIE['utm_data'])) {

        $utm_data = json_decode(stripslashes($_COOKIE['utm_data']), true);

        if (is_array($utm_data)) {
            $utm_keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];
            
            foreach ($utm_keys as $key) {
                if (!empty($utm_data[$key])) {
                    // Зберігаємо кожну знайдену мітку у відповідне мета-поле
                    update_post_meta($order_id, $key, sanitize_text_field($utm_data[$key]));
                }
            }
        }
    }
}, 15, 1);
/**
 * Вивід UTM-міток в адмін-панелі замовлення
 */
add_action('add_meta_boxes', function() {
    add_meta_box('ak_order_utms', 'Джерело замовлення (UTM)', function($post) {
        $utm_keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];
        $rows = [];
        $has_utms = false;

        foreach ($utm_keys as $key) {
            $val = get_post_meta($post->ID, $key, true);
            if (!empty($val)) {
                $rows[$key] = $val;
                $has_utms = true;
            }
        }

        if (!$has_utms) {
            echo '<p>Немає даних про джерело переходу.</p>';
            return;
        }

        echo '<div class="simplebasket_customer_address">
                <table class="form-table">
                    <tbody>';
        foreach ($rows as $k => $v) {
            echo '<tr>
                    <th scope="row" style="width: 120px;">' . esc_html($k) . '</th>
                    <td><strong>' . esc_html($v) . '</strong></td>
                  </tr>';
        }
        echo '      </tbody>
                </table>
              </div>';
    }, 'simplebasket_order', 'side', 'default'); // 'side' виведе блок у правій колонці
});
/**
 * Додаємо новий стовпчик "UTM Мітки" у список замовлень SimpleBasket
 */
add_filter('manage_simplebasket_order_posts_columns', function($columns) {
    $new_columns = [];
    
    foreach ($columns as $key => $title) {
        // Вставляємо новий стовпчик перед датою
        if ($key === 'date') {
            $new_columns['utm_tags'] = 'UTM Мітки';
        }
        $new_columns[$key] = $title;
    }
    
    // Якщо стовпчика 'date' немає, просто додаємо в кінець
    if (!isset($new_columns['utm_tags'])) {
        $new_columns['utm_tags'] = 'UTM Мітки';
    }
    
    return $new_columns;
});

/**
 * Виводимо дані UTM-міток у створений стовпчик
 */
add_action('manage_simplebasket_order_posts_custom_column', function($column_key, $post_id) {
    if ($column_key === 'utm_tags') {
        $utm_keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];
        $output = [];

        foreach ($utm_keys as $key) {
            $val = get_post_meta($post_id, $key, true);
            if (!empty($val)) {
                $output[] = '<strong>' . esc_html($key) . ':</strong> ' . esc_html($val);
            }
        }

        if (!empty($output)) {
            echo implode('<br>', $output);
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}, 10, 2);
