<template>
    <MainModal v-if="isEditing" @closed="closeForm()">
        <template v-slot:header>
            <template v-if="!!item.id">Edit Item</template>
            <template v-else>New Item</template>
        </template>
        <template v-slot:body>
            <div class="form-group row">
                <label class="col-4">Barcode</label>
                <div class="col-7">
                    <input type="text"
                           v-model="item.barcode"
                           :disabled="!!item.id"
                           class="form-control"
                           autofocus>
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
                    <input type="text" v-model="item.title" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-4">Unit of Measurement</label>
                <div class="col-8">
                    <select v-model="item.unit_id" class="form-control">
                        <option>Select unit of measurement...</option>
                        <option v-for="unit in units" :value="unit.value">{{ unit.text }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-4">Category</label>
                <div class="col-8">
                    <select v-model="item.category_id" class="form-control">
                        <option value="''">Select category...</option>
                        <option v-for="category in categories" :value="category.value">{{ category.text }}
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
                    <input type="number" v-model="item.margin" class="form-control" min="0"
                           step="0.1">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-4">Description</label>
                <div class="col-8">
                    <textarea v-model="item.description"
                              class="form-control"
                              rows="3"></textarea>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button :disabled="formInvalid"
                    type="button"
                    class="btn btn-info btn-block"
                    @click="saveItem()">
                Save
            </button>
        </template>
    </MainModal>
</template>

<script>
import {mapGetters} from "vuex";
import MainModal from "@/shared/components/MainModal";
import {EventBus} from "@/utils/httpClient";
import Item from "../../../../models/Item";

export default {
    components: {MainModal},
    mounted() {
        EventBus.$on('EDIT_ITEM', this.editItem);
    },
    computed: {
        ...mapGetters({
            formOptions: 'FORM_OPTIONS',
        }),
        formInvalid() {
            return this.isSending || !this.item.title || !this.item.category_id || !this.item.unit_id;
        },
        categories() {
            return this.formOptions.categories.map((cat) => {
                return {
                    text: cat.title,
                    value: cat.id,
                }
            });
        },
        units() {
            return this.formOptions.units.map((unit) => {
                return {
                    text: unit.title,
                    value: unit.id,
                }
            });
        }
    },
    data() {
        return {
            isEditing: false,
            isSending: false,
            item: new Item(),
        }
    },
    methods: {
        async saveItem() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch('SAVE_ITEM', this.item);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {title: response, icon: 'success'});
                this.closeForm();
                this.$emit('ITEM_SAVED');
            } catch (error) {
                this.isSending = false;
                let content = document.createElement('div');
                content.innerHTML = error;
                await this.$store.dispatch("SET_SNACKBAR", {content: content, icon: 'error'});
            }
        },

        editItem(item = null) {
            if (item) {
                this.item = new Item(item);
            }
            this.isEditing = true;
        },

        closeForm() {
            this.item = new Item();
            this.isEditing = false;
        },
    },
}
</script>

<style scoped>

</style>
