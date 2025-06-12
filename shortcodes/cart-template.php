<div id="cart-app">
    <div class="cart-container" v-cloak>
        <div class="cartBodyHeading">
            <div class="cartBodyHeadingTitle">
                Корзина
            </div>
        </div>
        <div class="cartBody">
            <div class="cartBodyWrapper">
                <div class="cartContent" v-if="cartItems && cartItems.length" style="background: #fff;">
                    <div class="cartOrderBlockItem" v-for="(item, index) in cartItems" :key="item.key"
                         style="">
                        <div class="cartOrderBlockItemSelected" v-if="item.selected" @click="item.selected = 0">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="1" y="1" width="18" height="18" stroke="white" stroke-width="2"/>
                                <path d="M4.5 10L9 16L15.5 4" stroke="#0071FE" stroke-width="1.5"/>
                            </svg>
                        </div>
                        <div class="cartOrderBlockItemSelected" v-else @click="item.selected = 1">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="1" y="1" width="18" height="18" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="cartOrderBlockItemImg" style="">
                            <img :src="item.image"/>
                        </div>
                        <div class="cartOrderBlockInfo" style="">
                            <div class="cart-info">
                                <div class="cardInfoTitle">
                                    {{ item.name }}
                                </div>
                                <div class="desc" v-html="item.desc" style="margin-bottom: 20px"></div>
                                <div class="cardInfoParams" style="display: flex; gap: 10px">
                                    <div class="cardInfoParamsRow" v-if="item.attributes">
                                        <div class="cardInfoParamsItem" v-for="(value,key, item) in item.attributes">{{key}}:{{value}}</div>
                                    </div>
<!--                                    <div class="cardInfoParamsRow">-->
<!--                                        <div class="cardInfoParamsItem">Ширина: 1000</div>-->
<!--                                        <div class="cardInfoParamsItem">Покрытие: Цинк</div>-->
<!--                                        <div class="cardInfoParamsItem">Обрамление: Тип А</div>-->
<!--                                    </div>-->
                                </div>
                            </div>
                        </div>

                        <div class="cartOrderBlockInfoMob">
                            <div class="cartOrderBlockInfoMobWrapper">
                                <div class="cartOrderBlockInfoMobRow" style="    flex: 1;">
                                    <div class="cartOrderBlockInfoMobQty" style="display: inline-flex;flex-flow: column;gap: 4px;">
                                        <div class="infoMobQtyHeading" style="margin-bottom: 60px">
                                            <div class="infoMobQtyHeadingTitle">
                                                Количество:
                                            </div>
                                        </div>
                                        <div class="infoMobQtyBox" style="background: rgb(255, 255, 255);padding: 1px;display: flex;align-items: center;height: 100%;">
                                            <div class="infoMobQtyBoxWrapper"
                                                 style="display: flex;justify-content: flex-start;">
                                                <div class="infoMobQtyBoxBtn">
                                                    <button @click="updateQuantity(index,  item.quantity-1)" style="background: #E9ECF5; border: none; padding: 10px 7px; font-size: 16px">−</button>
                                                </div>
                                                <div class="infoMobQtyBoxCount" style="background: #fff; padding: 10px 36px;">
                                                    {{ item.quantity }}
                                                </div>
                                                <div class="infoMobQtyBoxBtn">
                                                    <button @click="updateQuantity(index,  item.quantity+1)" style="background: #E9ECF5; border: none; padding: 10px 7px; font-size: 16px">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cartOrderBlockInfoMobRow" style="flex: 1;">
                                    <div class="cartOrderBlockInfoMobPrice">
                                        <div class="infoMobQtyHeading">
                                            <div class="infoMobQtyHeadingTitle">
                                                Стоимость:
                                            </div>
                                        </div>
                                        <div class="infoMobPrice">
                                            <div class="infoMobPriceBlock" style="background: #fff; padding: 12px 28px; min-width: 140px;">
<!--                                                {{ formatPrice(item.total) }}-->
                                                По запросу
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cartOrderBlockInfoMobRow cartOrderBlockDelMb" style="flex: 1;">
                                    <div class="cartOrderBlockDel">
                                        <div class="cartOrderBlockDelBtn">
                                            <button @click="removeItem(index)" style="border: none" style="display: flex; background: #fff; height: 100%;">
                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="40" fill="white"/>
                                                    <path d="M17.3333 12H22.6667M12 14.6667H28M26.2222 14.6667L25.5988 24.0172C25.5053 25.42 25.4586 26.1215 25.1556 26.6533C24.8888 27.1216 24.4864 27.498 24.0015 27.7331C23.4507 28 22.7476 28 21.3416 28H18.6584C17.2524 28 16.5494 28 15.9985 27.7331C15.5136 27.498 15.1112 27.1216 14.8444 26.6533C14.5414 26.1215 14.4947 25.42 14.4011 24.0172L13.7778 14.6667" stroke="#FF3C3C" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>

                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cartOrderBlockDel">
                            <div class="cartOrderBlockDelBtn">
                                <button @click="removeItem(index)" style="border: none">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <rect width="20" height="20" fill="white"/>
                                        <path d="M8 4H12M4 6H16M14.6667 6L14.1991 13.0129C14.129 14.065 14.0939 14.5911 13.8667 14.99C13.6666 15.3412 13.3648 15.6235 13.0011 15.7998C12.588 16 12.0607 16 11.0062 16H8.9938C7.93927 16 7.41202 16 6.99889 15.7998C6.63517 15.6235 6.33339 15.3412 6.13332 14.99C5.90607 14.5911 5.871 14.065 5.80086 13.0129L5.33333 6"
                                              stroke="#FF3C3C" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cartContent" v-else>
                    <h2>Корзина пустая!</h2>
                </div>

                <div class="cartSidebar">

                    <div class="submitOrderBlock clearCartBlock clearCartBlockMb" style="">
                        <a href="#" @click.prevent="clearCart" class="clearCartBtn">Очистить корзину</a>
                    </div>

                    <div class="cartSidebarWrapper" style="margin-bottom: 10px">
                        <form @submit.prevent="submitOrder">
                            <div class="submitOrderBlock" style="">
                                <div class="submitOrderBlockWrapper">
                                    <div class="submitOrderInput">
                                        <input v-model="form.org" placeholder="Наименование организации"/>
                                    </div>
                                    <div class="submitOrderInput">
                                        <input v-model="form.inn" placeholder="ИНН"/>
                                    </div>
                                </div>
                            </div>

                            <div class="submitOrderBlock" style="">
                                <div class="submitOrderBlockWrapper">
                                    <div class="submitOrderInput">
                                        <input v-model="form.fio" placeholder="ФИО"/>
                                    </div>
                                    <div class="submitOrderInput">
                                        <input v-model="form.phone" placeholder="Телефон"/>
                                    </div>
                                    <div class="submitOrderInput">
                                        <input v-model="form.email" placeholder="E-mail"/>
                                    </div>

                                    <div class="submitOrderTotal">
                                        <div class="orderTotalWrapper">
                                            <div class="orderTotalTitle">
                                                Сумма заказа:
                                            </div>
                                            <div class="orderTotalSummary">
                                                {{ formatPrice(totalSum) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="submitOrderBtn">
                                        <button type="submit">Заказать</button>
                                    </div>

                                </div>
                        </form>
                    </div>
                    <div class="submitOrderBlock clearCartBlock " style="">
                        <a href="#" @click.prevent="clearCart" class="clearCartBtn">Очистить корзину</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
