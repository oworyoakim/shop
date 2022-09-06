<template>
    <div class="search-select" :class="{ 'is-active': isOpen }">
        <button @click="toggleSelector()" type="button" class="btn search-select-input" v-bind:class="{'btn-sm': !!small}">
            <span v-if="selectedOption">{{ selectedOption.text }}</span>
            <span v-else class="search-select-placeholder" @click="select()">{{ placeholder }}</span>
            <span class="search-select-input-addon">
        <i class="caret"></i>
      </span>
        </button>
        <div v-show="isOpen" class="search-select-dropdown">
            <input v-model="search"
                   class="form-control search-select-search"
                   v-bind:class="{'form-control-sm': !!small}"
                   placeholder="Type to search ...">
            <ul v-show="filteredOptions.length > 0" class="search-select-options">
                <li class="search-select-option" v-if="selectedOption && !required" @click="select()">{{ placeholder }}</li>
                <li class="search-select-option" :class="{active: selectedOption && selectedOption.value === option.value}"
                    v-for="option in filteredOptions"
                    :key="option.value"
                    @click="select(option)"
                >{{ option.text }}</li>
            </ul>
            <div v-if="!!search && filteredOptions.length === 0" class="search-select-empty">No results found for "{{ search }}"</div>
        </div>
        <template v-if="name">
            <input type="hidden" :name="name" :value="selectedOption ? selectedOption.value : ''" :required="required">
        </template>
    </div>
</template>

<script>
export default {
    name: "SelectBoxInput",
    props: {
        options: {type: Array, require: true, default: () => ([])},
        name: {type: String, require: false},
        value: {type: String|Number, require: true, default: ''},
        placeholder: {type: String, default: 'Type to search ...'},
        required: {type: Boolean, default: false},
        small: {type: Boolean, default: false},
    },
    data() {
        return {
            isOpen: false,
            search: "",
            selectedOption: null
        }
    },
    watch: {
        value(newValue, oldValue) {
            if (!!newValue) {
                this.selectedOption = this.options.find((option) => option.value === newValue );
            }
        }
    },
    created() {
        window.addEventListener("click", this.close);
    },
    beforeDestroy() {
        window.removeEventListener("click", this.close);
    },
    mounted () {
        if (!!this.value) {
            this.selectedOption = this.options.find((option) => option.value === this.value );
        }
    },
    computed: {
        filteredOptions() {
            return this.options.filter(option =>
                option.text.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    },
    methods: {
        toggleSelector() {
            this.isOpen = !this.isOpen;
        },
        close(e) {
            if (!this.$el.contains(e.target)) {
                this.isOpen = false;
            }
        },
        select(option = null) {
            this.selectedOption = option;
            this.search = "";
            if (option) {
                this.$emit('input', this.selectedOption.value);
            } else {
                this.$emit('input', "");
            }
            this.isOpen = false;
        }
    }
}
</script>

<style scoped>
.search-select {
    position: relative;
    display: block;
    width: 100%;
    height: 36px;
    font-size: 14px;
    line-height: 1.6;
    color: #555555;
    background-color: #fff;
    background-image: none;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.08);
    -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}
.search-select-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    text-align: left;
    display: block;
    width: 100%;
    border: 1px solid #90999f;
    border-radius: 4px;
    padding: 6px 12px;
    background-color: #fff;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.search-select-input-addon {
    font-weight: 600;
    position: absolute;
    right: 10px;
}
.search-select-dropdown {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
    position: absolute;
    right: 0;
    left: 0;
    padding: 0.3rem;
    border-radius: 0.25rem;
    background-color: #fff;
    z-index: 9999;
    max-height: 274px;
    overflow: hidden;
    box-shadow: 0 0 3px 1px rgba(124,179,84,0.30);
}
.search-select-search {
    display: block;
    margin-bottom: 0.2rem;
    width: 100%;
    height: 36px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.6;
    color: #555555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #90999f;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.08);
    -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}
.search-select-options {
    list-style: none;
    padding: 0;
    position: relative;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    max-height: 14rem;
}
.search-select-option {
    padding: 0.5rem 0.75rem;
    color: #555555;
    background-color: #fff;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.search-select-option:hover {
    background-color: #83c8e7;
    color: #ffffff;
}
.search-select-option.active {
    background-color: #25aae1;
    color: #ffffff;
}
.search-select-empty {
    padding: 0.5rem 0.75rem;
    color: #FF0000;
}
</style>
