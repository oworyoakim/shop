<template>
    <nav class="navbar navbar-static-top">
        <div class="navbar-header">
            <!-- Menu toggle button-->
            <button type="button"
                    class="navbar-toggle collapsed"
                    data-toggle="collapse"
                    data-target="#navbarCollapse">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <!-- Navbar Left Menu -->
            <app-shop-left-menu/>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <app-shop-right-menu
                    :avatar="avatar"
                    :username="username"
                    :email="email"
                />
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        props: {
            username: String,
            email: String,
            avatar: String,
        },
    }
</script>

<style scoped>
    .navbar-toggle {
        background-color: inherit;
    }

    button.navbar-toggle:focus {
        outline: inherit;
    }

    @media (min-width: 767px) {
        .navbar-toggle, .collapsed {
            display: none;
        }
    }
</style>
