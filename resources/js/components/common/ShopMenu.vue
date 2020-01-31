<template>
    <div>
        <ul class="sidebar-menu">
            <router-link to="/" tag="li" active-class="active">
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
            <router-link to="/shop" tag="li" active-class="active">
                <a>
                    <i class="fa fa-money"></i>
                    <span>Shop</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </router-link>
        </ul>
    </div>
</template>

<script>
    export default {

    }
</script>

<style scoped>

</style>
