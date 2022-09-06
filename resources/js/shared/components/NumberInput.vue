<template>
    <input type="text"
           :value="value | separator"
           class="form-control"
           v-bind:class="[{'form-control-sm': !!small}, {'is-invalid': !!isInvalid}, {'text-center': !!centered}]"
           ref="numberInput"
           @keyup="setValue($event.target.value)"
           :required="!!required"
           :readonly="!!readonly"
           @keyup.enter="handleEnterKeyPressed($event.target.value)">
</template>

<script>
import numeral from "numeral";
export default {
    name: "NumberInput",
    props: {
        value: {type: Number|String, default: '',},
        required: {type: Boolean, default: false,},
        readonly: {type: Boolean, default: false,},
        small: {type: Boolean, default: false,},
        isInvalid: {type: Boolean, default: false},
        centered: {type: Boolean, default: false},
        min: {type: Number|String, default: 0},
        max: {type: Number|String, default: ''},
    },
    watch: {
        value(newVal, oldVal){
            if(!!newVal) {
                let value = numeral(newVal).value();
                if(value > 0) {
                    this.$emit("input", value);
                }
            }
        },
    },
    methods: {
        setValue(val) {
            let value = 0;
            if(!!val) {
                value = numeral(val).value();
            }
            let min = numeral(this.min ?? 0).value();
            let max = numeral(this.max).value();
          if(value >= min  && !(max > 0 && value > max)) {
              this.$emit("input", value);
          }
        },
        handleEnterKeyPressed(val) {
            this.$emit("enterKeyPressed", val);
        },
    },
}
</script>

<style scoped>

</style>
