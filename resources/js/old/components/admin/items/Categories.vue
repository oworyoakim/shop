<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-tree"></i> &nbsp;Item Categories</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="createCategory" class="btn btn-info btn-sm pull-right"><i
                    class="fa fa-plus"></i> Add Category
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table ref="categoriesDatatable" class="table table-condensed table-sm" width="100%">
                <thead>
                <tr class="bg-pale-purple text-bold">
                    <th>Title</th>
                    <th>Slug</th>
                    <th class="text-center">Items Count</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="category in categories">
                    <td>{{category.title}}</td>
                    <td>{{category.slug}}</td>
                    <td class="text-center">{{$numeral(category.itemsCount).format('0,0')}}</td>
                    <td>{{category.description}}</td>
                    <td>
                        <button v-if="category.canBeEdited"
                                type="button"
                                title="Edit"
                                @click="createCategory(category)"
                                class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button v-if="category.canBeDeleted"
                                type="button"
                                title="Delete"
                                @click="deleteCategory(category)"
                                class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="modal center-modal fade" ref="categoryFormModal" id="unitFormModal" tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form @submit.prevent="saveCategory" class="form-horizontal">
                            <div class="modal-header bg-pale-purple">
                                <h5 class="modal-title">
                                    <span v-if="!!activeCategory.id">Edit Item Category</span>
                                    <span v-else>New Item Category</span>
                                </h5>
                                <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-4">Title</label>
                                    <div class="col-8">
                                        <input v-model="activeCategory.title" type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-4">Description</label>
                                    <div class="col-8">
                                            <textarea v-model="activeCategory.description" class="form-control"
                                                      rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-footer-uniform">
                                <button :disabled="isSending || !!!activeCategory.title"
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
    </div>
</template>

<script>
    import {EventBus} from "../../../app";
    import {mapGetters} from "vuex";
    import {deepClone} from "../../../utils";
    import Category from "../../../models/Category";

    export default {
        created() {
            this.getCategories();
            EventBus.$on(['SAVE_CATEGORY', 'DELETE_CATEGORY'], this.getCategories);
        },
        computed: {
            ...mapGetters({
                categories: 'GET_CATEGORIES',
            }),
        },
        data() {
            return {
                isLoading: false,
                isSending: false,
                activeCategory: new Category(),
            }
        },
        methods: {
            async getCategories() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_CATEGORIES');
                    this.isLoading = false;
                    this.$nextTick(() => {
                        $(this.$refs.categoriesDatatable).DataTable();
                    });
                } catch (error) {
                    this.isLoading = false;
                    swal({title: error, icon: 'error'});
                }
            },
            async saveCategory() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_CATEGORY', this.activeCategory);
                    this.isSending = false;
                    swal({title: response, icon: 'success'});
                    this.closeModal();
                    EventBus.$emit('SAVE_CATEGORY');
                } catch (error) {
                    this.isSending = false;
                    let content = document.createElement('div');
                    content.innerHTML = error;
                    swal({content: content, icon: 'error'});
                }
            },
            async deleteCategory(category) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will delete ${category.title}!`,
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
                    let response = await this.$store.dispatch('DELETE_CATEGORY', category);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('DELETE_CATEGORY');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },
            createCategory(category = null) {
                if (category) {
                    this.activeCategory = deepClone(category);
                }
                $(this.$refs.categoryFormModal).modal('show');
            },
            closeModal() {
                this.activeUnit = new Category();
                $(this.$refs.categoryFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
