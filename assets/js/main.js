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
                    const linkmoreEls = document.querySelectorAll("main.featuresBlockBody .featuresBlockMoreBtn a");

                    const newTitle = activeSlide.dataset.title;
                    const newDesc = activeSlide.dataset.desc;
                    const newPrdUrl = activeSlide.dataset.productUrl;
                    console.log('active-slide -> ',activeSlide.dataset);

                    if (titleEls && newTitle) {
                        titleEls.forEach((titleEl) => {
                            titleEl.textContent = newTitle;
                        });
                    }

                    if(linkmoreEls && newPrdUrl){
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
                createApp({
                    data() {
                        return {
                            cartItems: [], // товары из корзины
                            form: {
                                org: '', inn: '', fio: '', phone: '', email: ''
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
                            const res = await fetch('/wp-admin/admin-ajax.php?action=get_cart_data');
                            const data = await res.json();
                            this.cartItems = data.map(item => ({
                                ...item, selected: true, total: item.price * item.quantity,
                            }));
                        }, updateQuantity(index, delta) {
                            const item = this.cartItems[index];
                            const newQty = Math.max(1, item.quantity + delta);
                            fetch('/wp-admin/admin-ajax.php?action=update_cart', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: new URLSearchParams({key: item.key, qty: newQty})
                            }).then(() => {
                                item.quantity = newQty;
                                item.total = newQty * item.price;
                            });
                        }, removeItem(index) {
                            const item = this.cartItems[index];
                            fetch('/wp-admin/admin-ajax.php?action=remove_cart_item', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: new URLSearchParams({key: item.key})
                            }).then(() => {
                                this.cartItems.splice(index, 1);
                            });
                        }, clearCart() {
                            fetch('/wp-admin/admin-ajax.php?action=clear_cart').then(() => {
                                this.cartItems = [];
                            });
                        }, formatPrice(val) {
                            return val.toLocaleString('ru-RU') + ' ₽';
                        }, submitOrder() {
                            alert("Заявка отправлена!"); // Здесь можно обработать отправку формы
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
            const productCardDetail = document.querySelector("#productCardDetail")
            if (productCardDetail) {
                createApp({
                    data() {
                        return {
                            rawParams: window.productParameters || [],
                            productDetail: {
                                select: {
                                    tab: {
                                        // name: 'about'
                                        name: 'spec'
                                    }
                                }
                            },
                            selected: {},
                            parameters: [],
                        };
                    }, computed: {}, methods: {
                        // Функция переключения выбора
                        toggleSelect(paramIndex, valueIndex) {
                            const param = this.rawParams[paramIndex];
                            const value = param.values[valueIndex];

                            if (!value.active) return;

                            param.values.forEach(v => v.selected = false);
                            value.selected = true;
                        },
                        setSelectTab(name) {
                            this.productDetail.select.tab.name = name;
                        }
                    }, mounted() {
                        // При монтировании проходим по параметрам и ставим selected у первого доступного
                        this.rawParams.forEach(paramSectItem => {
                            // Сначала сбросим все selected у значений
                            paramSectItem.values.forEach(value => value.selected = false);

                            // Найдём первый active и выделим его
                            const firstActiveIndex = paramSectItem.values.findIndex(value => value.active);
                            if (firstActiveIndex !== -1) {
                                paramSectItem.values[firstActiveIndex].selected = true;
                            }
                        });
                    }
                }).mount(productCardDetail);
            }
        } catch (e) {
            console.debug("cart-app not");
        }

    }
    const appMobNavbar = () => {
        try {
            const mainMobNavbar = document.getElementById('mainMobNavbar');
            if (!mainMobNavbar) return console.warn('#mainMobNavbar не найден!');
            console.log(mainMobNavbar);
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
                                alert('Пожалуйста, заполните обязательные поля: ФИО, телефон, email');
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
                                        alert('Форма отправлена!');
                                        // закрываем нужный попап
                                        if (formData.get('formType') === 'poupup') togglePoupup();
                                        else togglePoupupConsult();
                                    } else {
                                        alert('Ошибка: ' + json.data);
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert('Сетевая ошибка при отправке формы');
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
                        return  point.map_url;
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

    appCart();
    appProductCard();
    appMobNavbar();
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