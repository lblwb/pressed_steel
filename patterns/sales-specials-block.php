<?php
/**
 * Title: Блок-акции
 * Slug: pressed_steel/sales-specials-block
 */
?>
<?php

if (have_rows('sales')):
while (have_rows('sales')) :
the_row();

//        $dataPlatform = get_sub_field('platform');
?>
<div class="pageSpecialBodyList">
    <div class="pageSpecialBodyListItem">
        <div class="featuresBlockCol">
            <div class="featuresBlockHeadingWrap" style="display: flex; justify-content: space-between">
                <div class="featuresBlockHeading" style="margin-bottom: 4rem;min-width: 28.8vw;">
                    <div class="featuresBlockHeadingTitle">
                        <span class="headingTitle __HighActive">01</span>
                        <span><?php echo get_the_title() ?></span>
                    </div>
                </div>
            </div>

            <div class="featuresBlockBody" style="color: #000;">
            </div>
        </div>
        <?php
        endwhile;
        endif;
        ?>
    </div>
</div>
