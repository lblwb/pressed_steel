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



function app_cart_init() {
    add_action('rest_api_init', function () {
        // Get cart data
        register_rest_route('pst/v1', '/cart', [
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => 'pst_get_cart_data',
            'permission_callback' => '__return_true', // Public access for viewing cart
        ]);

        // Update cart item quantity
        register_rest_route('pst/v1', '/cart/update', [
            'methods' => WP_REST_Server::CREATABLE, // POST
            'callback' => 'pst_update_cart',
            'permission_callback' => '__return_true', // Public access for viewing cart
//            'permission_callback' => function () {
//                return check_ajax_referer('wc_cart_nonce', 'nonce', false);
//            }
        ]);

        // Remove cart item
        register_rest_route('pst/v1', '/cart/remove', [
            'methods' => WP_REST_Server::CREATABLE, // POST
            'callback' => 'pst_remove_cart_item',
            'permission_callback' => '__return_true', // Public access for viewing cart
//            'permission_callback' => function () {
//                return check_ajax_referer('wc_cart_nonce', 'nonce', false);
//            }
        ]);

        // Clear cart
        register_rest_route('pst/v1', '/cart/clear', [
            'methods' => WP_REST_Server::CREATABLE, // POST
            'callback' => 'pst_clear_cart',
            'permission_callback' => '__return_true', // Public access for viewing cart
//            'permission_callback' => function () {
//                return check_ajax_referer('wc_cart_nonce', 'nonce', false);
//            }
        ]);
    });
}

// Initialize WooCommerce objects
function pst_init_woocommerce() {
    if (!function_exists('WC')) {
        return new WP_Error('woocommerce_not_loaded', 'WooCommerce is not loaded', ['status' => 500]);
    }

    // Initialize session
    if (!WC()->session) {
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
    }

    // Initialize cart
    if (!WC()->cart) {
        WC()->cart = new WC_Cart();
    }

    // Initialize customer
    if (!WC()->customer) {
        WC()->customer = new WC_Customer(get_current_user_id());
    }

    return true;
}

// Get cart data
function pst_get_cart_data(WP_REST_Request $request) {
    try {
        $init = pst_init_woocommerce();
        if (is_wp_error($init)) {
            return new WP_REST_Response($init->get_error_data(), $init->get_error_code());
        }

        $result = [];
        foreach (WC()->cart->get_cart() as $key => $item) {
            $product = $item['data'];
            $result[] = [
                'key' => $key,
                'name' => $product->get_name(),
                'desc' => $product->get_short_description(),
                'image' => get_the_post_thumbnail_url($product->get_id(), 'thumbnail') ?: '',
                'quantity' => $item['quantity'],
                'price' => (float) $product->get_price(),
                'attributes' =>$item['variation'],
            ];
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $result,
            'message' => 'Cart data retrieved successfully'
        ], 200);
    } catch (Exception $e) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

// Update cart item quantity
function pst_update_cart(WP_REST_Request $request) {
    try {
        $init = pst_init_woocommerce();
        if (is_wp_error($init)) {
            return new WP_REST_Response($init->get_error_data(), $init->get_error_code());
        }

        $params = $request->get_json_params();
        $key = isset($params['key']) ? sanitize_text_field($params['key']) : '';
        $quantity = isset($params['qty']) ? absint($params['qty']) : 0;

        // Validate inputs
        if (empty($key)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Cart item key is required'
            ], 400);
        }

        if ($quantity < 0) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Quantity cannot be negative'
            ], 400);
        }

        // Check if cart item exists
        $cart_contents = WC()->cart->get_cart();
        if (!isset($cart_contents[$key])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid cart item key: ' . $key
            ], 400);
        }

        // Update quantity
        $updated = WC()->cart->set_quantity($key, $quantity, true); // true to refresh totals

        // If quantity is 0, remove the item
        if ($quantity === 0) {
            $removed = WC()->cart->remove_cart_item($key);
            if (!$removed) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Failed to remove cart item'
                ], 400);
            }
        }

        // Recalculate cart totals to ensure persistence
        WC()->cart->calculate_totals();

        // Verify update
        $current_quantity = isset(WC()->cart->get_cart()[$key]) ? WC()->cart->get_cart()[$key]['quantity'] : 0;

        return new WP_REST_Response([
            'success' => $updated && ($quantity === 0 || $current_quantity === $quantity),
            'message' => $quantity === 0 ? 'Позиция  удалена из корзины' : 'Корзина обновлена',
            'data' => [
                'key' => $key,
                'quantity' => $current_quantity
            ]
        ], 200);
    } catch (Exception $e) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
// Remove cart item
function pst_remove_cart_item(WP_REST_Request $request) {
    try {
        $init = pst_init_woocommerce();
        if (is_wp_error($init)) {
            return new WP_REST_Response($init->get_error_data(), $init->get_error_code());
        }

        $params = $request->get_json_params();
        $key = isset($params['key']) ? sanitize_text_field($params['key']) : '';

        if (!$key) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid cart item key'
            ], 400);
        }

        $removed = WC()->cart->remove_cart_item($key);

        return new WP_REST_Response([
            'success' => $removed,
            'message' => $removed ? 'Item removed from cart' : 'Failed to remove item'
        ], 200);
    } catch (Exception $e) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

// Clear cart
function pst_clear_cart(WP_REST_Request $request) {
    try {
        $init = pst_init_woocommerce();
        if (is_wp_error($init)) {
            return new WP_REST_Response($init->get_error_data(), $init->get_error_code());
        }

        WC()->cart->empty_cart();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ], 200);
    } catch (Exception $e) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

app_cart_init();
//add_action('init', 'app_cart_init');

require_once(__DIR__ . "/inc/mobile_full_menu.php");
require_once(__DIR__ . "/inc/product_params.php");
require_once(__DIR__ . "/inc/action_form_cta.php");
