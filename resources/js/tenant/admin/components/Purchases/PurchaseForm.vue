<template>
    <MainModal v-if="isEditing" @closed="closeForm()">
        <template v-slot:header>
            <template v-if="!!purchase.id">Edit Purchase</template>
            <template v-else>New Purchase</template>
        </template>
        <template v-slot:body>

        </template>
        <template v-slot:footer>
            <button :disabled="formInvalid"
                    type="button"
                    class="btn btn-info btn-block"
                    @click="savePurchase()">
                Save
            </button>
        </template>
    </MainModal>
</template>

<script>
import {mapGetters} from "vuex";
import MainModal from "@/shared/components/MainModal";
import {EventBus} from "@/utils/httpClient";
import Purchase from "../../../../models/Purchase";

export default {
    components: {MainModal},
    mounted() {
        EventBus.$on('EDIT_PURCHASE', this.editPurchase);
    },
    computed: {
        ...mapGetters({
            formOptions: 'FORM_OPTIONS',
        }),
        formInvalid() {
            return this.isSending;
        },
    },
    data() {
        return {
            isEditing: false,
            isSending: false,
            purchase: new Purchase(),
        }
    },
    methods: {
        async savePurchase() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch('SAVE_PURCHASE', this.purchase);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {title: response, icon: 'success'});
                this.closeForm();
                this.$emit('PURCHASE_SAVED');
            } catch (error) {
                this.isSending = false;
                let content = document.createElement('div');
                content.innerHTML = error;
                await this.$store.dispatch("SET_SNACKBAR", {content: content, icon: 'error'});
            }
        },

        editPurchase(purchase = null) {
            if (purchase) {
                this.purchase = new Purchase(purchase);
            }
            this.isEditing = true;
        },

        closeForm() {
            this.purchase = new Purchase();
            this.isEditing = false;
        },
    },
}
</script>

<style scoped>

</style>
