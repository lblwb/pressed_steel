<!-- Первый блок: Продукция -->
<article class="featuresBlockCol __Slider" itemscope itemtype="https://schema.org/Product">
    <header class="featuresBlockHeadingWrap">
        <!--        style="margin-bottom: 4rem; min-width: 28.8vw;"-->
        <div class="featuresBlockHeading">
            <div class="featuresBlockHeadingTitle">
                <span class="headingTitle __HighActive">01</span>
                <h3 class="wp-block-heading" itemprop="name">Продукция</h3>
            </div>
        </div>
        <div class="featuresBlockBodyHeading __Table">
            <h3 class="featuresBlockBodyHeadingTitle wp-block-heading" itemprop="description">
                Сварной решетчатый настил
            </h3>
            <p class="featuresBlockBodyHeadingDesc __Slice">
            </p>
        </div>

    </header>

    <main class="featuresBlockBody" style="color: #000;" unselectable="on">
        <div class="featuresBlockBodyWrapper">

            <!-- Левая колонка: описание -->
            <div class="featuresBlockBodyLt">
                <header class="featuresBlockBodyHeading __Main __Slice">
                    <h3 class="featuresBlockBodyHeadingTitle wp-block-heading" itemprop="description">
                        Сварной решетчатый настил
                    </h3>
                    <p class="featuresBlockBodyHeadingDesc __Main __Slice">
                    </p>
                </header>

                <div class="featuresBlockMoreBtn">
                    <a href="/" class="blockActionBtn __Active button is-style-fill" style="color: white;"
                       role="button" aria-label="Подробнее о продукции">
                        <div class="blockActionBtnWrapper">
                            <span class="blockActionBtnTitle">Подробнее</span>
                            <span class="blockActionBtnIcon" aria-hidden="true">
                                        <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white"
                                                  stroke-opacity="0.4"/>
                                            <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Правая колонка: слайдер -->
            <div class="featuresBlockBodyRt">
                <div class="featuresBlockBodySlider" aria-label="Галерея продукции">
                    <div class="featuresBlockBodySliderWrapper swiper-wrapper">
                        <?php
                        $terms = get_terms([
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'parent' => 0, // или убрать если нужны все
                            'number' => 10,
                        ]);

                        if (!empty($terms) && !is_wp_error($terms)) :
                            foreach ($terms as $term) :

                                // Получаем описание и изображение категории
                                $term_description = term_description($term->term_id, 'product_cat');
                                $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                                $image_url = wp_get_attachment_url($thumbnail_id);

                                // Получаем первый товар из категории
                                $products = wc_get_products([
                                    'limit' => 1,
                                    'status' => 'publish',
                                    'category' => [$term->slug],
                                    'orderby' => 'menu_order',
                                ]);

                                if (!empty($products)) :
                                    $product = $products[0];
                                    $product_link = get_permalink($product->get_id());
                                    ?>
                                    <figure class="featuresBlockBodySliderItem swiper-slide"
                                            data-title="<?php echo esc_attr($term->name); ?>"
                                            data-product-url="<?php echo esc_url($product_link); ?>"
                                            data-desc="<?php echo esc_attr(wp_strip_all_tags($term_description)); ?>">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <img src="<?php echo esc_url($image_url); ?>"
                                                 alt="<?php echo esc_attr($term->name); ?>" loading="lazy">
                                        </a>
                                    </figure>
                                <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <div class="sliderActionNavigationTbl">
                        <button class="sliderActionBtn featuresSliderActionBtnLft"
                                aria-label="Предыдущий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 1L1 9M1 9L8 17M1 9H17" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                        </button>
                        <button class="sliderActionBtn featuresSliderActionBtnRgh __Active"
                                aria-label="Следующий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 17L17 9M17 9L10 1M17 9H1" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                        </button>
                    </div>

                    <!-- Навигация слайдера -->
                    <nav class="sliderPaginate" aria-label="Навигация по слайдеру">
                        <div class="sliderPaginateWrapper">
                            <div class="sliderActionNavigation __Main __Mob">
                                <div class="sliderActionNavigationWrap">
                                    <button class="sliderActionBtn featuresSliderActionBtnLft"
                                            aria-label="Предыдущий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 1L1 9M1 9L8 17M1 9H17" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                    </button>

                                    <div class="sliderActionCount featuresSliderActionCount" style="width: auto;">
                                        <!-- 01 / 04 -->
                                    </div>

                                    <button class="sliderActionBtn featuresSliderActionBtnRgh __Active"
                                            aria-label="Следующий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 17L17 9M17 9L10 1M17 9H1" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                    </button>
                                </div>
                            </div>

<!--                            <div class="sliderPaginatePrevInfo">-->
<!--                                <span style="font-family: 'Montserrat',sans-serif;font-style: normal;font-weight: 300;font-size: 18px;line-height: 150%;letter-spacing: 0.02em;color: #232323;">Далее:</span><br>-->
<!--                                <span class="__NextSlideText"-->
<!--                                      style="font-family: 'Montserrat',sans-serif;font-style: normal;font-weight: 500;font-size: 18px;line-height: 150%;letter-spacing: 0.02em;color: #232323;">-->
<!--                                        Пресованный решетчатый настил-->
<!--                                    </span>-->
<!--                            </div>-->

                            <div class="sliderActionNavigationMob">
                                <div class="sliderActionNavigationWrap">
                                    <button class="sliderActionBtn featuresSliderActionBtnLft"
                                            aria-label="Предыдущий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 1L1 9M1 9L8 17M1 9H17" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                    </button>

                                    <div class="sliderActionCount featuresSliderActionCount" style="width: auto;">
                                        <!-- 01 / 04 -->
                                    </div>

                                    <button class="sliderActionBtn featuresSliderActionBtnRgh __Active"
                                            aria-label="Следующий слайд">
                                            <span class="sliderActionBtnIcon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 17L17 9M17 9L10 1M17 9H1" stroke="currentColor"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </main>
</article>