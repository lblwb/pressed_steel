<?php

function favicon_link()
{
    echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
}

add_action('wp_head', 'favicon_link');

// Disable REST API endpoints
//add_filter('rest_endpoints', function() {
//    return [];
//});

// Disable REST API HTTP header link
//remove_action('template_redirect', 'rest_output_link_header', 11);


/**
 * Basic setup style & scripts
 * @return void
 */
function pressed_steel_setup()
{
    // Не админ часть
    if (!is_admin()) {
        wp_enqueue_script("notify", get_stylesheet_directory_uri() . '/assets/js/notify.js', array(), null, true);
        wp_enqueue_script("axios", get_stylesheet_directory_uri() . '/assets/js/axios.js', array(), null, true);
        wp_enqueue_script("vue", get_stylesheet_directory_uri() . '/assets/js/vue.global.js', array(), null, false);
        //
        wp_enqueue_style("swpr-sldr-bnd-sty", get_stylesheet_directory_uri() . '/assets/css/slider/swiper-bundle.min.css');
        wp_enqueue_script("swpr-sldr-bnd-scr", get_stylesheet_directory_uri() . '/assets/js/slider/swiper-bundle.min.js');
        //
        wp_enqueue_style("style-main", get_stylesheet_directory_uri() . '/assets/css/main.css');
        wp_enqueue_script("script-main", get_stylesheet_directory_uri() . '/assets/js/main.js');
        //
        wp_localize_script('script-main', 'myAjax', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_nonce_action'),
        ]);
    } else {
        // Добавить поддержку редактора
//        add_theme_support('editor-styles');
//        add_editor_style('assets/css/editor.css');
    }
}

add_action('wp_enqueue_scripts', 'pressed_steel_setup');

/**
 * Оптимизация темы PressedSteel: удаление лишнего, совместимость с FSE + WooCommerce
 */

function optimize_pressed_steel_theme(): void
{
    // 🔌 Отключаем emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // 🔒 Удаление лишнего из <head>
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);

    // 🛡 Отключаем XML-RPC
    add_filter('xmlrpc_enabled', '__return_false');

    // 🚫 Отключаем wp-embed
//    add_action('wp_footer', function () {
//        wp_deregister_script('wp-embed');
//    });

    // 🚫 Удаляем jQuery Migrate (если не нужен)
    add_action('wp_default_scripts', function ($scripts) {
        if (!is_admin() && isset($scripts->registered['jquery'])) {
            $scripts->registered['jquery']->deps = array_diff(
                $scripts->registered['jquery']->deps,
                ['jquery-migrate']
            );
        }
    });

    // 🛍 WooCommerce: убираем стили, если используем кастомные
//    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // 🛒 WooCommerce: отключаем скрипты на лишних страницах
//    add_action('wp_enqueue_scripts', function () {
//        if (!is_woocommerce() && !is_cart() && !is_checkout()) {
//            wp_dequeue_style('woocommerce-general');
//            wp_dequeue_script('wc-cart-fragments');
//        }
//    }, 99);

    // ✅ Подключаем поддержку WooCommerce и галерей
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}

add_action('after_setup_theme', 'optimize_pressed_steel_theme');


function get_wc_category_title_shortcode()
{
    if (is_product_category()) {
        $term = get_queried_object();
        return esc_html($term->name);
    } else {
        return "Магазин";
    }

    return '';
}

add_shortcode('product_category_title', 'get_wc_category_title_shortcode');


// functions.php или custom plugin
add_shortcode('custom_cart', 'render_custom_cart');
function render_custom_cart()
{
    ob_start();
    include get_template_directory() . '/shortcodes/cart-template.php';
    return ob_get_clean();
}

// functions.php или custom plugin
add_shortcode('custom_product_cat', 'render_custom_product_cat');
function render_custom_product_cat()
{
    ob_start();
    include get_template_directory() . '/shortcodes/product-cat-template.php';
    return ob_get_clean();
}


// functions.php или custom plugin
add_shortcode('custom_mob_navbar', 'render_custom_mob_navbar');
function render_custom_mob_navbar()
{
    ob_start();
    include get_template_directory() . '/shortcodes/mobile-full-menu.php';
    return ob_get_clean();
}


// Initialize WooCommerce objects
function pst_init_woocommerce()
{

    if (!is_admin()) {
        if (!function_exists('WC')) {
            return new WP_Error('woocommerce_not_loaded', 'WooCommerce is not loaded', ['status' => 500]);
        }

        if (null === WC()->session) {
            WC()->initialize_session();
        }

        if (null === WC()->cart) {
            WC()->cart = new WC_Cart();
            WC()->cart->get_cart(); // триггер загрузки
        }

        // Initialize customer
        if (!WC()->customer) {
            WC()->customer = new WC_Customer(get_current_user_id());
        }

        return true;
    }
}

add_action('init', 'pst_init_woocommerce');

function add_yandex_metrika() {
    if ( ! is_admin() ) {
        ?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]function(){(m[i].a=m[i].a[]).push(arguments)};m[i].l=1*new Date();for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");ym(103163798, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/103163798" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
    <?php
    }
}
add_action('wp_footer', 'add_yandex_metrika');

require_once(__DIR__ . "/inc/mobile_full_menu.php");
require_once(__DIR__ . "/inc/product_params.php");
require_once(__DIR__ . "/inc/action_form_cta.php");
