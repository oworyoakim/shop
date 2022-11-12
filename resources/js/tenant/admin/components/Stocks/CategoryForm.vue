<template>
    <MainModal v-if="isEditing" @closed="closeForm()">
        <template v-slot:header>
            <template v-if="!!category.id">Edit Category</template>
            <template v-else>New Category</template>
        </template>
        <template v-slot:body>
            <div class="form-group row">
                <label class="col-sm-4">Title</label>
                <div class="col-sm-8">
                    <input v-model="category.title" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">Description</label>
                <div class="col-sm-8">
                    <textarea v-model="category.description" rows="3" class="form-control"></textarea>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button :disabled="formInvalid"
                    type="button"
                    class="btn btn-info btn-block"
                    @click="saveCategory()">
                Save
            </button>
        </template>
    </MainModal>
</template>

<script>
import MainModal from "@/shared/components/MainModal";
import {EventBus} from "@/utils/httpClient";
import Category from "@/models/Category";
export default {
    components: {MainModal},
    mounted() {
        EventBus.$on('EDIT_CATEGORY', this.editCategory)
    },
    computed: {
        formInvalid(){
            return this.isSending || !this.category.title
        },
    },
    data() {
        return {
            isEditing: false,
            isSending: false,
            category: new Category(),
        }
    },
    methods: {
        async saveCategory() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch('SAVE_CATEGORY', this.category);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR",{title: response, icon: 'success'});
                this.closeForm();
                this.$emit('CATEGORY_SAVED');
            } catch (error) {
                this.isSending = false;
                let content = document.createElement('div');
                content.innerHTML = error;
                await this.$store.dispatch("SET_SNACKBAR", {content: content, icon: 'error'});
            }
        },

        editCategory(category = null) {
            if (category) {
                this.category = new Category(category);
            }
            this.isEditing = true;
        },

        closeForm() {
            this.category = new Category();
            this.isEditing = false;
        },
    },
}
</script>

<style scoped>

</style>
