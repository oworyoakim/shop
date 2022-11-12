<template>
    <ul class="pagination pagination-sm m-0 float-right" v-if="!!items">
        <template v-for="link in items.links">
            <template v-if="!link.url || link.active">
                <li class="page-item disabled">
                    <a class="page-link disabled" href="javascript:void(0);">
                        <span v-html="link.label"></span>
                    </a>
                </li>
            </template>
            <template v-else>
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" @click="gotoPage(link.url)">
                        <span v-html="link.label"></span>
                    </a>
                </li>
            </template>
        </template>
    </ul>
</template>

<script>
export default {
    props: {
        items: {
            type: Object,
            required: true,
            default: () => null
        }
    },
    methods: {
        gotoPage(url) {
            let page = this.getPageNumberFromUrl(url);
            if (page > 0) {
                this.$emit("gotoPage", page);
            }
        },
        getPageNumberFromUrl(url) {
            let urlObject = new URL(url);
            let queryStrings = urlObject.search;
            let urlParams = new URLSearchParams(queryStrings);
            return Number(urlParams.get('page'));
        },
    },
}
</script>

<style scoped>

</style>
