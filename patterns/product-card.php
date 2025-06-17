<?php
/**
 * Title: Product Card
 * Slug: pressed_steel/product-card
 * @package WordPress
 */

if (!defined('ABSPATH')) {
    exit; // Защита от прямого доступа
}

$product_id = get_the_ID();
$product = wc_get_product($product_id);

if (!$product || !$product->is_visible()) {
    return;
}

$thumbnail_id = get_post_thumbnail_id($product->get_id());
$image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'large') : '';
$image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: $product->get_name();

$about = get_field('about_product');
$specs_group = get_field('technical_specs_area');
$material = $specs_group['material'] ?? null;
$tech_specs = $specs_group['technical_specs'] ?? null;

$application_areas = [];
if (have_rows('application_areas')) {
    while (have_rows('application_areas')) {
        the_row();
        $application_areas[] = get_sub_field('area');
    }
}
?>


<article class="productCardDetail gridWrap" id="productCardDetail" itemscope itemtype="https://schema.org/Product"
         style="padding: 85px 0;" v-cloak>
    <meta itemprop="sku" content="<?= esc_attr($product->get_sku()); ?>">
    <meta itemprop="url" content="<?= esc_url(get_permalink($product_id)); ?>">

    <!-- Верхняя часть -->
    <header class="productCardTop" style="display: flex; justify-content: flex-end;">
        <figure class="productImage" style=""
                itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
            <img src="<?= esc_url($image_url); ?>" alt="<?= esc_attr($image_alt); ?>"
                 style="width: 100%;height: 100%;object-fit: contain;object-position: center;">
            <meta itemprop="url" content="<?= esc_url($image_url); ?>">
        </figure>

        <div class="wp-block-column productInfo">
            <div class="productInfoHeading">
                <h1 class="productInfoHeadingTitle" itemprop="name"><?= esc_html($product->get_title()); ?></h1>
                <div class="productInfoHeadingDesc" itemprop="description">
                    <?= wpautop($product->get_description()); ?>
                </div>

                <div class="productInfoHeadingCart">

                    <div class="cartAddBox">
                        <div class="cartAddBoxWrapper" style="display: flex">
                            <div class="cartAddBoxBtn">
                                <a
                                        href="#"
                                        class="blockActionBtn blockActionBtn __Active add_to_cart_button"
                                        @click="addToCart"
                                        data-product_id="<?php echo $product_id ?>"
                                        data-product_name="<?php echo $product->get_name() ?>"
                                        rel="nofollow"
                                        style="display: inline-flex;"
                                        :aria-label="'Добавить ' + productName + ' в корзину?'"
                                >
                                    <div class="blockActionBtnWrapper" style="color: #fff;">
                                        <span class="blockActionBtnTitle">Добавить в корзину</span>
                                    </div>
                                </a>
                            </div>
                            <div class="cartAddBoxPrice">
