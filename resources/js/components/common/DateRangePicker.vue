<template>
    <input type="text"
           class="form-control"
           :value="dateRange"
           v-bind:class="{'is-invalid': !!hasErrors}"
           ref="dateRangePicker"
           id="dateRangePicker">
</template>

<script>
    export default {
        props: {
            config: Object,
            value: '',
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
