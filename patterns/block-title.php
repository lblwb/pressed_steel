<?php
/**
 * Title: Заголовок страницы / категории
 * Slug: pressed_steel/block-title
 * Description: Выводит заголовок текущей страницы или категории.
 * Categories: pressed_steel_titles
 */
?>

<?php
//// Получаем текущий объект запроса (например, объект термина таксономии)
//$current_term = get_queried_object();
//
//if ($current_term && isset($current_term->name)) {
//    // Выводим название категории
//    echo esc_html($current_term->name);
//}
//?>

<?php
$current_object = get_queried_object();

$title = '';

if (is_category() || is_tag() || is_tax()) {
    $title = $current_object->name ?? '';
} elseif (is_post_type_archive()) {
    $title = post_type_archive_title('', false);
} elseif (is_search()) {
    $title = 'Результаты поиска по запросу: ' . get_search_query();
} elseif (is_404()) {
    $title = 'Страница не найдена';
} elseif (is_singular()) {
    $title = get_the_title();
} else {
    $title = get_bloginfo('name');
}

if (!empty($title)) :
    ?>
    <?php echo esc_html($title); ?>
<?php endif; ?>
