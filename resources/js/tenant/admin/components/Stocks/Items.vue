<template>
    <div class="items mt-2">
        <div v-else class="card">
            <div class="card-header with-border">
                <h3 class="card-title"><i class="fa fa-balance-scale"></i> &nbsp;Items</h3>
                <div class="card-tools">
                    <button :disabled="isLoading" @click="editItem()" class="btn btn-info btn-sm"><i
                        class="fa fa-plus"></i> Add Item
                    </button>
                </div>
            </div>
            <div class="card-body" v-if="isLoading">
                <Spinner />
            </div>
            <template v-else>
                <div class="card-body table-responsive">
                    <table class="table table-condensed table-sm text-sm w-100">
                        <thead>
                        <tr class="text-bold">
                            <th>Barcode</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Units</th>
                            <th>Margin(%)</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in items">
                            <td>{{item.barcode}}</td>
                            <td>{{item.title}}</td>
                            <td>
                                <span v-if="item.category">{{item.category.title}}</span>
                            </td>
                            <td>
                                <span v-if="item.unit">{{item.unit.title}}</span>
                            </td>
                            <td>{{item.margin}}</td>
                            <td>
                                <button v-if="item.canBeEdited"
                                        type="button"
                                        title="Edit"
                                        @click="editItem(item)"
                                        class="btn btn-info btn-sm">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button v-if="item.canBeDeleted"
                                        type="button"
                                        title="Delete"
                                        @click="deleteItem(item)"
                                        class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button v-if="item.canPrintBarcode"
                                        type="button"
                                        title="Print Barcode"
                                        @click="printItemBarcode(item.barcode)"
                                        class="btn btn-info btn-sm">
                                    <i class="fa fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>
            <iframe src="about:blank" id="printFrame" name="printFrame" style="width:0px; height:0px; border:0px;"></iframe>
        </div>
        <ItemForm />
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import {EventBus} from "@/utils/httpClient";
    import Spinner from "@/shared/components/Spinner";
    import ItemForm from "./ItemForm";

    export default {
        components: {ItemForm, Spinner},
        mounted() {
            this.getItems();
        },
        computed: {
            ...mapGetters({
                items: 'ITEMS',
            }),
        },
        data() {
            return {
                isLoading: false,
                barcode: '',
                barcodeCount: 1,
            }
        },
        methods: {
            async getItems() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_ITEMS');
                    this.isLoading = false;
                } catch (error) {
                    this.isLoading = false;
                    await  this.$store.dispatch('SET_SNACKBAR', {title: error, icon: 'error'});
                }
            },
            async deleteItem(item) {
                try {
                    let isConfirmed = await  this.$store.dispatch('CONFIRM_ACTION',{
                        title: "Are you sure?",
                        text: `You will delete ${item.title}!`,
                        icon: 'warning',
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
                        closeOnClickOutside: false,
                    });
                    if (!isConfirmed) {
                        return;
                    }
                    let response = await this.$store.dispatch('DELETE_ITEM', item);
                    await this.$store.dispatch('SET_SNACKBAR',{title: response, icon: 'success'});
                    this.getItems();
                } catch (error) {
                    await this.$store.dispatch('SET_SNACKBAR',{title: error, icon: 'error'});
                }
            },
            editItem(item = null) {
                EventBus.$emit('EDIT_ITEM', item);
            },

            async printItemBarcode(barcode){
                try{
                    this.barcode = barcode;
                    let count = await this.$store.dispatch("PROMPT_INPUT_ACTION",{
                        title: 'How many copies would you like to print?',
                        input: 'number',
                        inputLabel: 'Number of copies',
                        inputPlaceholder: 'Enter number of copies: ',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You must provide the number of copies';
                            }
                            value = Number(value);
                            if (value < 1) {
                                return 'Number of copies must be greater than or equal to 1';
                            }
                        },
                        confirmButtonText: 'Print',
                        cancelButtonText: 'Cancel',
                    });
                    if (count === null || count === undefined) {
                        return;
                    }
                    if (count === '') {
                        await this.$store.dispatch('SET_SNACKBAR', 'Number of copies is required!');
                        return this.printItemBarcode(barcode);
                    }
                    this.barcodeCount = Number(count);
                    console.log(this.barcodeCount);
                    if (isNaN(this.barcodeCount)) {
                        await this.$store.dispatch('SET_SNACKBAR', 'Number of copies must be a valid number!');
                        return this.printItemBarcode(barcode);
                    }
                    let printUrl = `/items/print-barcode?barcode=${this.barcode}&count=${this.barcodeCount}`;
                    if (window.frames['printFrame']) {
                        window.frames['printFrame'].location = printUrl;
                    }
                }catch (error) {
                    console.log(error);
                    await this.$store.dispatch('SET_SNACKBAR', {title: error,icon: 'error'});
                }
            },
        }
    }
</script>

<style scoped>

</style>
