<template>
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Home</a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-bold d-flex align-items-center"
                       @click="logout()"
                       data-widget="control-sidebar"
                       href="javascript:void(0)"
                       role="button">
                        <i class="fas fa-power-off mr-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
</template>

<script>
export default {
    name: "Header",
    props: ['title'],
    methods: {
        async logout() {
            try {
                let response = await this.$store.dispatch("LOGOUT");
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: response,
                    icon: 'success'
                });
                location.reload();
            } catch (error) {
                console.error(error);
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: error,
                    icon: 'error'
                });
            }
        }
    }
}
</script>

<style scoped>

</style>
