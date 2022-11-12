<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/" class="brand-link d-flex justify-content-center align-items-center py-4">
            <span class="brand-text font-weight-light logo-xs">{{companyShortName}}</span>
            <span class="brand-text font-weight-light logo-xl">{{companyName}}</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <template v-if="loggedInUser">
                    <div class="image">
                        <router-link :to="{name: 'Profile'}">
                            <img :src="loggedInUser.avatar || '/images/avatar.png'" class="img-circle elevation-2" alt="Avatar">
                        </router-link>
                    </div>
                    <div class="info">
                        <router-link :to="{name: 'Profile'}" class="d-block">{{ loggedInUser.username }}</router-link>
                    </div>
                </template>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <router-link :to="{name: 'Dashboard'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$store.getters.HAS_ANY_ACCESS(['tenant.branches','tenant.branches.create','tenant.branches.update', 'tenant.branches.delete'])">
                        <router-link :to="{name: 'Shops'}" class="nav-link" active-class="active">
                            <i class="fa fa-building"></i>
                            <p>
                                Shops
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'Inventory'}" class="nav-link" active-class="active">
                            <i class="fa fa-shopping-bag"></i>
                            <p>
                                Inventory
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$store.getters.HAS_ANY_ACCESS(['tenant.users','tenant.users.create','tenant.users.update', 'tenant.users.block', 'tenant.users.unblock'])">
                        <router-link :to="{name: 'Users'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Users
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$store.getters.HAS_ANY_ACCESS(['tenant.settings'])">
                        <router-link :to="{name: 'Settings'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Settings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'Profile'}" class="nav-link" active-class="active">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>
                                Profile
                                <i class="right fas fa-angle-left"></i>
                            </p>
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
    props: ['companyName', 'companyShortName'],
    computed: {
        ...mapGetters({
            loggedInUser: "LOGGED_IN_USER",
        }),
    },
}
</script>

<style scoped>

</style>
