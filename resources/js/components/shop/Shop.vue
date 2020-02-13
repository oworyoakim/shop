<template>
    <router-view></router-view>
</template>

<script>
    export default {
        created() {
            // this.getShopInfo().then(() => {
            //     this.getSalableProducts();
            // });
        },
        methods: {
            async getShopInfo() {
                try {
                    await this.$store.dispatch('GET_SHOP_INFO');
                } catch (error) {
                    console.error(error);
                }
            },
            async getSalableProducts() {
                try {
                    await this.$store.dispatch('GET_SALABLE_PRODUCTS');
                } catch (error) {
                    console.error(error);
                }
            },
        },
    }
</script>

<style scoped>

</style>
