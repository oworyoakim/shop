<template>
    <div class="wrapper">
        <Header />
        <Sidebar :company-name="companyName" :company-short-name="companyShortName"/>
        <div class="content-wrapper">
            <section class="content">
                <Content />
            </section>
        </div>
        <Footer />
    </div>
</template>

<script>
import Footer from "./Footer";
import Header from "./Header";
import Sidebar from "./Sidebar";
import Content from "./Content";
export default {
    components: {Content, Sidebar, Header, Footer},
    props: ['companyName', 'companyShortName'],
}
</script>

<style scoped>

</style>
