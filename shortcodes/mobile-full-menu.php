<?php

wp_nav_menu(array(
    'menu' => 'Main', // Do not fall back to first non-empty menu.
    'theme_location' => 'base',
//    'fallback_cb' => false // Do not fall back to wp_page_menu()
));

