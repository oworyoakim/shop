<template>
    <div class="stocks mt-2">
        <div class="card card-secondary mb-2">
            <div class="card-header">
                <h2 class="card-title">Stocks</h2>
                <div class="card-tools">

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8 col-12">
                        <label>Branch</label>
                        <SelectBoxInput
                            :options="branchesOptions"
                            v-model="filters.branch_id"
                            :value="filters.branch_id"
                        />
                    </div>
                    <div class="col-sm-4 col-12">
                        <label>&nbsp;</label>
                        <button type="button"
                                class="btn btn-info btn-block"
                                @click="getStocks()"
                                :disabled="!filters.branch_id">
                            <i class="fa fa-search"></i>&nbsp;Search
                        </button>
                    </div>
                </div>
                <Spinner v-if="isLoading"/>
                <template v-else>
                    <div class="mt-2">
                        <table class="table table-condensed table-striped table-sm w-100">
                            <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>CostPrice</th>
                                <th>SellPrice</th>
                                <th>Discount(%)</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="stock in stocks">
                                <tr>
                                    <td>{{ stock.barcode }}</td>
                                    <td>{{ stock.title }}</td>
                                    <td>{{ stock.quantity }} {{ stock.unit }}</td>
                                    <td>{{ stock.cost_price | separator }}</td>
                                    <td>{{ stock.sell_price | separator }}</td>
                                    <td>{{ stock.discount }}</td>
                                    <td>
                                        <template v-if="$store.getters.HAS_ANY_ACCESS(['tenant.stocks.adjust'])">
                                            <button class="btn btn-primary btn-xs"
                                                    @click="adjustStock(stock.branch_id, stock.barcode)">
                                                <i class="fa fa-edit"></i> Adjust
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import Spinner from "@/shared/components/Spinner";
import SelectBoxInput from "../../../../shared/components/SelectBoxInput";

export default {
    components: {SelectBoxInput, Spinner},
    mounted() {
        this.$store.dispatch("GET_FORM_OPTIONS", {
            options: "branches"
        });
    },
    computed: {
        ...mapGetters({
            stocksInfo: "STOCKS",
            formOptions: "FORM_OPTIONS",
        }),

        stocks(){
            if (!this.stocksInfo) {
                return [];
            }
            return this.stocksInfo.data;
        },
        branchesOptions() {
            if(!this.formOptions){
                return [];
            }
            return this.formOptions.branches.map((branch) => {
                return {
                    text: branch.name,
                    value: branch.id,
                }
            });
        },
    },
    data() {
        return {
            isLoading: false,
            filters: {
                branch_id: "",
                page: 1,
            },
        }
    },
    methods: {
        adjustStock(branch_id, barcode) {

        },
        async getStocks(page = 0){
            try {
                this.isLoading = true;
                if(page > 0) {
                    this.filters.page = page;
                }
                await this.$store.dispatch('GET_STOCKS', this.filters);
                this.isLoading = false;
            }catch (error) {
                this.isLoading = false;
                await this.$store.dispatch('SET_SNACKBAR', {title: error, icon: 'error'});
            }
        }
    },
}
</script>

<style scoped>

</style>
