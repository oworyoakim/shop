<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-balance-scale"></i> &nbsp;Items</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="editItem" class="btn btn-info btn-sm pull-right"><i
                    class="fa fa-plus"></i> Add Item
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table ref="itemsDatatable" class="table table-condensed table-sm text-sm" width="100%">
                <thead>
                <tr class="bg-pale-purple text-bold">
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
            <app-item-form></app-item-form>
        </div>
        <!-- /.box-body -->
        <iframe src="about:blank" id="printFrame" name="printFrame" style="width:0px; height:0px; border:0px;"></iframe>
    </div>
</template>

<script>
    import {EventBus} from "../../../app";
    import {mapGetters} from "vuex";
    import Product from "../../../models/Product";
    import {deepClone} from "../../../utils";

    export default {
        created() {
            this.getItems();
            EventBus.$on(['SAVE_ITEM', 'DELETE_ITEM'], this.getItems);
        },
        mounted() {

        },
        computed: {
            ...mapGetters({
                items: 'GET_ITEMS',
            }),
        },
        data() {
            return {
                isLoading: false,
                barcode: '',
                barcodeCount: 1,
                activeItem: null,
            }
        },
        methods: {
            async getItems() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_ITEMS');
                    this.isLoading = false;
                    this.$nextTick(() => {
                        $(this.$refs.itemsDatatable).DataTable();
                    });
                } catch (error) {
                    this.isLoading = false;
                    swal({title: error, icon: 'error'});
                }
            },
            async deleteItem(item) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will delete ${item.title}!`,
                        icon: 'warning',
                        buttons: [
                            "Cancel",
                            "Delete"
                        ],
                        closeOnClickOutside: false,
                    });
                    if (!isConfirmed) {
                        return;
                    }
                    let response = await this.$store.dispatch('DELETE_ITEM', item);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('DELETE_ITEM');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },
            editItem(item = null) {
                if (item) {
                    this.activeItem = deepClone(item);
                }
                EventBus.$emit('EDIT_ITEM',this.activeItem);
            },
            async printItemBarcode(barcode){
                try{
                    this.barcode = barcode;
                    let count = await swal({
                        title: 'How many copies would you like to print?',
                        content: {
                            element: "input",
                            attributes: {
                                placeholder: "How many?",
                                min: 1,
                                type: "number",
                                required: 'required'
                            },
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: ['Cancel', 'Print'],
                    });
                    if (count === null) {
                        return;
                    }
                    if (count === '') {
                        await swal('Number of copies is required!');
                        return this.printItemBarcode(barcode);
                    }
                    this.barcodeCount = Number(count);
                    console.log(this.barcodeCount);
                    if (isNaN(this.barcodeCount)) {
                        await swal('Number of copies must be a valid number!');
                        return this.printItemBarcode(barcode);
                    }
                    let printUrl = `/items/print-barcode?barcode=${this.barcode}&count=${this.barcodeCount}`;
                    if (window.frames['printFrame']) {
                        window.frames['printFrame'].location = printUrl;
                    }
                }catch (error) {
                    console.log(error);
                    await swal({title: error,icon: 'error'});
                }
            },
            closeModal() {
                this.activeItem = new Product();
                $(this.$refs.itemFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
