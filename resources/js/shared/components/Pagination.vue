<template>
    <ul v-if="!!items && items.hasPages" class="pagination pull-right" role="navigation">
        <!--        Previous Page Link -->
        <template v-if="items.currentPage <= 1">
            <li class="page-item disabled" aria-disabled="true">
                <a class="page-link" href="javascript:void(0);" rel="prev">First</a>
            </li>
            <li class="page-item disabled" aria-disabled="true">
                <a class="page-link" href="javascript:void(0);" rel="prev">Previous</a>
            </li>
        </template>
        <template v-else>
            <li class="page-item" :aria-label="items.firstPage">
                <a class="page-link" href="javascript:void(0);" @click="firstPage" rel="next">First</a>
            </li>
            <li class="page-item" :aria-label="items.previousPage">
                <a class="page-link" href="javascript:void(0);" @click="previousPage" rel="prev">Previous</a>
            </li>
        </template>
        <!--        Current Page -->
        <li class="page-item active disabled" aria-disabled="true" :aria-label="items.currentPage">
            <a class="page-link" href="javascript:void(0);" rel="current">
                <span class="text-red"> Page {{items.currentPage}} of {{items.lastPage}} </span>
                <span class="sr-only">(current)</span>
            </a>
        </li>
        <!--        Next Page Link -->
        <template v-if="items.hasMorePages">
            <li class="page-item" :aria-label="items.nextPage">
                <a class="page-link" href="javascript:void(0);" @click="nextPage" rel="next">Next</a>
            </li>
            <li class="page-item" :aria-label="items.lastPage">
                <a class="page-link" href="javascript:void(0);" @click="lastPage" rel="next">Last</a>
            </li>
        </template>
        <template v-else>
            <li class="page-item disabled" aria-disabled="true">
                <a class="page-link" href="javascript:void(0);" rel="next">Next</a>
            </li>
            <li class="page-item disabled" aria-disabled="true">
                <a class="page-link" href="javascript:void(0);" rel="next">Last</a>
            </li>
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
        previousPage() {
            this.$emit('gotoPage', this.items.previousPage);
        },
        nextPage() {
            this.$emit('gotoPage', this.items.nextPage);
        },
        firstPage() {
            this.$emit('gotoPage', this.items.firstPage);
        },
        lastPage() {
            this.$emit('gotoPage', this.items.lastPage);
        },
    }
}
</script>

<style scoped>

</style>
