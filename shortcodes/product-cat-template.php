<?php
$post_id = $args['post_id'] ?? get_the_ID();
$product = wc_get_product($post_id);
if (!$product) return;

$product_link = get_permalink($post_id);
$product_title = get_the_title($post_id);
$product_price = $product->get_price_html();
$product_thumbnail = get_the_post_thumbnail($post_id, 'medium');
?>
<style>

</style>

<div class="wp-block-group productCard">
    <div class="productCardImage">
        <a href="<?php echo esc_url($product_link); ?>">
            <?php
            $thumbnail_id = get_post_thumbnail_id($product->get_id());
            $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail') : '';
            ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
        </a>
    </div>

    <div class="productCardView">
        <div class="productCardViewHeading">
            <div class="productCardViewHeadingTitle">
                <a href="<?php echo esc_url($product_link); ?>"><?php echo $product->get_name(); ?></a>
            </div>
        </div>
        <div class="productCardViewActions">
            <div class="productCardViewActionsWrap">
                <div class="actionsBtnPrice">
                    <span>Заказать</span>
                    <span class="priceDs">от <?php echo wc_price($product->get_price()); ?></span>
                </div>
                <div class="actionsBtnCart">
                    <a href="/cart/?add-to-cart=<?php echo $product->get_id(); ?>&quantity=1" class="actionsBtnCartBtn">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.17116 2.61839H13.0909C13.1268 2.61839 13.1596 2.63155 13.1932 2.64142L13.4862 1.70273C13.5316 1.55733 13.6237 1.43 13.7489 1.33952C13.8742 1.24904 14.0259 1.20019 14.1818 1.2002L15.6364 1.2002C15.8372 1.2002 16 1.35894 16 1.55475V2.26383C16 2.45964 15.8372 2.61839 15.6364 2.61839H14.7205L12.4485 9.89799L13.297 10.8632C13.6324 11.2447 13.7834 11.7703 13.6291 12.2497C13.4317 12.8628 12.8767 13.2547 12.2368 13.2547H1.81817C1.61734 13.2547 1.45452 13.096 1.45452 12.9002V12.1911C1.45452 11.9953 1.61734 11.8365 1.81817 11.8365H12.2369L11.0842 10.5253C11.0552 10.4924 11.0339 10.4551 11.0119 10.4183H3.96519C3.68909 10.4183 3.41868 10.3418 3.18556 10.1975C2.95244 10.0533 2.76622 9.84733 2.64866 9.60376L0.111136 4.3464C0.0382042 4.19431 0.000272751 4.02851 0 3.86062V3.7602C0 3.12963 0.524353 2.61839 1.17116 2.61839Z"
                                  fill="#232323"/>
                            <path d="M11.6353 16.8003C10.832 16.8003 10.1807 16.1653 10.1807 15.3821C10.1807 14.5988 10.832 13.9639 11.6353 13.9639C12.4386 13.9639 13.0898 14.5988 13.0898 15.3821C13.0898 16.1653 12.4386 16.8003 11.6353 16.8003Z"
                                  fill="#232323"/>
                            <path d="M2.91263 16.8003C2.1093 16.8003 1.45807 16.1653 1.45807 15.3821C1.45807 14.5988 2.1093 13.9639 2.91263 13.9639C3.71596 13.9639 4.36719 14.5988 4.36719 15.3821C4.36719 16.1653 3.71596 16.8003 2.91263 16.8003Z"
                                  fill="#232323"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
