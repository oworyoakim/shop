<template>
    <Container :company-name="companyName" :company-short-name="companyShortName"/>
</template>

<script>
import Container from "./components/Container";

export default {
    components: {Container,},
    props: ['companyName', 'companyShortName'],
    mounted() {
        this.getUser();
    },
    methods: {
        getUser() {
            return this.$store.dispatch('GET_LOGGED_IN_USER');
        },
    }
}
</script>

<style scoped>

</style>
