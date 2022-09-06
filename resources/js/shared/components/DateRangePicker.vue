<template>
    <div class="datetime-range-picker">
        <template v-if="wrap">
            <div class="input-group" v-bind:class="{'input-group-sm': !!small}">
                <div class="input-group-prepend">
            <span class="input-group-text">
                <template v-if="!!addonLabel">{{ addonLabel }}</template>
                <i v-else class="fas fa-calendar-alt"></i>
            </span>
                </div>
                <input type="text"
                       class="form-control"
                       :class="{'form-control-sm': !!small}"
                       :value="dateRange"
                       :disabled="disabled"
                       ref="dateRangePicker">
            </div>
        </template>
        <template v-else>
            <input type="text"
                   class="form-control"
                   :class="{'form-control-sm': !!small}"
                   :value="dateRange"
                   :disabled="disabled"
                   ref="dateRangePicker">
        </template>
    </div>
</template>
<script>
// daterangepicker
const DateRangePicker = require('admin-lte/plugins/daterangepicker/daterangepicker');
export default {
    name: "DateRangePicker",
    props: {
        config: Object,
        value: {type: String, default: ''},
        wrap: {type: Boolean, default: true},
        small: {type: Boolean, default: false},
        disabled: {type: Boolean, default: false},
        addonLabel: {type: String, default: ''},
    },
    mounted() {
        this.$nextTick(() => {
            this.dateRange = String(this.value);
            if (!!this.config) {
                if (!!this.config.locale) {
                    this.config.locale.format = this.format;
                }
                new DateRangePicker(this.$refs.dateRangePicker, this.config, this.handleChange);
            }
        });
    },
    data() {
        return {
            dateRange: '',
        };
    },
    computed: {
        format() {
            let format = "YYYY-MM-DD";
            if (!!this.config && this.config.timePicker) {
                format += " HH:mm";
            }
            return format;
        },
    },
    methods: {
        /**
         *
         * @param {moment.Moment} startDate
         * @param {moment.Moment} endDate
         */
        handleChange(startDate, endDate) {
            if (this.config.singleDatePicker) {
                this.dateRange = startDate.format(this.format);
            } else {
                this.dateRange = startDate.format(this.format) + ' - ' + endDate.format(this.format);
            }
            this.$emit('input', this.dateRange);
        }
    }
}
</script>
<style lang="scss" scoped>
// daterangepicker
@import "~admin-lte/plugins/daterangepicker/daterangepicker.css";
</style>
