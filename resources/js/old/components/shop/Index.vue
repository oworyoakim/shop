<template>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-8 table-responsive">
                    <div class="form-group">
                        <app-autocomplete-input
                            :select-items.sync="productsOptions"
                            v-model="barcode"
                            :value="barcode"
                            @input="addItem"
                            :autofocus="true"
                            placeholder="Type item barcode, title, or category to search"
                        />
                    </div>
                    <table class="table-bordered table-sm text-center" width="100%">
                        <thead class="bg-dark text-white">
                        <tr>
                            <th class="w-sm-p50">Item</th>
                            <th class="w-sm-p10">Qty</th>
                            <th class="w-sm-p10">Price</th>
                            <th class="w-sm-p10">Discount(%)</th>
                            <th class="w-sm-p10">Total</th>
                            <th class="w-sm-p10"><i class="fa fa-trash"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-if="basket.isEmpty">
                            <tr>
                                <th colspan="6">No items in the basket!</th>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="(item,key) in basket.items" :key="key">
                                <td class="text-left">
                                    <span>{{item.title}}</span>
                                    <br/>
                                    <span class="small text-muted">{{key}}</span>
                                </td>
                                <td>
                                    <input type="number"
                                           :value="item.quantity"
                                           class="form-control text-center"
                                           min="0"
                                           :title="`In ${item.unit}`"
                                           @change.prevent="setItemQuantity(item,$event.target.value)">
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.price).format('0,0')"
                                           class="form-control text-center"
                                           @change.prevent="setItemPrice(item,$event.target.value)"
                                           disabled>
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.discount).format('0,0')"
                                           class="form-control text-center"
                                           @change.prevent="setItemDiscount(item,$event.target.value)"
                                           disabled>
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.netAmount).format('0,0')"
                                           class="form-control text-center"
                                           disabled>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-warning btn-xs"
                                            @click="removeItem(item)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-4">
                    <div class="form-group row">
                        <div class="col-12">
                            <span>Items Count: </span>
                            <span class="badge badge-gray pull-right">{{basket.count}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5">Subtotal</label>
                        <div class="col-7">
                            <input type="text"
                                   v-model="basket.grossAmount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5">Discount</label>
                        <div class="col-7">
                            <input type="text"
                                   v-model="basket.discount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5">VAT (<span>{{shopInfo.vatRate}}</span>%)</label>
                        <div class="col-7">
                            <input type="text"
                                   v-model="basket.taxAmount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 text-bold">Net Total</label>
                        <div class="col-7">
                            <input type="text"
                                   v-model="basket.netAmount"
                                   class="form-control text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5">Amount Tendered</label>
                        <div class="col-7">
                            <input type="text"
                                   :value="$numeral(basket.tenderedAmount).format('0,0')"
                                   class="form-control text-right"
                                   ref="amountTendered"
                                   @keyup="setAmountTendered($event.target.value)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5">Change (UGX)</label>
                        <div class="col-7">
                            <input type="text"
                                   :value="$numeral(basket.balance).format('0,0')"
                                   class="form-control text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4">
                            <button type="button" @click="completeTransaction"
                                    class="btn btn-success btn-sm btn-block"
                                    :disabled="basket.count === 0  || basket.balance < 0">
                                <i class="fa fa-money"></i>
                                Finish
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" @click="printReceipt"
                                    class="btn btn-warning btn-sm btn-block"
                                    :disabled="basket.count === 0 || basket.balance < 0">
                                <i class="fa fa-print"></i> Print
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" @click="clearBasket"
                                    :disabled="basket.count === 0"
                                    class="btn btn-danger btn-sm btn-block">Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Basket from "../../models/Basket";
    import Item from "../../models/Item";
    import {mapGetters} from "vuex";
    import {deepClone} from "../../utils";

    export default {
        props: {
            branchId: Number,
        },
        created() {
            this.getProducts();
            //setInterval(this.getProducts, 30000);
        },
        mounted() {
            this.basket.branchId = this.branchId;
            //setInterval(this.getProducts, 30000);
        },
        computed: {
            ...mapGetters({
                products: 'GET_SALABLE_PRODUCTS',
                shopInfo: 'GET_SHOP_INFO',
                formSelectionsOptions: 'GET_FORM_SELECTION_OPTIONS',
                basket: 'GET_BASKET',
            }),
            productsOptions() {
                return this.products.map((item) => {
                    let barcode = (!!item.isSecondary && !!item.secondaryBarcode) ? item.secondaryBarcode : item.barcode;
                    return {
                        value: barcode,
                        text: barcode + ' (' + item.title + ' ' + item.category + ')',
                    }
                });
            },
        },
        data() {
            return {
                barcode: '',
                isSending: false,
            }
        },
        methods: {
            async getProducts(branchId) {
                try {
                    this.branchId = branchId;
                    console.log(branchId);
                    await this.$store.dispatch('GET_SALABLE_PRODUCTS', {branchId: this.branchId});
                } catch (error) {
                    console.error(error);
                    toastr.error(error);
                }
            },
            async addItem(event) {
                try {
                    //console.log(event);
                    //event.stopImmediatePropagation();
                    this.barcode = String(this.barcode).trim();
                    if (!!!this.barcode) {
                        throw "Search field is empty!";
                    }

                    let product = this.products.find((prod) => {
                        return prod.secondaryBarcode === this.barcode || prod.barcode === this.barcode;
                    });

                    if (!product) {
                        throw "Item not found!";
                    }
                    if (product.stockQty <= 0) {
                        throw "Item out off stock!";
                    }
                    // if the item is already in the basket, just increment quantity by 1
                    if(this.basket.itemExists(this.barcode)){
                        this.updateItemQuantity(product,1);
                        this.barcode = '';
                        $("#autocompleteInput").focus();
                        return;
                    }
                    product = deepClone(product);
                    product.type = 'sell';
                    product.quantity = 1;
                    this.$store.dispatch('ADD_ITEM', {barcode: this.barcode, item: product});
                    this.barcode = '';
                    $("#autocompleteInput").focus();
                } catch (error) {
                    console.error(error.message);
                    await swal({title: error.message, icon: 'error'});
                    this.barcode = '';
                    $("#autocompleteInput").focus();
                }
            },
            setItemQuantity(item, qty) {
                qty = this.$numeral(qty).value();
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('SET_ITEM_QUANTITY', {barcode: barcode, quantity: qty});
            },
            updateItemQuantity(item, qty) {
                qty = this.$numeral(qty).value();
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('UPDATE_ITEM_QUANTITY', {barcode: barcode, quantity: qty});
            },

            updateItemDiscount(item, rate) {
                if (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) {
                    this.basket.updateItemDiscount(item.secondaryBarcode, rate);
                } else {
                    this.basket.updateItemDiscount(item.barcode, rate);
                }
            },

            setItemDiscount(item, rate) {
                rate = this.$numeral(rate).value();
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('SET_ITEM_DISCOUNT', {barcode: barcode, rate: rate});
            },

            setItemPrice(item, price) {
                price = this.$numeral(price).value();
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('SET_ITEM_PRICE', {barcode: barcode, price: price});
            },

            removeItem(item) {
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('REMOVE_ITEM', {barcode: barcode});
            },

            setAmountTendered(amount) {
                amount = this.$numeral(amount).value();
                this.$store.dispatch('SET_TENDERED_AMOUNT', {amount: amount});
            },
            setAmountPaid(amount) {
                amount = this.$numeral(amount).value();
                this.$store.dispatch('SET_PAID_AMOUNT', {amount: amount});
            },
            async clearBasket() {
                let isConfimed = await swal({
                    title: 'Are you sure?',
                    text: `You will remove ${this.basket.count} items from the basket.`,
                    icon: 'warning',
                    buttons: ['Cancel', 'Clear'],
                    closeOnClickOutside: false
                });
                if (!isConfimed) {
                    return;
                }
                this.$store.dispatch('CLEAR_BASKET');
            },

            async completeTransaction(event) {
                try {
                    event.preventDefault();
                    console.log(event);
                    if (this.basket.isEmpty) {
                        await swal('Basket empty!');
                        return false;
                    }

                    let balance = this.basket.balance;

                    if (balance < 0) {
                        await swal({title: 'Amount is less!', icon: 'error'});
                        return false;
                    }
                    // Submit the sale
                    let transcode = await this.$store.dispatch('COMPLETE_SALE_TRANSACTION', this.basket);
                    // Show change
                    await swal({title: `CHANGE (UGX): ${this.$numeral(balance).format('0,0')}`});
                    return transcode;
                } catch (error) {
                    await swal({title: error, icon: 'error'});
                    return false;
                }
            },

            async printReceipt(event) {
                let transcode = this.completeTransaction(event);
                if (!!transcode) {
                    console.log(transcode);
                    // print the receipt
                }
            },
        },
    }
</script>

<style scoped>
    .barcode-input {
        height: 120% !important;
    }
</style>
