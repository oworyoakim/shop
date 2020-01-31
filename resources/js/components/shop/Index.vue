<template>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-12">
                    <input v-model="barcode"
                           type="text"
                           class="form-control border-primary barcode-input"
                           ref="barcodeInput"
                           placeholder="Item Barcode"
                           autocomplete="off"
                           @change="addItem"
                           autofocus>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-sm-8 table-responsive">
                    <table class="table-bordered table-sm text-center" width="100%">
                        <thead class="bg-dark">
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
                            <tr v-for="item in basket.items">
                                <td>
                                    <span>{{item.title}}</span>
                                    <br/>
                                    <span class="small text-muted">Barcode: {{item.barcode}}</span>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-prepend" @click="decrementItem(item.barcode)">
                                            <span class="input-group-text">
                                                <i class="fa fa-minus"></i>
                                            </span>
                                        </span>
                                        <input type="text"
                                               :value="item.quantity"
                                               class="form-control text-center"
                                               min="0"
                                               :title="`In ${item.unit}`"
                                               @change="updateQuantity(item.barcode,$event.target.value)">
                                        <div class="input-group-append" @click="incrementItem(item.barcode)">
                                            <span class="input-group-text">
                                                <i class="fa fa-plus"></i>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.price).format('0,0')"
                                           class="form-control text-center"
                                           min="0"
                                           @change="updatePrice(item.barcode,$event.target.value)"
                                           readonly>
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.discount).format('0,0')"
                                           class="form-control text-center"
                                           min="0"
                                           @change="updateItemDiscount(item.barcode,$event.target.value)"
                                           readonly>
                                </td>
                                <td>
                                    <input type="text"
                                           :value="$numeral(item.netAmount).format('0,0')"
                                           class="form-control text-center"
                                           min="0"
                                           disabled>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-warning btn-xs"
                                            @click="removeItem(item.barcode)">
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
                        <div class="col-6">
                            <button type="submit" @click="printReceipt"
                                    class="btn btn-warning btn-sm btn-block"
                                    name="printReceipt"
                                    value="printReceipt"><i class="fa fa-print"></i> Print
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" @click="clearBasket();"
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

    export default {
        props: {
            branchId: Number,
        },
        created() {
            //this.getProducts();
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
            })
        },
        data() {
            return {
                basket: new Basket(),
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
                    console.log(event);
                    event.stopImmediatePropagation();
                    this.barcode = this.barcode.trim();
                    if (this.barcode === '') {
                        if(this.basket.count > 0 && event.type === 'keyup' && event.key === 'Enter'){
                            $(this.$refs.amountTendered).focus();
                            return ;
                        }
                        $(this.$refs.barcodeInput).focus();
                        return;
                    }

                    if (this.basket.itemExists(this.barcode)) {
                        this.incrementItem(this.barcode);
                        this.barcode = '';
                        $(this.$refs.barcodeInput).focus();
                        return;
                    }

                    let product = this.products.find((prod) => {
                        return prod.barcode === this.barcode;
                    });

                    if (!product) {
                        await swal({title: 'Item not found!', icon: 'error'});
                        this.barcode = '';
                        $(this.$refs.barcodeInput).focus();
                        return;
                    }

                    if (product.stockQty <= 0) {
                        await swal({title: 'Item out off stock!', icon: 'error'});
                        this.barcode = '';
                        $(this.$refs.barcodeInput).focus();
                        return;
                    }
                    product.type = 'sell';
                    //console.log('Product Clone: ',product);
                    let item = Item.make(product);
                    item.setQuantity(1);
                    this.basket.addItem(item);
                    console.log('Basket: ', this.basket);
                    this.barcode = '';
                    $(this.$refs.barcodeInput).focus();
                } catch (error) {
                    console.error(error.message);
                    await swal({title: error.message, icon: 'error'});
                    this.barcode = '';
                    $(this.$refs.barcodeInput).focus();
                }
            },
            updateQuantity(barcode, qty) {
                //console.log(barcode, qty);
                this.basket.updateItemQuantity(barcode, qty);
            },

            updateItemDiscount(barcode, rate) {
                console.log(barcode, rate);
                this.basket.updateItemDiscount(barcode, rate);
            },

            updatePrice(barcode, price) {
                console.log(barcode, price);
                this.basket.updateItemPrice(barcode, this.$numeral(price).value());
            },

            incrementItem(barcode) {
                this.basket.incrementItemQuantity(barcode);
            },

            decrementItem(barcode) {
                this.basket.decrementItemQuantity(barcode);
            },

            removeItem(barcode) {
                this.basket.removeItem(barcode);
            },

            setAmountTendered(amount) {
                this.basket.setTenderedAmount(this.$numeral(amount).value());
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
                this.basket.clear();
            },

            async printReceipt(event) {
                event.preventDefault();
                console.log(event);
                if (this.basket.isEmpty) {
                    await swal('Basket empty!');
                    return false;
                }

                let balance = this.basket.balance;

                if (balance < 0) {
                    await swal({title: 'Amount is less!', icon: 'error!'});
                    return false;
                }
                // Submit the sale

                // Show change
                await swal({title: `CHANGE (UGX): ${this.$numeral(balance).format('0,0')}`});
                return true;
            },
        },
    }
</script>

<style scoped>
    .barcode-input {
        height: 120% !important;
    }
</style>
