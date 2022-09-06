<template>
    <MainModal v-if="isEditing" @closed="closeForm()">
        <template v-slot:header>
            <template v-if="!!branch.id">Edit Branch</template>
            <template v-else>New Branch</template>
        </template>
        <template v-slot:body>
            <div class="form-group row">
                <label class="col-sm-4">Name</label>
                <div class="col-sm-8">
                    <input v-model="branch.name" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">Phone</label>
                <div class="col-sm-8">
                    <input v-model="branch.phone" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">Email</label>
                <div class="col-sm-8">
                    <input v-model="branch.email" type="email" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">Country</label>
                <div class="col-sm-8">
                    <input v-model="branch.country" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">City</label>
                <div class="col-sm-8">
                    <input v-model="branch.city" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">Address</label>
                <div class="col-sm-8">
                    <textarea v-model="branch.address" rows="3" class="form-control"></textarea>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button :disabled="formInvalid"
                    type="button"
                    class="btn btn-info btn-block"
                    @click="saveBranch()">
                Save
            </button>
        </template>
    </MainModal>
</template>

<script>
import {EventBus} from "@/utils/httpClient";
import MainModal from "@/shared/components/MainModal";
import Branch from "@/models/Branch";

export default {
    components: {MainModal},
    mounted() {
        EventBus.$on('EDIT_BRANCH', this.editBranch)
    },
    computed: {
        formInvalid(){
            return this.isSending || !(!!this.branch.name && !!this.branch.phone)
        },
    },
    data() {
        return {
            isEditing: false,
            isSending: false,
            branch: new Branch(),
        }
    },
    methods: {
        async saveBranch() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch('SAVE_BRANCH', this.branch);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR",{title: response, icon: 'success'});
                this.closeForm();
                this.$emit('BRANCH_SAVED');
            } catch (error) {
                this.isSending = false;
                let content = document.createElement('div');
                content.innerHTML = error;
                await this.$store.dispatch("SET_SNACKBAR", {content: content, icon: 'error'});
            }
        },

        editBranch(branch = null) {
            if (branch) {
                this.branch = new Branch(branch);
            }
            this.isEditing = true;
        },

        closeForm() {
            this.branch = new Branch();
            this.isEditing = false;
        },
    },
}
</script>

<style scoped>

</style>
