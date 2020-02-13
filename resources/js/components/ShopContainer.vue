<template>
    <div class="">
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        created() {
            this.getUserData().then(() => {
                setInterval(this.getUserData, 60000);
            });
        },
        methods: {
            async getUserData() {
                try {
                    await this.$store.dispatch('GET_USER_DATA');
                    await this.$store.dispatch('GET_FORM_SELECTION_OPTIONS');
                    await this.$store.dispatch('GET_SHOP_INFO');
                } catch (error) {
                    console.log(error);
                }
            },
        },
    }
</script>

<style scoped>

</style>
