<template>
    <Container :companyName="companyName" />
</template>

<script>
import Container from "./components/Container";

export default {
    props: ['companyName'],
    components: {Container,}
}
</script>

<style scoped>

</style>