<!--                                --><?php //echo $product->get_price_html() ?>
                                По запросу
                            </div>
                            <div class="cartAddBoxQty">
                                <div class="cartAddBoxQtyWrapper">
                                    <div class="cartAddBoxQtyBtn" @click="remQty">
                                        -
                                    </div>
                                    <div class="cartAddBoxQtyCount" style="max-width: 64px">
                                        {{ getQtyPrdDetail }}
                                    </div>
                                    <div class="cartAddBoxQtyBtn" @click="addQty">
                                        +
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php
    $parameters = get_field('product_parameters_repeater', $product->get_id());

    // Подготавливаем чистый массив с нужными полями из ACF, чтобы потом использовать в Vue
    $vueParams = [];

    if (!empty($parameters) && is_array($parameters)) {
        foreach ($parameters as $param) {
            $paramName = !empty($param['paramentrs'][0]->post_title) ? $param['paramentrs'][0]->post_title : 'Без названия';
            $values = $param['parametr_values'] ?? [];

            $items = [];
            foreach ($values as $value) {
                if (empty($value['parametr_values_view'])) continue; // фильтруем по видимости
                $items[] = [
                    'item' => $value['parametr_values_item'] ?? '',
                    'active' => !empty($value['parametr_values_active']),
                    'selected' => !empty($value['parametr_values_selected']),
                    'price' => $value['parametr_values_price'] ?? '',
                ];
            }

            $vueParams[] = [
                'name' => $paramName,
                'values' => $items,
            ];
        }
    }
    ?>


    <div class="productCardMiddle">
        <div class="productParams" v-if="rawParams">
            <div class="productParamsItem" v-for="(paramSectItem, pIndex) in rawParams">
                <div class="productParamsItemHeading">
                    <div class="productParamsItemHeadingTitle">
                        {{paramSectItem.name}}
                    </div>
                    <div class="productParamsList productParamsOptions">
                        <div
                                v-for="(value, vIndex) in paramSectItem.values"
                                :key="vIndex"
                                class="productParamsListItem"
                                :class="{
                      '__Selected': value.selected,
                      '__Disabled': !value.active,
                      '__Active': value.selected && value.active
                    }"
                                @click="toggleSelect(pIndex, vIndex)"
                        >
                            {{ value.item }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <main class="productCardBottom" itemprop="additionalProperty" itemscope itemtype="https://schema.org/PropertyValue">
        <div class="productCardTabs">
            <nav class="productCardTabsWrapper" aria-label="Навигация по вкладкам">
                <!--                <button class="productCardTabsItem" :class="{__Active: productDetail.select.tab.name === 'about'}"-->
                <!--                        @click="setSelectTab('about')" type="button">-->
                <!--                    О товаре-->
                <!--                </button>-->
                <button class="productCardTabsItem" :class="{__Active: productDetail.select.tab.name === 'spec'}"
                        @click="setSelectTab('spec')" type="button">
                    Отличительные характеристики
                </button>
                <button class="productCardTabsItem" :class="{__Active: productDetail.select.tab.name === 'space'}"
                        @click="setSelectTab('space')" type="button">
                    Область применения
                </button>
            </nav>
        </div>

        <!-- Контент вкладок -->
        <section class="productCardTabsWrapper">
            <!-- О товаре -->
            <div class="productCardTabsView" v-show="productDetail.select.tab.name === 'about'" id="tab-about"
                 role="tabpanel">
                <h2 class="tabsViewMobTitle">О товаре</h2>
                <div class="tabsViewWrapper">
                    <?php if (!empty($about)): ?>
                        <div class="about-text" itemprop="description">
                            <?= wp_kses_post($about); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Характеристики -->
            <div class="productCardTabsView" v-show="productDetail.select.tab.name === 'spec'" id="tab-spec"
                 role="tabpanel">
                <h2 class="tabsViewMobTitle">Отличительные характеристики</h2>
                <div class="tabsViewWrapper">
                    <?php if ($material): ?>
                        <p><strong>Материал:</strong> <?= esc_html($material); ?></p>
                    <?php endif; ?>

                    <?php if ($tech_specs): ?>
                        <ul class="tech-specs-list">
                            <?php foreach ($tech_specs as $spec): ?>
                                <li>
                                    <?php if($spec['spec_name']):?> <strong><?= esc_html($spec['spec_name']); ?>
                                        :</strong> <?php endif; ?> <?= esc_html($spec['spec_value']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Область применения -->
            <div class="productCardTabsView" v-show="productDetail.select.tab.name === 'space'" id="tab-space"
                 role="tabpanel">
                <h2 class="tabsViewMobTitle">Область применения</h2>
                <div class="tabsViewWrapper">
                    <?php if (!empty($application_areas)): ?>
                        <ul class="application-list">
                            <?php foreach ($application_areas as $area): ?>
                                <li><?= esc_html($area); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</article>

<script>
    window.productParameters = <?php echo json_encode($vueParams, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
</script>