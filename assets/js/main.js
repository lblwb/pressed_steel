const showNotify = ({ icon = 'ℹ️', title = '', message = '', type = 'info', duration = 5000 }) => {
    jQuery.notify.addStyle('customStyle', {
        html: `
    <div class="notifyjs-customStyle-base" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; min-width: 300px; max-width: 400px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); background: #fff; color: #000;">
      <div class="icon" style="font-size: 22px;" data-notify-html="icon"></div>
      <div class="content">
        <div class="title" style="margin-bottom: 8px">
        <strong data-notify-html="title"></strong>
        </div>
        <div data-notify-html="message"></div>
      </div>
       <button class="notifyjs-close-btn" style="
        position: absolute; 
        top: 10px; 
        right: 10px; 
        border: none; 
        background: transparent; 
        font-size: 24px; 
        cursor: pointer; 
        color: #999;
        transition: color 0.2s ease;
      ">&times;</button>
    </div>
  `,
        classes: {
            base: {},
        }
    });

    jQuery.notify({
        icon: icon,
        title: title,
        message: message
    }, {
        style: 'customStyle',
        autoHide: true,
        autoHideDelay: duration,
        clickToHide: true,
        position: 'right top',
        // Можно управлять закрытием крестиком, если хочешь, добавь сюда свой код.
    });
}

const initFeaturesSliders = () => {
    const sliders = document.querySelectorAll(".featuresBlockBodySlider");
    sliders.forEach((sliderEl) => {
        //Left
        const btnPrev = sliderEl.querySelector(".featuresSliderActionBtnLft");
        const btnPrevs = sliderEl.querySelectorAll(".featuresSliderActionBtnLft");
        //Right
        const btnNext = sliderEl.querySelector(".featuresSliderActionBtnRgh");
        const btnNexts = sliderEl.querySelectorAll(".featuresSliderActionBtnRgh");
        //
        const counterEls = sliderEl.querySelectorAll(".featuresSliderActionCount");
        const pad = num => (num < 10 ? '0' + num : num);
        const slides = sliderEl.querySelectorAll(".swiper-slide");

        const swiper = new Swiper(sliderEl, {
            direction: 'horizontal',
            loop: true,

            navigation: {
                prevEl: btnPrev,
                nextEl: btnNext
            },

            pagination: {
                el: sliderEl.querySelector(".sliderPaginate .sliderActionCount"),
                type: 'custom',
                renderCustom: (swiper, current, total) => {
                    const html = `<span class="current-position">${pad(current)}</span><span class="split-positions">/</span><span class="total-positions">${pad(total)}</span>`;
                    counterEls.forEach(el => el.innerHTML = html);
                    return html;
                }
            },
            on: {
                slideChange: function () {
                    const idx = this.realIndex;
                    // const activeSlide = slides[idx];
                    const realIndex = this.realIndex;
                    const slides = this.slides;
                    const activeSlide = slides[this.activeIndex];


                    const titleEls = document.querySelectorAll("main.featuresBlockBody .featuresBlockBodyHeadingTitle");
                    const descEls = document.querySelectorAll("main.featuresBlockBody .featuresBlockBodyHeadingDesc");
                    const linkmoreEls = document.querySelectorAll("main.featuresBlockBody .featuresBlockMoreBtn a.__PrdLink");

                    const newTitle = activeSlide.dataset.title;
                    const newDesc = activeSlide.dataset.desc;
                    const newPrdUrl = activeSlide.dataset.productUrl;
                    console.log('active-slide -> ', activeSlide.dataset);

                    if (titleEls && newTitle) {
                        titleEls.forEach((titleEl) => {
                            titleEl.textContent = newTitle;
                        });
                    }

                    if (linkmoreEls && newPrdUrl) {
                        linkmoreEls.forEach((linkMoreEl) => {
                            linkMoreEl.href = newPrdUrl;
                        });
                    }

                    if (descEls) {
                        // descEl.textContent = newDesc;
                        descEls.forEach((descEl) => {
                            if (newDesc !== "") {
                                descEl.textContent = newDesc;
                            } else {
                                descEl.textContent = "";
                            }
                        });
                    }


                    // Следующий слайд (с учётом loop)
                    let nextRealIndex = this.realIndex + 1;
                    if (nextRealIndex >= this.slides.length - this.loopedSlides * 2) {
                        nextRealIndex = 0;
                    }


                    // Получаем следующий "настоящий" слайд
                    const allRealSlides = Array.from(slides).filter(slide => !slide.classList.contains('swiper-slide-duplicate'));
                    const nextSlide = allRealSlides[nextRealIndex];
                    const nextTitle = nextSlide?.dataset?.title || '';
                    const nextTextEls = document.querySelectorAll('main.featuresBlockBody .sliderPaginatePrevInfo .__NextSlideText');
                    if (nextTextEls && nextTitle) {
                        nextTextEls?.forEach((el) => {
                            el.textContent = nextTitle;
                        });
                    }
                }
            }
        });

        if (btnNexts) {
            btnNexts.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    swiper.slideNext();
                    // console.log('Next clicked for block', block);
                });
            });
        }


        if (btnPrevs) {
            btnPrevs.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const block = e.target.closest('.featuresBlockCol');
                    // if (swiper) {
                    swiper.slidePrev();
                    console.log('Next clicked for block', block);
                    // }
                });
            });
        }

        // console.log("Initialized features slider:", sliderEl, swiper);
    });
};

