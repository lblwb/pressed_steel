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
                         style="background: #E9ECF5">
                        <div class="cartOrderBlockItemSelected">
                            <input type="checkbox" v-model="item.selected"/>
                        </div>

                        <div class="cartOrderBlockItemImg">
                            <img :src="item.image"/>
                        </div>

                        <div class="cartOrderBlockInfo">
                            <div class="cart-info">
                                <div class="cardInfoTitle">
                                    {{ item.name }}
                                </div>
                                <div class="desc" v-html="item.desc"></div>
                            </div>
                            <div class="qty-box">
                                <button @click="updateQuantity(index, -1)">−</button>
                                <span class="qty">{{ item.quantity }}</span>
                                <button @click="updateQuantity(index, 1)">+</button>
                            </div>
                            <div class="price">{{ formatPrice(item.total) }}</div>
                        </div>

                        <div class="cartOrderBlockDel">
                            <div class="cartOrderBlockDelBtn">
                                <button @click="removeItem(index)">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <rect width="20" height="20" fill="white"/>
                                        <path d="M8 4H12M4 6H16M14.6667 6L14.1991 13.0129C14.129 14.065 14.0939 14.5911 13.8667 14.99C13.6666 15.3412 13.3648 15.6235 13.0011 15.7998C12.588 16 12.0607 16 11.0062 16H8.9938C7.93927 16 7.41202 16 6.99889 15.7998C6.63517 15.6235 6.33339 15.3412 6.13332 14.99C5.90607 14.5911 5.871 14.065 5.80086 13.0129L5.33333 6"
                                              stroke="#FF3C3C" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="cartOrderBlockInfoMob">
                            <div class="cartOrderBlockInfoMobWrapper"
                                 style="display: flex; justify-content: space-between; align-items: center">
                                <div class="cartOrderBlockInfoMobRow" style="    flex: 1;">
                                    <div class="cartOrderBlockInfoMobQty" style="display: inline-flex;flex-flow: column;gap: 4px;">
                                        <div class="infoMobQtyHeading">
                                            <div class="infoMobQtyHeadingTitle">
                                                Количество:
                                            </div>
                                        </div>
                                        <div class="infoMobQtyBox" style="background: rgb(255, 255, 255);padding: 1px;display: flex;align-items: center;height: 100%;">
                                            <div class="infoMobQtyBoxWrapper"
                                                 style="display: flex;justify-content: flex-start;">
                                                <div class="infoMobQtyBoxBtn">
                                                    <button @click="updateQuantity(index, -1)" style="background: #E9ECF5; border: none; padding: 10px 7px; font-size: 16px">−</button>
                                                </div>
                                                <div class="infoMobQtyBoxCount" style="background: #fff; padding: 10px 36px;">
                                                    {{ item.quantity }}
                                                </div>
                                                <div class="infoMobQtyBoxBtn">
                                                    <button @click="updateQuantity(index, 1)" style="background: #E9ECF5; border: none; padding: 10px 7px; font-size: 16px">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cartOrderBlockInfoMobRow" style="flex: 1;">
                                    <div class="cartOrderBlockInfoMobPrice" style="
    display: flex;
    flex-flow: column;
    gap: 4px;
">
                                        <div class="infoMobQtyHeading">
                                            <div class="infoMobQtyHeadingTitle">
                                                Стоимость:
                                            </div>
                                        </div>
                                        <div class="infoMobPrice">
                                            <div class="infoMobPriceBlock" style="background: #fff; padding: 12px 28px;">
                                                {{ formatPrice(item.total) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
