<template>
    <div class="card card-outline card-outline-tabs" :class="`card-${variant || 'primary'}`">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" role="tablist">
                <template v-for="(tab, index) in tabs">
                    <li class="nav-item" :key="tab.title">
                        <a class="nav-link"
                           :class="{'active': !!tab.isActive || index === activeTabIndex}"
                           role="tab"
                           :aria-selected="!!tab.isActive || index === activeTabIndex"
                           @click="selectTab(index)">
                            {{tab.title}}
                        </a>
                    </li>
                </template>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <slot></slot>
            </div>
        </div>
        <!-- /.card -->
    </div>
</template>

<script>
export default {
    name: "Tabs",
    props: {
        variant: {type: String, default: 'primary'}
    },
    data(){
        return {
            tabs: [], // all the tabs
            activeTabIndex: 0 // the index of the selected tab,
        }
    },
    created() {
        this.tabs = this.$children;
    },
    mounted() {
        this.selectTab(0);
    },
    methods: {
        selectTab(i) {
            this.activeTabIndex = i;
            // loop over all the tabs
            this.tabs.forEach((tab, index) => {
                tab.isActive = (index === i);
            });
        }
    }
}
</script>

<style scoped>

</style>
