<?php
/**
 * Title: Блок-акции
 * Slug: pressed_steel/sales-specials-block
 */
?>
<?php

//var_dump(have_rows('special'));
//
//if (have_rows('special')):
//while (have_rows('special')) :
//the_row();
//
////        $dataPlatform = get_sub_field('platform');
?>


<?php
// Query specials
$query = new WP_Query(array(
    'post_type' => 'special',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
));?>

<div class="pageSpecialBodyList" style="padding: 100px 0;">
    <?php if( $query->have_posts() ): ?>
        <?php while( $query->have_posts() ): $query->the_post(); ?>
    <div class="pageSpecialBodyListItem">
        <div class="featuresBlockCol">
            <div class="featuresBlockHeadingWrap" style="display: flex; justify-content: space-between">
                <div class="featuresBlockHeading" style="width: 100%">
                    <div class="featuresBlockHeadingTitle" style="display: flex; width: 100%;">
                        <span class="headingTitle __HighActive" style="flex: 0;">01</span>
                        <span style="flex: auto; text-align: center; width: 100%"><?php echo get_the_title() ?></span>
                    </div>
                </div>
            </div>

            <div class="featuresBlockBody" style="color: #000;">
                <svg width="1400" height="420" viewBox="0 0 1400 420" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 100%;">
                    <rect width="1400" height="420" fill="#E9ECF5"/>
                </svg>
            </div>
        </div>
    </div>
        <?php endwhile; wp_reset_postdata(); ?>
    <?php else: ?>
        <p><?php _e('No specials found.', 'textdomain'); ?></p>
    <?php endif; ?>
</div>
