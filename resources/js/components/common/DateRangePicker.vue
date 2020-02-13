<template>
    <input type="text"
           :value="dateRange"
           v-bind:class="inputCssClass"
           ref="dateRangePicker"
           id="dateRangePicker">
</template>

<script>
    export default {
        props: {
            config: Object,
            value: '',
            inputClass: '',
            hasErrors: Boolean,
        },
        mounted() {
            this.dateRange = String(this.value);
            this.$nextTick(() => {
                $(this.$refs.dateRangePicker).daterangepicker(this.config, this.handleChange);
            });
        },
        data() {
            return {
                dateRange: '',
            };
        },
        computed:{
            inputCssClass(){
                let cssClass = "form-control " + this.inputClass;
                if(!!this.hasErrors){
                    cssClass += " is-invalid";
                }
              return cssClass;
            },
        },
        methods: {
            handleChange(startDate, endDate) {
                if (this.config.singleDatePicker) {
                    this.dateRange = startDate.format(this.config.locale.format || 'YYYY-MM-DD');
                } else {
                    this.dateRange = startDate.format(this.config.locale.format || 'YYYY-MM-DD') + ' - ' + endDate.format(this.config.locale.format || 'YYYY-MM-DD');
                }
                this.$emit('input', this.dateRange);
            }
        }
    }
</script>
