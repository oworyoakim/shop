<template>
    <div class="purchases mt-2">
        <Spinner v-if="isLoading || !purchasesInfo" />
        <template v-else>
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><i class="fa fa-balance-scale"></i> Purchases</h3>
                    <div class="card-tools">
                        <template>
                            <button :disabled="isLoading" @click="editPurchase()"  type="button" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add</button>
                        </template>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-condensed table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="purchase in purchases">
                            <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <Pagination :items="purchasesInfo" @gotoPage="getPurchases" />
                </div>
                <PurchaseForm @PURCHASE_SAVED="getPurchases()" />
            </div>
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import Pagination from "@/shared/components/Pagination";
import Spinner from "@/shared/components/Spinner";
import {EventBus} from "@/utils/httpClient";
import PurchaseForm from "../../components/Purchases/PurchaseForm";

export default {
    components: {Pagination,Spinner,PurchaseForm},
    mounted() {
        this.getPurchases();
        this.$store.dispatch("GET_FORM_OPTIONS", {
            options: "units,categories"
        });
    },
    computed: {
        ...mapGetters({
            purchasesInfo: 'PURCHASES',
        }),
        purchases(){
            if(!this.purchasesInfo){
                return [];
            }
            return this.purchasesInfo.data;
        },
    },
    data() {
        return {
            isLoading: false,
            barcode: '',
            barcodeCount: 1,
            filters: {
                page: 1
            },
        }
    },
    methods: {
        editPurchase(purchase = null) {
            EventBus.$emit('EDIT_PURCHASE', purchase);
        },

        async getPurchases(page = 0) {
            try {
                this.isLoading = true;
                if (page > 0) {
                    this.filters.page = page;
                }
                await this.$store.dispatch('GET_PURCHASES', this.filters);
                this.isLoading = false;
            } catch (error) {
                this.isLoading = false;
                await this.$store.dispatch('SET_SNACKBAR', {title: error, icon: 'error'});
            }
        },
    },
}
</script>

<style scoped>

</style>
