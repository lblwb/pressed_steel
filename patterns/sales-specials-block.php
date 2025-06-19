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
)); ?>

<!--<div class="pageSpecialBodyList" style="padding: 100px 0;">-->

<section class="featuresBlock">
    <div class="featuresBlockWrapper gridWrap">
        <?php if ($query->have_posts()): ?>
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <?php
                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                if (!$thumbnail_url) {
                    $thumbnail_url = get_theme_file_uri('assets/images/prdctImage2Bg.png'); // fallback
                }
                ?>
                <article class="featuresBlockCol">
                    <header class="featuresBlockHeadingWrap">
                        <div class="featuresBlockHeading">
                            <h2 class="featuresBlockHeadingTitle">
                                <span class="headingTitle __HighActive"><?php echo str_pad($i++, 2, '0', STR_PAD_LEFT); ?></span>
                                                    <?php echo esc_html(get_the_title()); ?>
                            </h2>
                        </div>
                        <div class="featuresBlockBodyHeading __Table __Main __Mob">
                            <h3 class="featuresBlockBodyHeadingTitle">
                                <?php // echo esc_html(get_the_title()); ?>
                            </h3>
                            <span class="featuresBlockBodyHeadingDesc"><?php echo wp_kses_post(get_the_content()); ?></span>
                        </div>
                    </header>
                    <div class="featuresBlockBody">
                        <div class="featuresBlockBodyWrapper __End">
                            <div class="featuresBlockBodyLt">
                                <div class="featuresBlockMbImg">
                                    <img class="featuresBlockBodyFiImg"
                                         src="<?php echo esc_url($thumbnail_url); ?>"
                                         alt="<?php the_title_attribute(); ?>" loading="lazy">
                                </div>
                                <div class="featuresBlockMoreBtn">
                                </div>
                            </div>
                            <div class="featuresBlockBodyRt">
                                <div class="featuresBlockBodyFi">
                                    <img class="featuresBlockBodyFiImg" style="max-height: 450px;width: 100%;object-fit: cover;object-position: center;"
                                         src="<?php echo esc_url($thumbnail_url); ?>"
                                         alt="<?php the_title_attribute(); ?>" loading="lazy">
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile;
            wp_reset_postdata(); ?>
        <?php else: ?><p><?php _e('No specials found.', 'textdomain'); ?></p><?php endif; ?>
    </div>
</section>
<!--</div>-->