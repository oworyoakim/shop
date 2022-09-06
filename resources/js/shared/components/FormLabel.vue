<template>
    <label :class="className">
        {{text}}
        <span v-if="required" class="text-red">*</span>
        <slot></slot>
    </label>
</template>

<script>
export default {
    name: "FormLabel",
    props: {
        text: {type: String, required: true},
        className: {type: String, required: false, default: ''},
        required: {type: Boolean, required: false, default: false,},
    }
}
</script>

<style scoped>

</style>
