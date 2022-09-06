<template>
    <textarea v-model="textValue"
              class="form-control"
              v-bind:class="{'form-control-sm': !!small, 'is-invalid': !!isInvalid}"
              :placeholder="placeholder"
              :required="required"
              :readonly="readonly"
              :rows="rows">
        {{textValue}}
    </textarea>
</template>

<script>
export default {
    name: "TextAreaInput",
    props: {
        value: {type: String|Number,require: true},
        required: {type: Boolean, default: false},
        readonly: {type: Boolean, default: false},
        isInvalid: {type: Boolean, default: false},
        placeholder: {type: String, default: ''},
        small: {type: Boolean, default: false},
        rows: {type: String|Number, default: 4},
    },
    created() {
        this.textValue = this.value;
    },
    data(){
        return {
            textValue: "",
        }
    },
    watch: {
        textValue(newVal, oldVal){
            this.handleInputChange(newVal);
        }
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
