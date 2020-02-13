<template>
    <div class="modal center-modal fade" ref="itemFormModal" id="itemFormModal" tabindex="-1"
         data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form @submit.prevent="saveItem" class="form-horizontal">
                    <div class="modal-header bg-pale-purple">
                        <h5 class="modal-title">
                            <span v-if="!!item.id">Edit Item</span>
                            <span v-else>New Item</span>
                        </h5>
                        <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-4">Barcode</label>
                            <div class="col-7">
                                <input v-model="item.barcode" :disabled="!!item.id" type="text"
                                       class="form-control" autofocus>
                            </div>
                            <div class="col-1">
                                <button type="button"
                                        class="btn btn-info btn-sm btn-block"
                                        data-toggle="tooltip"
                                        title="A unique barcode will be automatically generated for this item if you do not provide one!">
                                    <span class="fa fa-info"></span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-4">Title</label>
                            <div class="col-8">
                                <input v-model="item.title" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-4">Unit of Measurement</label>
                            <div class="col-8">
                                <select v-model="item.unitId" class="form-control">
                                    <option>Select unit of measurement...</option>
                                    <option v-for="unit in units" :value="unit.value">{{unit.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-4">Category</label>
                            <div class="col-8">
                                <select v-model="item.categoryId" class="form-control">
                                    <option value="''">Select category...</option>
                                    <option v-for="category in categories" :value="category.value">{{category.text}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-4">Item Type</label>
                            <div class="col-8">
                                <select v-model="item.account" class="form-control" required>
                                    <option value="sales">Sales Only</option>
                                    <option value="purchases">Purchases Only</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-4">Profit Margin</label>
                            <div class="col-8">
                                <input v-model="item.margin" type="number" class="form-control" min="0"
                                       step="0.1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-4">Description</label>
                            <div class="col-8">
                                            <textarea v-model="item.description" class="form-control"
                                                      rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-uniform">
                        <button
                            :disabled="isSending || !(!!item.title && !!item.unitId && !!item.categoryId && !!item.account)"
                            type="submit"
                            class="btn btn-info float-right">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../app";
    import {mapGetters} from "vuex";
    import Product from "../../../models/Product";
    import {deepClone} from "../../../utils";

    export default {
        created() {
            EventBus.$on(['EDIT_ITEM'], this.editItem);
        },
        mounted() {
            $('[data-toggle="tooltip"]').tooltip();
        },
        computed: {
            ...mapGetters({
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
                isSending: false,
                item: new Product(),
            }
        },
        methods: {
            async saveItem() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_ITEM', this.item);
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
            editItem(item = null) {
                if (item) {
                    this.item = deepClone(item);
                }
                $(this.$refs.itemFormModal).modal('show');
            },
            closeModal() {
                this.item = new Product();
                $(this.$refs.itemFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
