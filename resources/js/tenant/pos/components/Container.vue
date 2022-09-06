<template>
    <div class="wrapper">
        <Header
            :company-name="companyName"
            :company-short-name="companyShortName"
            :fixture="fixture"
            :combo-fixture="comboFixture"
        />
        <Sidebar
            :company-name="companyName"
            :company-short-name="companyShortName"
            :fixture="fixture"
            :combo-fixture="comboFixture"
        />
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
    name: "ShopContainer",
    props: {
        companyName: {
            type: String, default: "",
        },
        companyShortName: {
            type: String, default: "",
        },
        fixture: {
            type: String, default: "",
        },
        comboFixture: {
            type: String, default: "",
        },
    },
    components: {Sidebar, Header, Footer, Content}
}
</script>

<style scoped>

</style>
