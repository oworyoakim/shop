<template>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-9">
                    <app-autocomplete-input
                        :select-items.sync="productsOptions"
                        v-model="barcode"
                        :value="barcode"
                        @input="addItem"
                        :autofocus="true"
                        placeholder="Type item barcode, title, or category to search"
                    />
                </div>
                <div class="col-1">
                    <button v-if="shopInfo.canCreateItem"
                            type="button"
                            data-toggle="modal"
                            data-target="#itemFormModal"
                            title="Add Item"
                            class="btn btn-info btn-sm pull-right">
                        <i class="fa fa-plus"></i> Item
                    </button>
                </div>
                <div class="col-2">
                    <button v-if="shopInfo.canCreateSupplier"
                            type="button"
                            title="Add Supplier"
                            data-toggle="modal"
                            :disabled="basket.count === 0"
                            data-target="#supplierFormModal"
                            class="btn btn-info btn-sm pull-right">
                        <i class="fa fa-plus"></i> Supplier
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-9 table-responsive">
                    <table class="table-bordered table-sm text-center" width="100%">
                        <thead class="bg-dark">
                        <tr>
                            <th class="w-200">Item</th>
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
                            <tr v-for="(item,key) in basket.items">
                                <td class="w-200 text-left">
                                    <span>{{item.title}}</span>
                                    <br/>
                                    <span class="small text-muted">{{key}}</span>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-prepend" @click="updateItemQuantity(item,-1)">
                                            <span class="input-group-text">
                                                <i class="fa fa-minus"></i>
                                            </span>
                                        </span>
                                        <input type="text"
                                               :value.sync="item.quantity"
                                               class="form-control text-center"
                                               :title="`In ${item.unit}`"
                                               @change.prevent="setItemQuantity(item,$event.target.value)">
                                        <div class="input-group-append" @click.prevent="updateItemQuantity(item,1)">
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
                                           @change.prevent="setItemPrice(item,$event.target.value)">
                                </td>
                                <td>
                                    <input type="number"
                                           :value="item.discount"
                                           min="0"
                                           max="100"
                                           class="form-control text-center"
                                           @change.prevent="setItemDiscount(item,$event.target.value)">
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
                <div class="col-sm-3">
                    <div class="form-group row">
                        <div class="col-12">
                            <span>Items Count: </span>
                            <span class="badge badge-gray pull-right">{{basket.count}}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-4">Subtotal</label>
                        <div class="col-8">
                            <input type="text"
                                   v-model="basket.grossAmount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-4">Discount</label>
                        <div class="col-8">
                            <input type="text"
                                   v-model="basket.discount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-4">VAT (<span>{{shopInfo.vatRate}}</span>%)</label>
                        <div class="col-8">
                            <input type="text"
                                   v-model="basket.taxAmount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-4 text-bold">Net Total</label>
                        <div class="col-8">
                            <input type="text"
                                   v-model="basket.netAmount"
                                   class="form-control input-sm text-right"
                                   readonly>
                        </div>
                    </div>
                    <!--                    <div class="form-group row">-->
                    <!--                        <label class="col-4">Purchase Date</label>-->
                    <!--                        <div class="col-8">-->
                    <!--                            <app-date-range-picker-->
                    <!--                                :config="dateConfig"-->
                    <!--                                v-model="basket.paymentDate"-->
                    <!--                                :value="basket.paymentDate"-->
                    <!--                                input-class="text-right"-->
                    <!--                            />-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <div class="form-group row">
                        <label class="col-4">Amount Paid</label>
                        <div class="col-8">
                            <input type="text"
                                   class="form-control  input-sm text-right"
                                   :value="$numeral(basket.paidAmount).format('0,0')"
                                   @keyup.prevent="setAmountPaid($event.target.value)">
                        </div>
                    </div>
                    <div class="form-group row" v-if="basket.dueAmount > 0">
                        <label class="col-4">Amount Due</label>
                        <div class="col-8">
                            <input type="text"
                                   class="form-control  input-sm text-right"
                                   :value="$numeral(basket.dueAmount).format('0,0')"
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group row" v-if="basket.dueAmount > 0">
                        <label class="col-4">Due Date</label>
                        <div class="col-8">
                            <app-date-range-picker
                                :config="dateConfig"
                                v-model="basket.dueDate"
                                :value="basket.dueDate"
                                :has-errors="!!!basket.dueDate"
                                input-class="text-right"
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-4">Supplier</label>
                        <div class="col-8">
                            <app-select-box
                                :select-options.sync="suppliersOptions"
                                v-model="basket.supplierId"
                                :value="basket.supplierId"
                                placeholder="Select...."
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <button type="button" @click="completeTransaction"
                                    class="btn btn-success btn-sm btn-block"
                                    :disabled="basket.count === 0 || !!!basket.supplierId || (basket.dueAmount > 0 && !!!basket.dueDate)">
                                <i class="fa fa-money"></i>
                                Finish
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" @click="clearBasket"
                                    :disabled="basket.count === 0"
                                    class="btn btn-danger btn-sm btn-block">Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <app-item-form></app-item-form>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import {deepClone} from "../../utils";
    import {EventBus} from "../../app";

    export default {
        props: {
            branchId: Number,
        },
        created() {
            this.getProducts();
            EventBus.$on(['SAVE_ITEM'], this.getProducts);
            //setInterval(this.getProducts, 30000);
        },
        mounted() {
            this.basket.branchId = this.branchId;
            //setInterval(this.getProducts, 30000);
        },
        computed: {
            ...mapGetters({
                products: 'GET_PURCHASABLE_PRODUCTS',
                shopInfo: 'GET_SHOP_INFO',
                formSelectionsOptions: 'GET_FORM_SELECTION_OPTIONS',
                basket: 'GET_BASKET',
            }),
            productsOptions() {
                return this.products.map((item) => {
                    return {
                        value: item.barcode,
                        text: item.barcode + ' (' + item.title + ' ' + item.category + ')',
                    }
                });
            },
            suppliersOptions() {
                return this.formSelectionsOptions.suppliers.map((supplier) => {
                    return {
                        value: supplier.id,
                        text: supplier.name + ` (${supplier.phone})`,
                    }
                });
            }
        },
        data() {
            return {
                barcode: '',
                dueDate: '',
                isSending: false,
                dateConfig: {
                    showDropdowns: true,
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    opens: "center",
                    minDate: this.$moment(),
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                },
            }
        },
        methods: {
            async getProducts() {
                try {
                    await this.$store.dispatch('GET_PURCHASABLE_PRODUCTS', {branchId: this.branchId});
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
                        return prod.barcode === this.barcode;
                    });

                    if (!product) {
                        throw "Item not found!";
                    }
                    product = deepClone(product);

                    product.type = 'buy';
                    product.quantity = 1;
                    this.$store.dispatch('ADD_ITEM', {barcode: this.barcode, item: product});
                    this.barcode = '';
                    $(this.$refs.barcodeInput).focus();
                } catch (error) {
                    console.error(error);
                    await swal({title: error, icon: 'error'});
                    this.barcode = '';
                    $(this.$refs.barcodeInput).focus();
                }
            },
            setItemQuantity(item, qty) {
                qty = this.$numeral(qty).value();
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('SET_ITEM_QUANTITY', {barcode: barcode, quantity: qty});
            },

            updateItemQuantity(item, qty) {
                let barcode = (item.isSecondary && this.basket.itemExists(item.secondaryBarcode)) ? item.secondaryBarcode : item.barcode;
                this.$store.dispatch('UPDATE_ITEM_QUANTITY', {barcode: barcode, quantity: qty});
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
                    if (this.basket.isEmpty) {
                        throw 'Basket empty!';
                    }

                    let balance = this.basket.balance;

                    if (this.basket.netAmount <= 0) {
                        throw 'Amount is less!';
                    }

                    if (this.basket.dueAmount > 0 && !!!this.basket.dueDate) {
                        throw "Due date is required for credit purchases!";
                    }
                    // Submit the purchase
                    this.basket.branchId = this.shopInfo.branchId ?? this.branchId;
                    let response = await this.$store.dispatch('COMPLETE_PURCHASE_TRANSACTION',this.basket);
                    // Show change
                    await swal({title: response, icon: 'success'});
                    this.$store.dispatch('CLEAR_BASKET');
                } catch (error) {
                    await swal({title: error, icon: 'error'});
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
