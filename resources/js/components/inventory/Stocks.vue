<template>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-item-hunt"></i>Item Stocks</h3>
            <div class="box-tools text-right">

            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table-condensed table-striped table-sm stocks-datatable" width="100%">
                <thead>
                <tr class="bg-dark">
                    <th>Barcode</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>CostPrice</th>
                    <th>SellPrice</th>
                    <th>Discount(%)</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="stock in stocks">
                    <td></td>
                    <td>{{stock.item.barcode}}</td>
                    <td>{{stock.item.title}}</td>
                    <td>{{stock.stockQty}} {{stock.unit.slug}}</td>
                    <td>{{stock.costPrice}}</td>
                    <td>{{stock.sellPrice}}</td>
                    <td>{{stock.discount}}</td>
                    <td>
                        <button v-if="stock.canBeAdjusted"
                                @click="adjustStock(stock)"
                                class="btn btn-primary btn-xs">
                            <i class="fa fa-edit"></i> Adjust
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                stocks: [],
            }
        },
        methods:{
            adjustStock(stock){

            }
        }
    }
</script>

<style scoped>

</style>
