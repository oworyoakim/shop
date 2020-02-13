<template>
    <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                <img :src="avatar" class="user-image rounded-circle" alt="User Image">
            </a>
            <ul class="dropdown-menu scale-up">
                <li class="user-header">
                    <img :src="avatar" class="float-left rounded-circle" alt="User Image">
                    <p>{{username}}</p>
                    <p>{{email}}</p>
                </li>
                <li class="user-body">
                    <div class="row">
                        <div class="col-12 text-left">
                            <a href="javascript:void(0)">
                                <i class="fa fa-user"></i> <span>My Profile</span>
                            </a>
                        </div>
                        <div class="col-12 text-left">
                            <a href="javascript:void(0)" @click="logout()">
                                <i class="fa fa-power-off"></i> <span>Logout</span>
                            </a>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                </li>
            </ul>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            username: String,
            email: String,
            avatar: String,
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
    .user-header, .user-body {
        background-color: #fff !important;
    }

    @media (max-width: 767px) {
        .navbar .dropdown-menu li.user-body a {
            color: #455a64;
        }
    }


</style>
