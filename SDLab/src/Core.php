<?php

namespace SDLab;

use InvalidArgumentException;use WP_Error;

class Core
{

    private static $_instance;
    private $_settings_prefix = 'sdlab';
    private $_is_init = false;

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    static function instance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    function init()
    {
        if (self::$_instance && $this->_is_init) {
            return self::$_instance;
        }

        include_once dirname(__DIR__ ). '/includes/define.php';
        include_once dirname(__DIR__ ). '/includes/hooks.php';

        new Translator;

        if (function_exists('acf_add_options_page')) {

            acf_add_options_page(array(
                'page_title' => 'Основні налаштування',
                'menu_title' => 'Основні налаштування',
                'menu_slug'  => $this->_get_settings_prefix() . '-theme-settings',
                'capability' => 'edit_posts',
                'redirect'   => false,
            ));

            /*acf_add_options_sub_page(array(
              'page_title' => 'Доставка',
                'menu_title' => 'Доставка',
                'parent_slug'  => $this->_get_settings_prefix() . '-theme-settings',
                'capability' => 'edit_posts',
                'redirect'   => false
            ));*/
        }

        add_action('after_setup_theme', [$this, 'after_setup_theme']);

        return $this;
    }

    private function _get_settings_prefix()
    {
        return $this->_settings_prefix;
    }

    function do_action($tag) {
        do_action($this->_get_settings_prefix() . '_' . $tag);
    }

    function after_setup_theme() {
        add_theme_support( 'custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => array( 'site-title', 'site-description' ),
        ) );
    }

    function get_settings($name) {
        return get_theme_mod($this->_get_settings_prefix() . '_' . $name);
    }

    function remove_default_image_sizes($sizes)
    {
        unset($sizes['medium_large']);
        unset($sizes['medium']);
        unset($sizes['large']);

        return $sizes;
    }

    // include custom jQuery

    function disable_plugin_updates($value)
    {
        $plugin_path = 'plugins/wordpress-ecommerce';

        if (isset($value->response[$plugin_path])) {
            unset($value->response[$plugin_path]);
        }

        if (isset($value->updates['translations'])) {
            unset($value->updates['translations']);
        }

        return $value;
    }

    function rest_authentication_errors($result)
    {
        if (!empty($result)) {
            return $result;
        }
        if (!is_user_logged_in()) {
            return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
        }
        return $result;
    }

    function include_custom_jquery()
    {
        if (is_admin()) {
            return;
        }

        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', array(), null, true);
    }

    function my_login_logo()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id , 'full');

        if ($logo) ?>
            <style>
                #login h1 a,
                .login h1 a {
                    background-image: url(<?= esc_url($logo[0]) ?>);
                    padding-bottom: 60px;
                    width: 100%;
                    height: auto;
                    background-size: contain;
                }
            </style>
<? }

    private function register_action() {
        $args = func_get_args();

        if (count($args) < 2)
           {throw new InvalidArgumentException('require action & args');}

        $action = $this->_get_settings_prefix() . '_' . array_shift($args);
        $func_to_add = [$this, array_shift($args)];

         if (!is_callable($func_to_add))
           {throw new InvalidArgumentException('Function is not callable');}

        add_action($action, $func_to_add, $priority = 10, $accepted_args = count($args) ?? 1);
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}
