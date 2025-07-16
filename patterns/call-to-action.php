<?php
/**
 * Title: Призыв к действию
 * Slug: pressed_steel/call-to-action
 * Description: Выводит блок с призывом к действию
 */
?>

<section class="callToAction" id="callToAction" v-cloak>
    <div class="callToActionWrapper">
        <div class="callToActionContext gridWrap">
            <header class="callToActionHeading">
                <h2 class="callToActionHeadingTitle">
                    Разместите заявку для предварительной оценки стоимости и сроков производства
                </h2>
                <p class="callToActionHeadingDesc">
                    Наша команда оперативно сориентирует Вас о сроках и стоимости производства заказа на основе
                    предоставленных характеристик или технического задания. Наше коммерческое предложение отразит все
                    характеристики, сроки и условия оплаты.
                </p>
            </header>

            <div class="callToActionForm">

                <div class="formSubmit">
                    <div class="blockSubmitActionBtn">
                        <button type="submit" aria-label="Получить бесплатный расчет" @click.prevent="togglePoupup">
                            <div class="blockSubmitActionBtnWrapper">
                                <span class="blockSubmitActionBtnTitle">Разместить заявку</span>
                                <span class="blockActionBtnIcon">
                                    <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="#999"
                                              stroke-opacity="0.4"/>
                                        <g clip-path="url(#clip0_26_5234)">
                                            <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor" stroke-width="1.5"/>
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
                        </button>
                    </div>
                    <p class="blockSubmitDesc">
                        Нажимая “Разместить заявку” Вы соглашаетесь с <a href="/privacy-policy" target="_blank">политикой
                            обработки персональных данных</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="callToActionPreview">
            <figure class="callToActionPreviewImg wp-block-image size-full" style="">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/material-callto.png'); ?>"
                     alt="Материалы для расчета решетчатого настила" loading="lazy">
            </figure>
        </div>

        <div class="callToPopupMb">
            <template v-if="appCallToActionState.poupup.show">
                <div class="modal-overlay" @click.self="togglePoupup" @keydown.esc="togglePoupup" @keydown.enter="submitForm">
                    <div class="modal-content">
                        <button class="modal-close" @click="togglePoupup">✕</button>
                        <div class="modal-title">
                            <h2>Оставьте свои контактные данные и менеджер свяжется с вами в течении рабочего дня</h2>
                        </div>
                        <div class="modal-subtitle">
                            <p>———</p>
                        </div>

                        <form @submit.prevent="submitForm" class="modal-form">
                            <!-- ФИО -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">ФИО</div>
                                    <input type="text" placeholder="Введите ФИО" v-model="form.fullname" required />
                                </label>
                            </div>

                            <!-- Компания и ИНН -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">Название компании</div>
                                    <input type="text" placeholder="Введите название компании" v-model="form.company" required />
                                </label>

                                <label class="modal-label">
                                    <div class="modal-heading-title">ИНН</div>
                                    <input type="text" placeholder="Введите ИНН" v-model="form.inn" required />
                                </label>
                            </div>

                            <!-- Телефон и Почта -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">Телефон</div>
                                    <input type="tel" placeholder="Введите телефон" v-model="form.phone" required />
                                </label>

                                <label class="modal-label">
                                    <div class="modal-heading-title">Почта</div>
                                    <input type="email" placeholder="Введите почту" v-model="form.email" required />
                                </label>
                            </div>

                            <!-- Комментарий -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">Комментарий</div>
                                    <input type="text" placeholder="Введите комментарий" v-model="form.comment" />
                                </label>
                            </div>

                            <!-- Загрузка файла -->
                            <div class="modal-upload" @dragover.prevent @drop.prevent="handleFileDrop" @click="triggerFileInput">
                                <div class="icon" style="display: flex; align-items: center; justify-content: center; margin-bottom: 5px">
                                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 11V7.3C19.5 5.61984 19.5 4.77976 19.173 4.13803C18.8854 3.57354 18.4265 3.1146 17.862 2.82698C17.2202 2.5 16.3802 2.5 14.7 2.5H8.3C6.61984 2.5 5.77976 2.5 5.13803 2.82698C4.57354 3.1146 4.1146 3.57354 3.82698 4.13803C3.5 4.77976 3.5 5.61984 3.5 7.3V17.7C3.5 19.3802 3.5 20.2202 3.82698 20.862C4.1146 21.4265 4.57354 21.8854 5.13803 22.173C5.77976 22.5 6.61984 22.5 8.3 22.5H11.5M13.5 11.5H7.5M9.5 15.5H7.5M15.5 7.5H7.5M17.5 21.5V15.5M14.5 18.5H20.5" stroke="#0062DD" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <p v-if="!form.file">Нажмите или перетащите файл в эту область для загрузки</p>
                                <p v-else>Загружен файл: {{ form.file.name }}</p>
                                <input type="file" @change="handleFileChange" ref="fileInput" hidden />
                            </div>

                            <!-- Кнопки -->
                            <div class="modal-buttons">

                                <button class="bgrHeroBlockActionBtn blockActionBtn __Active" type="submit" style="border: none;width: 100%;display: flex;justify-content: center;">
                                    <div class="blockActionBtnWrapper">
                                        <div class="blockActionBtnTitle">Отправить</div>
                                        <div class="blockActionBtnIcon">
                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white" stroke-opacity="0.4"/>
                                                <g clip-path="url(#clip0_26_5234)">
                                                    <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor" stroke-width="1.5"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_26_5234">
                                                        <rect width="25" height="25" fill="currentColor" transform="translate(0 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                            </div>

                            <p class="modal-disclaimer">
                                Нажимая “Отправить”, Вы соглашаетесь с <a href="/privacy-policy/">политикой обработки персональных данных</a> и <a href="/privacy-policy/">политикой сайта</a>
                            </p>
                        </form>

                    </div>
                </div>
            </template>
            <template v-if="appCallToActionState.poupup_consult.show">
                <div class="modal-overlay" @click.self="togglePoupupConsult">
                    <div class="modal-content">
                        <button class="modal-close" @click="togglePoupupConsult">✕</button>
                        <div class="modal-title">
                            <h2>Оставьте свои контактные данные и менеджер свяжется с вами в течении рабочего дня</h2>
                        </div>
                        <div class="modal-subtitle">
                            <p>———</p>
                        </div>

                        <form @submit.prevent="submitForm" class="modal-form">
                            <!-- ФИО -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">ФИО</div>
                                    <input type="text" placeholder="Введите ФИО" v-model="form.fullname" required />
                                </label>
                            </div>

                            <!-- Компания и ИНН -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">Телефон</div>
                                    <input type="tel" placeholder="Введите телефон" v-model="form.phone" required />
                                </label>

                                <label class="modal-label">
                                    <div class="modal-heading-title">Почта</div>
                                    <input type="email" placeholder="Введите почту" v-model="form.email" required />
                                </label>
                            </div>

                            <!-- Комментарий -->
                            <div class="modal-row">
                                <label class="modal-label">
                                    <div class="modal-heading-title">Комментарий</div>
                                    <input type="text" placeholder="Введите комментарий" v-model="form.comment" />
                                </label>
                            </div>

                            <!-- Кнопки -->
                            <div class="modal-buttons">
