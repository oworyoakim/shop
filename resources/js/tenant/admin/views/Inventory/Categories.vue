<template>
    <div class="items mt-2">
        <Spinner v-if="isLoading || !categoriesInfo" />
        <template v-else>
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><i class="fa fa-tree"></i> &nbsp;Item Categories</h3>
                    <div class="card-tools">
                        <button :disabled="isLoading" @click="editCategory()" class="btn btn-info btn-sm"><i
                            class="fa fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-condensed table-sm text-sm w-100">
                        <thead>
                        <tr class="bg-pale-purple text-bold">
                            <th>Title</th>
                            <th class="text-center">Items Count</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="category in categories">
                            <td>{{category.title}}</td>
                            <td class="text-center">{{category.items_count}}</td>
                            <td>{{category.description}}</td>
                            <td>
                                <button v-if="category.canBeEdited"
                                        type="button"
                                        title="Edit"
                                        @click="editCategory(category)"
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
                </div>
                <div class="card-footer text-center">
                    <Pagination :items="categoriesInfo" @gotoPage="getCategories" />
                </div>
                <CategoryForm @CATEGORY_SAVED="getCategories()" />
            </div>
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import {EventBus} from "@/utils/httpClient";
import Spinner from "@/shared/components/Spinner";
import Pagination from "@/shared/components/Pagination";
import CategoryForm from "../../components/Stocks/CategoryForm";

export default {
    components: {CategoryForm, Pagination, Spinner},
    computed: {
        ...mapGetters({
            categoriesInfo: "CATEGORIES"
        }),
        categories(){
            if(!this.categoriesInfo){
                return [];
            }
            return this.categoriesInfo.data;
        },
    },
    data(){
        return {
            isLoading: false,
            filters: {
                page: 1
            },
        }
    },
    mounted() {
        this.getCategories();
    },
    methods: {
        editCategory(category = null){
            EventBus.$emit('EDIT_CATEGORY', category);
        },
        async getCategories(page = 0) {
            try {
                this.isLoading = true;
                if(page > 0) {
                    this.filters.page = page;
                }
                await this.$store.dispatch('GET_CATEGORIES', this.filters);
                this.isLoading = false;
            } catch (error) {
                this.isLoading = false;
                await this.$store.dispatch('SET_SNACKBAR', {title: error, icon: 'error'});
            }
        },
        async deleteCategory(category) {
            try {
                let isConfirmed = await this.$store.dispatch('CONFIRM_ACTION',{
                    title: "Are you sure?",
                    text: `You will delete ${category.name}!`,
                    icon: 'warning',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    closeOnClickOutside: false,
                });
                if (!isConfirmed) {
                    return;
                }
                let response = await this.$store.dispatch('DELETE_CATEGORY', category);
                await this.$store.dispatch('SET_SNACKBAR',{title: response, icon: 'success'});
                this.getCategories();
            } catch (error) {
                await this.$store.dispatch('SET_SNACKBAR',{title: error, icon: 'error'});
            }
        },
    },
}
</script>

<style scoped>

</style>
