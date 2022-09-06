<template>
    <div class="tab-pane fade active show" role="tabpanel" v-if="isActive">
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        title: {type: String, required: true},
    },
    data(){
        return {
            isActive: false,
        }
    },
}
</script>

<style scoped>

</style>
