<template>
    <select class="form-control"
            ref="select2Dropdown"
            style='width: 100%'
            v-bind:class="{'is-invalid': !!hasErrors}"
            :value="value"
            :required="required"
            :multiple="multiple">
        <option value="">{{placeholder || 'Select...'}}</option>
        <option v-for="option in selectOptions" :value="option.value">{{option.text}}</option>
    </select>
</template>

<script>
    export default {
        props: {
            config: Object,
            selectOptions: {
                type: Array,
                default: [],
                required: true,
            },
            value: '',
            placeholder: '',
            required: Boolean,
            multiple: {
                type: Boolean,
                required: false,
                default: false
            },
            hasErrors: Boolean,
        },
        mounted() {
            setTimeout(() => {
                $(this.$refs.select2Dropdown).select2(this.config).on('change', this.handleChange);
                $(this.$refs.select2Dropdown).val(this.value).trigger('change');
            }, 1500);
        },
        data() {
            return {
                selectedItem: '',
            };
        },
        methods: {
            handleChange(event) {
                this.selectedItem = event.target.value;
                // console.log(this.selectedItem);
                this.$emit('input', this.selectedItem);
            }
        }
    }
</script>
