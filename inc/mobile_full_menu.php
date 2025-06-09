<?php

register_nav_menus(array(
    'mobile_full_menu_navigator' => 'Мобильное [полное меню] "Навигация"',
    'mobile_full_menu_product' => 'Мобильное [полное меню] "Продукция"',
));

function render_mob_full_menu_navi_shortcode()
{
    wp_nav_menu(array(
        'theme_location' => 'mobile_full_menu_navigator',
        'container_class'=> 'menu',
        'fallback_cb'    => false,
        'echo'           => true,
    ));
}

function render_mob_full_menu_product_shortcode()
{
//    ob_start();
    wp_nav_menu(array(
        'theme_location' => 'mobile_full_menu_product',
        'container_class'=> 'menu',
//        'menu_class'     => 'menu',
        'fallback_cb'    => false,
        'echo'           => true,
    ));
}

add_shortcode('mobile_full_menu_navi', 'render_mob_full_menu_navi_shortcode');
add_shortcode('mobile_full_menu_prd', 'render_mob_full_menu_product_shortcode');