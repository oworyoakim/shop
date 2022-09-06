<template>
    <input type="text"
           :class="inputClass"
           :value="value"
           :placeholder="placeholder"
           :title="title"
           :readonly="readonly"
           :disabled="disabled"
           v-bind:class="{'is-invalid': !!hasErrors}"
           @change.prevent="handleChange($event.target.value)"
           @input.prevent="handleInput($event.target.value)">
</template>

<script>
    export default {
        props: {
            value: [String, Number],
            placeholder: String,
            title: String,
            inputClass: String,
            readonly: Boolean,
            disabled: Boolean,
            hasErrors: Boolean,
        },
        data() {
            return {
                inputValue: '',
            }
        },
        methods: {
            handleInput(value) {
                this.inputValue = value;
                this.$emit('input', this.inputValue);
            },
            handleChange(value) {
                this.$emit('change', this.value);
            }
        }
    }
</script>

<style scoped>

</style>
