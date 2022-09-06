<template>
    <!-- sidebar -->
    <section class="sidebar">
        <!-- sidebar menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="user-profile">
                <a href="#">
                    <template v-if="!!user">
                        <img :src="user.avatar" alt="user">
                        <span>{{user.username}}</span>
                        <span class="small pull-right">
                            <i class="fa fa-circle text-success"></i>
                        </span>
                    </template>
                    <template v-else>
                        <img src="/images/avatar.png" alt="user">
                        <span>Admin Panel</span>
                    </template>
                </a>
            </li>
            <template v-if="!!user">
                <template v-if="user.isAdmin">
                    <app-admin-menu></app-admin-menu>
                </template>
                <template v-else>
                    <app-shop-menu></app-shop-menu>
                </template>
            </template>
        </ul>
    </section>
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
