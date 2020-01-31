<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-balance-scale"></i> &nbsp;Items</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="createItem" class="btn btn-info btn-sm pull-right"><i
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
                                @click="createItem(item)"
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
            <div class="modal center-modal fade" ref="itemFormModal" id="itemFormModal" tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form @submit.prevent="saveItem" class="form-horizontal">
                            <div class="modal-header bg-pale-purple">
                                <h5 class="modal-title">
                                    <span v-if="!!activeItem.id">Edit Item</span>
                                    <span v-else>New Item</span>
                                </h5>
                                <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-4">Title</label>
                                    <div class="col-8">
                                        <input v-model="activeItem.title" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Unit of Measurement</label>
                                    <div class="col-8">
                                        <select v-model="activeItem.unitId" class="form-control">
                                            <option>Select unit of measurement...</option>
                                            <option v-for="unit in units" :value="unit.value">{{unit.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Category</label>
                                    <div class="col-8">
                                        <select v-model="activeItem.categoryId" class="form-control">
                                            <option value="''">Select category...</option>
                                            <option v-for="category in categories" :value="category.value">{{category.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Item Type</label>
                                    <div class="col-8">
                                        <select v-model="activeItem.account" class="form-control" required>
                                            <option value="sales">Sales Only</option>
                                            <option value="purchases">Purchases Only</option>
                                            <option value="both">Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Profit Margin</label>
                                    <div class="col-8">
                                        <input v-model="activeItem.margin" type="number" class="form-control" min="0"
                                               step="0.1">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-4">Description</label>
                                    <div class="col-8">
                                            <textarea v-model="activeItem.description" class="form-control"
                                                      rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-footer-uniform">
                                <button :disabled="isSending || !(!!activeItem.title && !!activeItem.unitId && !!activeItem.categoryId && !!activeItem.account)"
                                        type="submit"
                                        class="btn btn-info float-right">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                formSelectionOptions: 'GET_FORM_SELECTION_OPTIONS',
            }),
            categories() {
                return this.formSelectionOptions.categories.map((cat) => {
                    return {
                        text: cat.title,
                        value: cat.id,
                    }
                });
            },
            units() {
                return this.formSelectionOptions.units.map((unit) => {
                    return {
                        text: unit.title,
                        value: unit.id,
                    }
                });
            }
        },
        data() {
            return {
                isLoading: false,
                isSending: false,
                activeItem: new Product(),
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
                    this.$nextTick(() => {
                        $(this.$refs.itemsDatatable).DataTable();
                    });
                } catch (error) {
                    this.isLoading = false;
                    swal({title: error, icon: 'error'});
                }
            },
            async saveItem() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_ITEM', this.activeItem);
                    this.isSending = false;
                    swal({title: response, icon: 'success'});
                    this.closeModal();
                    EventBus.$emit('SAVE_ITEM');
                } catch (error) {
                    this.isSending = false;
                    let content = document.createElement('div');
                    content.innerHTML = error;
                    swal({content: content, icon: 'error'});
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
            createItem(item = null) {
                if (item) {
                    this.activeItem = deepClone(item);
                }
                $(this.$refs.itemFormModal).modal('show');
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
