<template>
    <div class="stocks mt-2">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <router-link :to="{name: 'Items'}" class="text-decoration-none">
                    <div class="info-box shadow-none">
                        <span class="info-box-icon bg-info"><i class="fa fa-shopping-basket"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Products</span>
                            <span class="info-box-number">
                                (456)
                            </span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-4 col-sm-6 col-12">
                <router-link :to="{name: 'Categories'}" class="text-decoration-none">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Categories</span>
                            <span class="info-box-number">
                                (35)
                            </span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-4 col-sm-6 col-12" v-if="$store.getters.HAS_ANY_ACCESS(['tenant.suppliers','tenant.suppliers.create','tenant.suppliers.update', 'tenant.suppliers.delete'])">
                <router-link :to="{name: 'Suppliers'}" class="text-decoration-none">
                    <div class="info-box shadow">
                        <span class="info-box-icon bg-warning"><i class="fa fa-shipping-fast"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Suppliers</span>
                            <span class="info-box-number">(10)</span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-4 col-sm-6 col-12" v-if="$store.getters.HAS_ANY_ACCESS(['tenant.purchases','tenant.purchases.create','tenant.purchases.update', 'tenant.purchases.delete', 'tenant.purchases.cancel', 'tenant.purchases.refund', 'tenant.purchases.block', 'tenant.purchases.payables'])">
                <router-link :to="{name: 'Purchases'}">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-danger"><i class="fa fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Purchases</span>
                            <span class="info-box-number">(134)</span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-4 col-sm-6 col-12">
                <router-link :to="{name: 'Stocks'}">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-purple"><i class="fa fa-store-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Stocks</span>
                            <span class="info-box-number">(40,979,000)</span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-md-4 col-sm-6 col-12">
                <router-link :to="{name: 'AccountsPayable'}">
                    <div class="info-box shadow-lg">
                        <span class="info-box-icon bg-green"><i class="fa fa-credit-card"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-gray-dark text-bold">Accounts Payable</span>
                            <span class="info-box-number">(4,097,900)</span>
                        </div>
                    </div>
                </router-link>
            </div>

        </div>
        <div class="mt-2">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import Tabs from "@/shared/components/Tabs";
import Tab from "@/shared/components/Tab";
import Categories from "../../views/Inventory/Categories";
import Items from "../../views/Inventory/Items";
import Stocks from "../../views/Inventory/Stocks";
import Suppliers from "../../views/Inventory/Suppliers";

export default {
    components: {Tabs, Tab, Stocks, Items, Categories,Suppliers},
    data(){
        return {
            active: true
        }
    },
    methods: {
        toggleDefaultTab(){
            if (!this.active) {
                this.active = true;
            }
        },
    },
}
</script>

<style scoped>

</style>
