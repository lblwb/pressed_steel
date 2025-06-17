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


<?php $i = 1; ?>

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
                        <span class="headingTitle __HighActive"><?php echo str_pad($i++, 2, '0', STR_PAD_LEFT); ?></span>
                        <span style="flex: auto; text-align: center; width: 100%"><?php echo esc_html(get_the_title()); ?></span>
                    </div>
                </div>
            </div>

            <div class="featuresBlockBody" style="color: #000;min-height: 240px;background: #E9ECF5;padding: 16px 20px;font-size: 19px;line-height: 48px;word-break: break-word;">
                <?php echo wp_kses_post(get_the_content()); ?>
            </div>
        </div>
    </div>
        <?php endwhile; wp_reset_postdata(); ?>
    <?php else: ?>
        <p><?php _e('No specials found.', 'textdomain'); ?></p>
    <?php endif; ?>
</div>
