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