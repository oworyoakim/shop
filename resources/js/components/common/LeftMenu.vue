<template>
    <ul class="nav nav-pills margin-bottom margin-top-5 ml-2 pull-left">
        <router-link class="nav-item" to="/shop/dashboard" tag="li" active-class="active">
            <a class="nav-link">Dashboard</a>
        </router-link>
        <router-link class="nav-item" to="/shop/pos" tag="li" active-class="active">
            <a class="nav-link">PoS</a>
        </router-link>
        <router-link class="nav-item" to="/shop/reports" tag="li" active-class="active">
            <a class="nav-link">Reports</a>
        </router-link>
    </ul>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        computed: {},
    }
</script>

<style scoped>
    .nav-link {
        padding: 1.5rem 1rem !important;
    }
</style>
