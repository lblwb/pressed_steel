<?php
/**
 * Title: Категории товаров с миниатюрами
 * Slug: pressed_steel/category-shop
 * Description: Выводит категории товаров WooCommerce с миниатюрами и ссылками.
 * Categories: pressed_steel/shop
 * Inserter: true
 */

$product_categories = get_terms([
    'taxonomy' => 'product_cat',
//    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => true,
]);

if (!empty($product_categories) && !is_wp_error($product_categories)) {
?>

<div class="catalogCategories">
    <div class="catalogCategoriesWrapper" style="">
        <?php

        function get_first_product_url_by_category_id_wc( $category_id ) {
            $products = wc_get_products( array(
                'status'    => 'publish',
                'limit'     => 1,
                'orderby'   => 'date', // можно: 'menu_order', 'title', 'price'
                'order'     => 'ASC',
                'category'  => array( get_term( $category_id )->slug ),
            ) );

            if ( ! empty( $products ) ) {
                return get_permalink( $products[0]->get_id() );
            }

            return false;
        }

        foreach ($product_categories as $category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image_url = wp_get_attachment_url($thumbnail_id);
            if (!$image_url) {
                $image_url = get_template_directory_uri() . '/assets/img/placeholder.jpg';
            }
            if ($category->slug === 'misc') {
                continue;
            }

//            esc_url(get_term_link($category))
            ?>

            <div class="catalogCategoriesItem" style="">
                <a href="<?php echo esc_url(get_first_product_url_by_category_id_wc($category->term_id)); ?>" style="color: var(--hover-text-color);">
                    <div class="categoryItemTitle">
                        <?php echo esc_html($category->name) ?>
                    </div>
                    <div class="categoryItemImage" style="">
                        <img src="<?php echo esc_url($image_url) ?>" alt="<?php echo esc_attr($category->name) ?>"
                             style="width:100%; height:auto; border-radius: 10px;"/>
                    </div>
                </a>
            </div>
        <?php }
        // if end
        } else { ?>
            <h3>Категории не найдены.</h3>
        <?php } ?>
    </div>


</div>



