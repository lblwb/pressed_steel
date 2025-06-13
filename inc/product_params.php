<?php

// Register ACF Options Page
//if (function_exists('acf_add_options_page')) {
//    acf_add_options_page(array(
//        'page_title' => 'Параметры товаров',
//        'menu_title' => 'Параметры товаров',
//        'menu_slug'  => 'product-parameters',
//        'capability' => 'edit_posts',
//        'redirect'   => false
//    ));
//}

// Populate parameter choices in the product repeater field
add_filter('acf/load_field/name=parameter', function($field) {
    $parameters = get_field('product_parameters', 'option'); // Repeater field on options page
    if ($parameters) {
        foreach ($parameters as $param) {
            $field['choices'][$param['parameter_name']] = $param['parameter_name'];
        }
    }
    return $field;
});

// AJAX handler to load possible values for the 'value' field
add_action('wp_ajax_load_parameter_values', 'load_parameter_values_callback');
function load_parameter_values_callback() {
    $parameter = isset($_GET['parameter']) ? sanitize_text_field($_GET['parameter']) : '';
    $parameters = get_field('product_parameters', 'option');
    $values = array();

    if ($parameters) {
        foreach ($parameters as $param) {
            if ($param['parameter_name'] === $parameter) {
                $values = array_map('trim', explode(',', $param['possible_values']));
                $values = array_combine($values, $values);
                break;
            }
        }
    }

    wp_send_json($values);
    wp_die();
}

// Save parameter-value pairs as individual meta keys
add_action('acf/save_post', function($post_id) {
    if (get_post_type($post_id) !== 'product') return;

    // Get repeater field data
    $parameters = get_field('product_parameters_repeater', $post_id);

    // Clear existing parameter meta keys to avoid duplicates
    $existing_meta = get_post_meta($post_id);
    foreach ($existing_meta as $key => $value) {
        if (strpos($key, 'parameter_') === 0) {
            delete_post_meta($post_id, $key);
        }
    }

    // Save new parameter-value pairs
    if ($parameters) {
        foreach ($parameters as $param) {
            $meta_key = 'parameter_' . sanitize_title($param['parameter']);
            update_post_meta($post_id, $meta_key, $param['value']);
        }
    }
});

// Modify product query for filtering
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_category())) {
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            $meta_query = array('relation' => 'AND');
            foreach ($_GET['filter'] as $param => $value) {
                $meta_query[] = array(
                    'key'     => 'parameter_' . sanitize_title($param),
                    'value'   => $value,
                    'compare' => '='
                );
            }
            $query->set('meta_query', $meta_query);
        }
    }
});

// Display filter interface in the catalog
add_action('woocommerce_before_shop_loop', 'display_product_filters', 20);