const appInit = () => {
    const {createApp} = Vue;

    const appCart = () => {
        try {
            const cartApp = document.querySelector("#cart-app")
            if (cartApp) {
                window.states = {};
                window.states.appCart = createApp({
                    data() {
                        return {
                            cartItems: [], // товары из корзины
                            form: {
                                org: '', inn: '', fio: '', phone: '', email: '', comment: '',
                            },
                        };
                    }, computed: {
                        totalSum() {
                            return this.cartItems
                                .filter(item => item.selected)
                                .reduce((acc, item) => acc + item.total, 0);
                        }
                    }, methods: {
                        async fetchCart() {
                            const res = await fetch('/wp-json/pst/v1/cart', {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            });
                            const data = await res.json();
                            // console.log(data);
                            this.cartItems = data.data.map(item => ({
                                ...item, selected: true,
                                total: item.price * item.quantity,
                            }));
                        }, updateQuantity(index, delta) {
                            const item = this.cartItems[index];
                            // const newQty = Math.max(1, item.quantity + delta);
                            fetch('/wp-json/pst/v1/cart/update', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    // 'X-WP-Nonce': wc_product_data.nonce // Pass nonce
                                },
                                body: JSON.stringify({key: item.key, qty: delta})
                            }).then(async () => {
                                item.quantity = delta;
                                // item.total = newQty * item.price;
                                if (delta == 0) {
                                    delete item;
                                }
                                //
                                await this.fetchCart();
                            });
                        }, removeItem(index) {
                            const item = this.cartItems[index];
                            fetch('/wp-json/pst/v1/cart/remove', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({key: item.key})
                            }).then(async () => {
                                this.cartItems.splice(index, 1);
                                showNotify({
                                    icon: '✅',
                                    title: `Товар удален из корзины`,
                                    message: `Вы удалил  — <strong>${item.name}</strong> из коризны.`
                                });
                                await this.fetchCart()
                            });
                        }, clearCart() {
                            fetch('/wp-json/pst/v1/cart/clear', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    // 'X-WP-Nonce': wc_product_data.nonce // Если потребуется безопасность
                                },
                                // body: JSON.stringify({cartItems: this.cartItems, form: this.form})
                            }).then(async () => {
                                this.cartItems = [];
                                await this.fetchCart();
                                showNotify({
                                    icon: '✅',
                                    title: 'Корзина очищена',
                                    message: 'Все товары были удалены из вашей корзины.'
                                });
                            }).catch((error) => {
                                console.error('Ошибка запроса:', error);
                                showNotify({
                                    icon: '🚫',
                                    title: 'Ошибка соединения',
                                    message: 'Не удалось установить соединение с сервером'
                                });
                            });
                        }, formatPrice(val) {
                            return val.toLocaleString('ru-RU') + ' ₽';
                        }, submitOrder() {
                            fetch('/wp-json/pst/v1/cart/submit_cart', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    // 'X-WP-Nonce': wc_product_data.nonce // Если потребуется безопасность
                                },
                                body: JSON.stringify({cartItems: this.cartItems, form: this.form})
                            })
                                .then(async (res) => {
                                    const data = await res.json();

                                    if (res.ok && data.success) {;
                                        showNotify({
                                            icon: '✅',
                                            title: 'Заказ успешно создан!',
                                            message: 'Мы свяжемся с вами в ближайшие время!'
                                        });
                                        this.clearCart();
                                        this.fetchCart();
                                    } else {
                                        console.error(data);
                                        showNotify({
                                            icon: '⚠',
                                            title: 'Ошибка при отправке',
                                            // message: ''
                                        });
                                    }
                                })
                                .catch((error) => {
                                    console.error('Ошибка запроса:', error);
                                    showNotify({
                                        icon: '🚫',
                                        title: 'Ошибка не удалось отправить',
                                        // message: ''
                                    });
                                });
                        }
                    }, mounted() {
                        this.fetchCart();
                    }
                }).mount(cartApp);
            }
        } catch (e) {
            console.debug("cart-app not");
        }

    }
    const appProductCard = () => {
        try {
            const productCardDetail = document.querySelector('#productCardDetail');
            if (!productCardDetail) {
                return;
            }

            createApp({
                setup() {
                    // Reactive state
                    const state = Vue.reactive({
                        rawParams: window.productParameters || [],
                        productDetail: {
                            qty: {
                                count: 1
                            },
                            select: {
                                tab: {
                                    name: 'spec'
                                }
                            }
                        },
                        selected: {},
                        parameters: [],
                    });

                    // Validate rawParams structure
                    const validatedParams = Vue.computed(() => {
                        if (!Array.isArray(state.rawParams)) return [];
                        return state.rawParams.map(param => ({
                            ...param,
                            values: Array.isArray(param.values) ? param.values : []
                        }));
                    });

                    // Toggle selection method
                    const toggleSelect = (paramIndex, valueIndex) => {
                        // console.log(paramIndex, valueIndex)
                        const param = validatedParams.value[paramIndex];
                        if (!param || !param.values || !param.values[valueIndex]) {
                            console.warn(`error ${paramIndex}:${valueIndex}`);
                            return;
                        }

                        const value = param.values[valueIndex];
                        if (!value.active) return;

                        // Reset all selections
                        param.values.forEach(v => (v.selected = false));
                        // Set selected value
                        value.selected = true;
                        console.log(param.values);
                    };

                    // Set select tab
                    const setSelectTab = (name) => {
                        if (typeof name === 'string') {
                            state.productDetail.select.tab.name = name;
                        } else {
                            console.warn('Invalid tab name:', name);
                        }
                    };


                    const getQtyPrdDetail = Vue.computed(() => {
                        // if (state.productDetail.count) {
                        return state.productDetail.qty.count
                    });


                    const addQty = () => {
                        state.productDetail.qty.count = (state.productDetail.qty.count + 1)
                    };

                    const remQty = () => {
                        if (state.productDetail.qty.count < 2) {
                            return
                        }
                        state.productDetail.qty.count = state.productDetail.qty.count > 0 ? (state.productDetail.qty.count - 1) : null
                    };

                    // Add to cart method using Fetch with custom REST API
                    const addToCart = async (event) => {
                        event.preventDefault(); // Prevent default link behavior
                        const link = event.currentTarget;
                        const productId = link.dataset.product_id;
                        const productName = link.dataset.product_name;
                        const quantity = state.productDetail.qty.count;

                        // Collect selected parameters
                        const attributes = {};
                        validatedParams.value.forEach((param, index) => {
                            const selectedValue = param.values.find(value => value.selected && value.active);
                            console.log(param, selectedValue);
                            if (selectedValue && param.name) {
                                attributes[`${param.name}`] = selectedValue.item;
                            }
                        });

                        // Validate product ID
                        if (!productId) {
                            console.error('Product ID is missing');
                            showNotify({
                                icon: '⚠️',
                                title: "Ошибка с товаром!"
                            })
                            return;
                        }

                        // Prepare JSON payload for custom REST API
                        const payload = {
                            product_id: productId,
                            quantity: parseInt(quantity),
                            attributes: attributes,
                            // custom_fields: validatedParams.value.reduce((acc, param) => {
                            //     if (param.custom_field) acc[param.custom_field] = param.values.find(v => v.selected)?.item;
                            //     return acc;
                            // }, {})
                        };

                        try {
                            const response = await fetch('/wp-json/pst/v1/handle-add-cart', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                console.log('Product added to cart:', data);
                                showNotify({
                                    icon: '🛒',
                                    title: 'Товар добавлен',
                                    message: `Товар — <strong>${productName}</strong> успешно добавлен в корзину.`
                                })
                                // Trigger custom event for front-end updates
                                const event = new CustomEvent('added_to_cart', {
                                    detail: {product_id: productId, attributes}
                                });
                                document.body.dispatchEvent(event);
                            } else {
                                console.error('Add to cart failed:', data.message || 'Unknown error');
                                showNotify({
                                    icon:'⚠️',
                                    title: 'Не удалось добавить',
                                    message: 'Не удалось добавить товар в корзину!'
                                })
                            }
                        } catch (error) {
                            console.error('Fetch error:', error);
                            showNotify({
                                icon:'⚠️',
                                title: 'Не успешно',
                                message: 'Не удалось добавить товар!'
                            })
                        }
                    };

                    // Initialize selections on mount
                    Vue.onMounted(() => {
                        validatedParams.value.forEach(param => {
                            // Reset all selections
                            param.values.forEach(value => (value.selected = false));

                            // Select first active value
                            const firstActiveIndex = param.values.findIndex(value => value.active);
                            if (firstActiveIndex !== -1) {
                                param.values[firstActiveIndex].selected = true;
                            }
                        });
                    });

                    return {
                        rawParams: state.rawParams,
                        productDetail: state.productDetail,
                        selected: state.selected,
                        parameters: state.parameters,
                        toggleSelect,
                        setSelectTab,
                        addToCart,
                        addQty,
                        remQty,
                        getQtyPrdDetail,
                    };
                }
            }).mount(productCardDetail);
        } catch (error) {
            console.error('Failed to initialize product card app:', error);
        }
    }
    const appMobNavbar = () => {
        try {
            const mainMobNavbar = document.getElementById('mainMobNavbar');
            if (!mainMobNavbar) return console.warn('#mainMobNavbar не найден!');
            // console.log(mainMobNavbar);
            Vue.createApp({
                setup() {
                    const appMobNavbarState = Vue.ref({
                        main_navbar: {
                            show: false
                        }
                    });

                    const toggleMobNavbar = () => {
                        appMobNavbarState.value.main_navbar.show = !appMobNavbarState.value.main_navbar.show
                    }

                    return {
                        appMobNavbarState,
                        toggleMobNavbar
                    }
                }
            }).mount(mainMobNavbar);
        } catch (e) {
            console.debug("mainMobNavbar not", e);
        }

    }
    const appCallToAction = () => {
        try {
            const callToActions = document.querySelectorAll('#callToAction');
            callToActions.forEach((callItemAction) => {
                if (!callToActions) return console.warn('#callToAction не найден!');
                // console.log(callToAction);
                window.states = {};
                window.states.appCallToAction = Vue.createApp({
                    setup() {
                        // const ajaxUrl = window.myAjax.ajaxurl;

                        const appCallToActionState = Vue.ref({
                            poupup: {
                                show: false
                            },
                            poupup_consult: {
                                show: false
                            }
                        });

                        const fileInput = Vue.ref(null);

                        const triggerFileInput = () => {
                            fileInput.value?.click();
                        };

                        const form = Vue.reactive({
                            fullname: '',
                            company: '',
                            inn: '',
                            phone: '',
                            email: '',
                            comment: '',
                            file: null
                        });

                        const togglePoupup = () => {
                            appCallToActionState.value.poupup.show = !appCallToActionState.value.poupup.show;
                        };


                        const togglePoupupConsult = () => {
                            appCallToActionState.value.poupup_consult.show = !appCallToActionState.value.poupup_consult.show;
                        }

                        const handleFileChange = (event) => {
                            form.file = event.target.files[0];
                        };

                        const handleFileDrop = (event) => {
                            const files = event.dataTransfer.files;
                            if (files.length > 0) {
                                form.file = files[0];
                            }
                        };

                        const submitForm = () => {
                            if (!form.fullname || !form.phone || !form.email) {
                                showNotify({
                                    icon: 'x',
                                    title: 'Пожалуйста, заполните обязательные поля:',
                                    message: 'ФИО, телефон, email'
                                });
                                return;
                            }

                            const formData = new FormData();
                            // formData.append('action', 'pressedsteel_send_form');    // имя вашего AJAX‑действия
                            formData.append('nonce', window.myAjax.nonce);
                            formData.append('formType', appCallToActionState.value.poupup.show ? 'poupup' : 'poupup_consult');

                            for (const key in form) {
                                if (key === 'file' && form.file) {
                                    formData.append('file', form.file);
                                } else {
                                    formData.append(key, form[key]);
                                }
                            }

                            // fetch(window.myAjax.ajaxUrl + "?action=pressedsteel_send_form", {
                            fetch("/wp-json/pst/v1/handle-form", {
                                method: 'POST',
                                body: formData,
                            })
                                .then(res => res.json())
                                .then(json => {
                                    if (json.success) {
                                        showNotify({
                                            icon: '✅',
                                            title: 'Заявка отправлена',
                                            message: 'Ваша заявка успешно принята!'
                                        });
                                        // закрываем нужный попап
                                        if (formData.get('formType') === 'poupup') togglePoupup();
                                        else togglePoupupConsult();
                                    } else {
                                        console.error('Ошибка: ' + json.data);
                                        showNotify({
                                            icon: '⚠️',
                                            title: 'Ошибка при отправке заявки',
                                            message: 'Не удалось отправить заявку!'
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    showNotify({
                                        icon: '✅',
                                        title: 'Заказ успешно создан!',
                                        message: 'Мы свяжемся с вами в ближайшие время!'
                                    });
                                });
                        };


                        Vue.watch(
                            () => appCallToActionState.value.poupup.show || appCallToActionState.value.poupup_consult.show,
                            (show) => {
                                if (show) {
                                    document.body.style.overflow = 'hidden';     // блокируем прокрутку
                                    document.body.style.touchAction = 'none';   // предотвращаем скролл на мобильных
                                } else {
                                    document.body.style.overflow = '';
                                    document.body.style.touchAction = '';
                                }
                            }
                        );

                        return {
                            appCallToActionState,
                            fileInput,
                            triggerFileInput,
                            togglePoupup,
                            togglePoupupConsult,
                            form,
                            handleFileDrop,
                            handleFileChange,
                            submitForm
                        };
                    }
                }).mount(callItemAction);
            })

        } catch (e) {
            console.debug("callToAction not", e);
        }

    }

    const appContacts = () => {
        try {
            const appContacts = document.querySelector('#appContacts');
            if (!appContacts) return console.warn('#appContacts не найден!');

            Vue.createApp({
                setup() {
                    const appContactState = Vue.reactive({
                        map: {
                            points: [
                                {
                                    name: 'Офис в Москве',
                                    slug: 'office_msk',
                                    coords: [55.865632, 37.587102],
                                    zoom: 17.7,
                                    map_url: "https://yandex.ru/map-widget/v1/?um=constructor%3A83925289b360f934cea261385ce16446f7266ca428344a447ef55cf6f17d1fd2&amp;source=constructor",
                                    // map_url: 'https://yandex.ru/map-widget/v1/?um=constructor%3A903404a79568df78d3c9baa7bdfab3f84c1c6bed5e98dc3ce5a2fb46065ecd64&source=constructor'
                                },
                                {
                                    name: 'Производственная площадка в Орле',
                                    slug: 'mnf_orel',
                                    coords: [52.990941, 36.110711],
                                    zoom: 21,
                                    map_url: "https://yandex.ru/map-widget/v1/?um=constructor%3A3964ce3db25819c097387d865f8ee142a893595de40c7aa46bb5009e981ecc97&amp;source=constructor",
                                    // map_url: 'https://yandex.ru/map-widget/v1/?um=constructor%3A903404a79568df78d3c9baa7bdfab3f84c1c6bed5e98dc3ce5a2fb46065ecd64&source=constructor'
                                }
                            ],
                            tabs: {
                                selected: {
                                    slug: 'office_msk' // стартовое значение
                                }
                            }
                        }
                    });

                    const selectedContactMapTab = (slug) => {
                        appContactState.map.tabs.selected.slug = slug;
                    };

                    // const currentMapUrl = Vue.computed(() => {
                    //     const slug = appContactState.map.tabs.selected.slug;
                    //     const point = appContactState.map.points.find(p => p.slug === slug);
                    //     return point ? point.map_url : '';
                    // });
                    const currentMapUrl = Vue.computed(() => {
                        const slug = appContactState.map.tabs.selected.slug;
                        const point = appContactState.map.points.find(p => p.slug === slug);
                        if (!point) return '';

                        const [lat, lon] = point.coords;
                        const zoom = point.zoom || 17;

                        // pt = "долгота,широта,pm2rdm"
                        // return `https://yandex.ru/map-widget/v1/?ll=${lon},${lat}&z=${zoom}&pt=${lon},${lat},pm2bls&mode=constructor&scroll=true`;
                        return point.map_url;
                    });

                    return {
                        appContactState,
                        selectedContactMapTab,
                        currentMapUrl
                    };
                }
            }).mount(appContacts);
        } catch (e) {
            console.debug("Ошибка инициализации appContacts:", e);
        }
    };

    appMobNavbar();
    appCart();
    appProductCard();
    appCallToAction();
    appContacts();
}


const swipperInit = async () => {
    console.debug("swipper init...");
    await initFeaturesSliders();
}


document.addEventListener("DOMContentLoaded", function () {
    console.debug("loaded!");
    //
    swipperInit().then();
    appInit();
});