<template>
    <div>
        <!-- Logo -->
        <a href="javascript:void(0)" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <b class="logo-mini">{{companyShortName || companyName}}</b>
            <!-- logo for regular state and mobile devices -->
<!--            <span class="logo-lg">{{companyName}}</span>-->
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="javascript:void(0)" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Menu</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown notifications-menu">
                        <a href="javascript:void(0)" @click="logout()">
                            <i class="fa fa-power-off"></i> <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        props: {
            companyName: String,
            companyShortName: String,
        },
        computed: {
            ...mapGetters({
                user: "GET_USER",
            }),
        },
        methods: {
            async logout() {
                try {
                    let response = await this.$store.dispatch('LOGOUT');
                    toastr.warning(response);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    toastr.error(error);
                }
            }
        }
    }
</script>

<style scoped>

</style>
