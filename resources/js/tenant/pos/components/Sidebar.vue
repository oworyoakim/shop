<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <router-link :to="{name: 'dashboard'}" class="brand-link d-flex justify-content-center align-items-center py-4">
            <span class="brand-text font-weight-light logo-xs">{{companyShortName}}</span>
            <span class="brand-text font-weight-light logo-xl">{{companyName}}</span>
        </router-link>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <template v-if="loggedInUser">
                    <div class="image">
                        <img :src="loggedInUser.avatar" class="img-circle elevation-2" alt="Avatar">
                    </div>
                    <div class="info">
                        <a href="javascript:void(0);" class="d-block">{{loggedInUser.username}}</a>
                        <h5 class="float-left text-muted">{{loggedInUser.group}}</h5>
                    </div>
                </template>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <router-link :to="{name: 'dashboard'}" class="nav-link" exact-active-class="active"  active-class="active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'pos'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>PoS<i class="right fas fa-angle-left"></i></p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'reports'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports<i class="right fas fa-angle-left"></i></p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'profile'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Profile<i class="right fas fa-angle-left"></i></p>
                        </router-link>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    name: "Sidebar",
    props: {
        companyName: {
            type: String, default: "",
        },
        companyShortName: {
            type: String, default: "",
        },
    },
    computed: {
        ...mapGetters({
            loggedInUser: "LOGGED_IN_USER",
        }),
    },
}
</script>

<style scoped>

</style>
