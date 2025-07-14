<?php
/**
 * Title: Блок-преимущества 2
 * Slug: pressed_steel/features-two-block
 */
?>

<section class="featuresBlock">
    <div class="featuresBlockWrapper gridWrap">
        <!-- About Company Section -->
        <article class="featuresBlockCol">
            <header class="featuresBlockHeadingWrap">
                <div class="featuresBlockHeading">
                    <h2 class="featuresBlockHeadingTitle">
                        <span class="headingTitle __HighActive">03</span>
                        О компании
                    </h2>
                </div>
                <div class="featuresBlockBodyHeading __Table __Main __Mob">
                    <h3 class="featuresBlockBodyHeadingTitle">
                        PressedSteel – Ваш надежный поставщик прессованного и сварного решетчатого настила
                    </h3>
                    <p class="featuresBlockBodyHeadingDesc">
                        Мы производим решетчатые настилы для промышленности и строительства. Наше преимущество —
                        собственное производство, строгий контроль качества и команда инженеров, готовых реализовать ваш
                        проект любой сложности.
                    </p>
                </div>
            </header>

            <div class="featuresBlockBody">
                <div class="featuresBlockBodyWrapper __End">
                    <div class="featuresBlockBodyLt">
                        <div class="featuresBlockMbImg">
                            <img class="featuresBlockBodyFiImg"
                                 src="<?php echo esc_url(get_theme_file_uri('assets/images/prdctImage2Bg.png')); ?>"
                                 alt="Решетчатый настил PressedSteel" loading="lazy">
                        </div>
                        <div class="featuresBlockMoreBtn">
                            <a class="blockActionBtn __Active" href="/about" class="button is-style-fill"
                               aria-label="Узнать больше о компании">
                                <div class="blockActionBtnWrapper">
                                    <span class="blockActionBtnTitle">О компании</span>
                                    <span class="blockActionBtnIcon">
                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white"
                                                      stroke-opacity="0.4"/>
                                                <g clip-path="url(#clip0_26_5234)">
                                                    <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor"
                                                          stroke-width="1.5"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_26_5234">
                                                        <rect width="25" height="25" fill="currentColor"
                                                              transform="translate(0 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="featuresBlockBodyRt">
                        <div class="featuresBlockBodyFi">
                            <img class="featuresBlockBodyFiImg"
                                 src="<?php echo esc_url(get_theme_file_uri('assets/images/prdctImage2Bg.png')); ?>"
                                 alt="Решетчатый настил PressedSteel" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>

<!--background: rgb(250 252 255)-->
<section class="featuresBlock" style="background: rgb(239 246 255); min-height: 60vh;">
    <div class="featuresBlockWrapper gridWrap" >
        <!-- Certificates Section -->
        <article class="featuresBlockCol" id="certification">
            <header class="featuresBlockHeadingWrap">
                <div class="featuresBlockHeading">
                    <h2 class="featuresBlockHeadingTitle">
                        <span class="headingTitle __HighActive">04</span>
                        Сертификаты
                    </h2>
                </div>
                <div class="featuresBlockBodyHeading __Table __Main">
                    <h3 class="featuresBlockBodyHeadingTitle">
                        Качество, подтвержденное документально
                    </h3>
                </div>
            </header>

            <div class="featuresBlockBody">
                <div class="featuresBlockBodyWrapper __End">
                    <div class="featuresBlockBodyLt">
                        <div class="featuresBlockBodyFiMob">
<!--                            <img src="--><?php //echo esc_url(get_theme_file_uri('assets/images/certificates-mb.svg')); ?><!--"-->
<!--                                 alt="Сертификаты качества PressedSteel" loading="lazy"-->
<!--                                 style="width: 100%; height: 100%;">-->
                            <div class="featuresBlockBodyFiWrapper"   style="display:flex; overflow-x: auto; justify-content: space-between">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/certificate-tb.png')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 225px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert-full.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 225px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 225px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>')">
                            </div>
                        </div>
                        <div class="featuresBlockMoreBtn">
                            <a class="blockActionBtn __Active" href="/certificates" class="button is-style-fill"
                               aria-label="Посмотреть все сертификаты">
                                <div class="blockActionBtnWrapper">
                                    <span class="blockActionBtnTitle">Смотреть все</span>
                                    <span class="blockActionBtnIcon">
                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white"
                                                      stroke-opacity="0.4"/>
                                                <g clip-path="url(#clip0_26_5234)">
                                                    <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor"
                                                          stroke-width="1.5"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_26_5234">
                                                        <rect width="25" height="25" fill="currentColor"
                                                              transform="translate(0 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="featuresBlockBodyRt">
                        <div class="featuresBlockBodyFi __Main">
<!--                            <img src="--><?php //echo esc_url(get_theme_file_uri('assets/images/certificates.svg')); ?><!--"-->
<!--                                 alt="Сертификаты качества PressedSteel" loading="lazy"-->
<!--                                 style="width: 100%; height: 100%;">-->
                            <div class="featuresBlockBodyFiWrapper"  style="display:flex; overflow-x: auto; justify-content: space-between">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/certificate.png')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 280px;"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert-full.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 280px;"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 280px;"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>')">
                            </div>
                        </div>

                        <div class="featuresBlockBodyFi __Table">
<!--                            <img src="--><?php //echo esc_url(get_theme_file_uri('assets/images/certificates-tb.svg')); ?><!--"-->
<!--                                 alt="Сертификаты качества PressedSteel" loading="lazy"-->
<!--                                 style="width: 100%; height: 100%;">-->
                            <div class="featuresBlockBodyFiWrapper"  style="display:flex; overflow-x: auto; justify-content: space-between">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/certificate-tb.png')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 220px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert-full.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 220px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert2.jpg')); ?>')">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>"
                                     alt="Сертификаты качества PressedSteel" loading="lazy"
                                     style="width: 100%; height: 100%; max-width: 220px"
                                     onclick="window.cerftModal.toggleModal('<?php echo esc_url(get_theme_file_uri('assets/images/cert3.jpg')); ?>')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>
