<template>
    <input :type="type"
           :value="value"
           class="form-control"
           v-bind:class="[{'form-control-sm': !!small}, {'is-invalid': !!isInvalid},  {'text-center': !!centered}]"
           @input="handleInputChange($event.target.value)"
           :placeholder="placeholder"
           :required="required"
           :readonly="readonly">
</template>

<script>
/**
 * Generic input for use with text, password, and email input types
 */
export default {
    name: "TextInput",
    props: {
        type: {type: String, default: 'text'},
        value: {type: String|Number,require: true},
        required: {type: Boolean, default: false},
        readonly: {type: Boolean, default: false},
        isInvalid: {type: Boolean, default: false},
        small: {type: Boolean, default: false},
        centered: {type: Boolean, default: false},
        placeholder: {type: String, default: ''},
    },
    methods: {
        handleInputChange(val) {
            this.$emit("input", val);
        }
    }
}
</script>

<style scoped>

</style>
