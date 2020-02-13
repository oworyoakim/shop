<template>
    <div>
        <ul class="sidebar-menu">
            <router-link to="/admin/dashboard" active-class="active" tag="li">
                <a>
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>
        <ul class="sidebar-menu">
            <router-link to="/admin/units" tag="li" active-class="active">
                <a>
                    <i class="fa fa-balance-scale"></i>
                    <span>Units</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/categories" tag="li" active-class="active">
                <a>
                    <i class="fa fa-tree"></i>
                    <span>Categories</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/items" tag="li" active-class="active">
                <a>
                    <i class="fa fa-product-hunt"></i>
                    <span>Items</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/branches" tag="li" active-class="active">
                <a>
                    <i class="fa fa-building"></i>
                    <span>Branches</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/stores/purchase" tag="li" active-class="active">
                <a>
                    <i class="fa fa-shopping-basket"></i>
                    <span>Purchase</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/stores/stocks" tag="li" active-class="active">
                <a>
                    <i class="fa fa-shopping-bag"></i>
                    <span>Stocks</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/suppliers" tag="li" active-class="active">
                <a>
                    <i class="fa fa-truck"></i>
                    <span>Suppliers</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/users" tag="li" active-class="active">
                <a><i class="fa fa-users"></i>
                    <span>Manage Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/roles" tag="li" active-class="active">
                <a>
                    <i class="fa fa-gears"></i>
                    <span>Manage Roles</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>

        <ul class="sidebar-menu">
            <router-link to="/admin/settings" tag="li" active-class="active">
                <a>
                    <i class="fa fa-cogs"></i>
                    <span>Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>
    </div>
</template>

<script>
    export default {}
</script>

<style scoped>

</style>