function display_product_filters() {
    $current_category = is_product_category() ? get_queried_object() : null;
    $selected_params = $current_category ? get_field('category_parameters', 'product_cat_' . $current_category->term_id) : [];

    $all_params = get_field('product_parameters', 'option');
    if ($all_params) {
        echo '<div class="product-filters">';
        foreach ($all_params as $param) {
            if (!$selected_params || in_array($param['parameter_name'], $selected_params)) {
                echo '<div class="filter-section">';
                echo '<h3>' . esc_html($param['parameter_name']) . '</h3>';
                $values = array_map('trim', explode(',', $param['possible_values']));
                foreach ($values as $value) {
                    $current_filters = isset($_GET['filter']) && is_array($_GET['filter']) ? $_GET['filter'] : [];
                    $new_filters = $current_filters;
                    $new_filters[$param['parameter_name']] = $value;
                    $url = add_query_arg('filter', $new_filters);
                    $active = isset($current_filters[$param['parameter_name']]) && $current_filters[$param['parameter_name']] === $value;
                    echo '<a href="' . esc_url($url) . '" class="' . ($active ? 'active' : '') . '">' . esc_html($value) . '</a> ';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
}

// Display product parameters on single product page
add_action('woocommerce_single_product_summary', 'display_product_parameters', 25);

function display_product_parameters() {
    global $product;

    // Get repeater field data
    $parameters = get_field('product_parameters_repeater', $product->get_id());

    if ($parameters) {
        echo '<div class="product-parameters">';
        echo '<h3>Параметры товара</h3>';
        echo '<ul>';
        foreach ($parameters as $param) {
            $parameter_name = esc_html($param['parameter']);
            $value = esc_html($param['value']);
            if ($parameter_name === 'Длина') {
                $value .= ' мм'; // Добавляем единицу измерения для длины
            }
            echo '<li><strong>' . $parameter_name . ':</strong> ' . $value . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}



add_action('rest_api_init', function () {
    register_rest_route('pst/v1', '/handle-add-cart', [
        'methods' => WP_REST_Server::CREATABLE, // Эквивалент 'POST'
        'callback' => 'pressedsteel_add_to_cart',
        'permission_callback' => '__return_true', // Доступ для всех
    ]);
});
function pressedsteel_add_to_cart(WP_REST_Request $request) {
    try {
        // Ensure WooCommerce is loaded
        if (!function_exists('WC')) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'WooCommerce is not loaded'
            ], 500);
        }

        // Initialize WooCommerce session
        if (!WC()->session) {
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }

        // Initialize cart if not already set
        if (!WC()->cart) {
            WC()->cart = new WC_Cart();
        }

        // Initialize customer if not already set
        if (!WC()->customer) {
            WC()->customer = new WC_Customer(get_current_user_id());
        }

        $params = $request->get_json_params();
        $product_id = isset($params['product_id']) ? absint($params['product_id']) : 0;
        $quantity = isset($params['quantity']) ? absint($params['quantity']) : 1;
        $attributes = isset($params['attributes']) && is_array($params['attributes']) ? $params['attributes'] : [];

        // Validate product ID
        if (!$product_id) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Не получилось добавить!'
            ], 400);
        }

        // Validate product
        $product = wc_get_product($product_id);
        if (!$product) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Ошибка при добавлении товара!'
            ], 404);
        }

        // Add to cart with attributes as variation data
        $cart_id = WC()->cart->add_to_cart($product_id, $quantity, 0, $attributes, []);

        if ($cart_id) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Товар добавлен в корзину',
                'cart_id' => $cart_id
            ], 200);
        } else {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to add product to cart. Ensure attributes match a valid product variation.'
            ], 400);
        }
    } catch (Exception $e) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * CART
 */


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
                'product_id' => base64_encode($product->get_id()),
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

        if ($quantity < 1) {
            $removed = WC()->cart->remove_cart_item($key);
            if (!$removed) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Failed to remove cart item'
                ], 400);
            }

//            return new WP_REST_Response([
//                'success' => false,
//                'message' => 'Quantity cannot be negative'
//            ], 400);
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
//        if ($quantity === 0) {
//            $removed = WC()->cart->remove_cart_item($key);
//            if (!$removed) {
//                return new WP_REST_Response([
//                    'success' => false,
//                    'message' => 'Failed to remove cart item'
//                ], 400);
//            }
//        }

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

//        var_dump($key);

        if (!$key) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid cart item key'
            ], 400);
        }

        $removed = WC()->cart->remove_cart_item($key);
        WC()->cart->remove_cart_item($key);

        return new WP_REST_Response([
            'success' => $removed,
            'del_key' => $key,
            'cart' => WC()->cart->get_cart(),
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


require_once "pst_order_handler.php";

/**
 *
 */

add_action('rest_api_init', function () {
    $orderHandler = new PressedSteelOrderHandler();

    // Get cart data
    register_rest_route('pst/v1', '/cart', [
        'methods' => WP_REST_Server::READABLE, // GET
        'callback' => 'pst_get_cart_data',
        'permission_callback' => '__return_true', // Public access for viewing cart
    ]);

    // Update cart item quantity
    register_rest_route('pst/v1', '/cart/submit_cart', [
        'methods' => WP_REST_Server::CREATABLE, // POST
        'callback' => [$orderHandler, 'handle_request'],
        'permission_callback' => '__return_true', // Public access for viewing cart
//            'permission_callback' => function () {
//                return check_ajax_referer('wc_cart_nonce', 'nonce', false);
//            }
    ]);

    // Update cart item quantity
    register_rest_route('pst/v1', '/cart/update', [
        'methods' => WP_REST_Server::CREATABLE, // POST
        'callback' => 'pst_update_cart',
        'permission_callback' => '__return_true', // Public access for viewing cart
    ]);

    // Remove cart item
    register_rest_route('pst/v1', '/cart/remove', [
        'methods' => WP_REST_Server::CREATABLE, // POST
        'callback' => 'pst_remove_cart_item',
        'permission_callback' => '__return_true', // Public access for viewing cart
    ]);

    // Clear cart
    register_rest_route('pst/v1', '/cart/clear', [
        'methods' => WP_REST_Server::CREATABLE, // POST
        'callback' => 'pst_clear_cart',
        'permission_callback' => '__return_true', // Public access for viewing cart
    ]);
});