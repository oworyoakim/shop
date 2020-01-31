<template>
    <h1>Admin Dashboard</h1>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        computed: {
            ...mapGetters({
                user: 'GET_USER',
            }),
        },
    }
</script>

<style scoped>

</style>