<!--                                <div class="bgrHeroBlockActionBtn blockActionBtn __Transp __Shop" @click="togglePoupup">-->
<!--                                    <div class="blockActionBtnWrapper">-->
<!--                                        <div class="blockActionBtnTitle">Пропустить</div>-->
<!--                                        <div class="blockActionBtnIcon">-->
<!--                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                                                <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white" stroke-opacity="0.4"/>-->
<!--                                                <g clip-path="url(#clip0_26_5234)">-->
<!--                                                    <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor" stroke-width="1.5"/>-->
<!--                                                </g>-->
<!--                                                <defs>-->
<!--                                                    <clipPath id="clip0_26_5234">-->
<!--                                                        <rect width="25" height="25" fill="currentColor" transform="translate(0 0.5)"/>-->
<!--                                                    </clipPath>-->
<!--                                                </defs>-->
<!--                                            </svg>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->

                                <button class="bgrHeroBlockActionBtn blockActionBtn __Active" type="submit" style="border: none;width: 100%;display: flex;justify-content: center;">
                                    <div class="blockActionBtnWrapper">
                                        <div class="blockActionBtnTitle">Отправить</div>
                                        <div class="blockActionBtnIcon">
                                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.5" y="1" width="24" height="24" rx="12" stroke="white" stroke-opacity="0.4"/>
                                                <g clip-path="url(#clip0_26_5234)">
                                                    <path d="M10.5 9L14.5 13L10.5 17" stroke="currentColor" stroke-width="1.5"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_26_5234">
                                                        <rect width="25" height="25" fill="currentColor" transform="translate(0 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </button>
                            </div>

                            <p class="modal-disclaimer">
                                Нажимая “Отправить”, Вы соглашаетесь с <a href="/privacy-policy/">политикой обработки персональных данных</a> и <a href="/privacy-policy/">политикой сайта</a>
                            </p>
                        </form>

                    </div>
                </div>
            </template>
        </div>
    </div>
</section>
