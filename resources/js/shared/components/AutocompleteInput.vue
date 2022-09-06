<template>
    <div style="position:relative">
        <input type="text"
               class="form-control"
               :class="{'form-control-sm': !!this.small}"
               :value="searchText"
               :required="required"
               ref="autocompleteInput"
               :autofocus="autofocus"
               @keyup.prevent="filterItems($event.target.value)"
               @change.prevent="setSelectedItem($event.target.value)"
               :placeholder="placeholder">
        <ul class="dropdown-menu search-result-items"
            v-bind:style="{display: filteredItems.length >= 1 ? 'block' : 'none'}">
            <template v-if="filteredItems.length >= 1">
                <li class="search-result-item"
                    v-for="item in filteredItems"
                    v-bind:class="{'active': item.value === selectedItem.value}"
                    @click="selectItem(item)">
                    {{ item.text }}
                </li>
            </template>
            <li v-else class="search-result-item">No matches!</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        selectItems: {
            type: Array,
            required: true,
            default: () => [],
        },
        value: {type: String|Number, default: ''},
        placeholder: {type: String, default: 'Type to search...'},
        small: {type: Boolean, default: false},
        required: {type: Boolean, default: false},
        autofocus: {type: Boolean, default: false},
    },
    mounted() {
        if (this.value) {
            //this.searchText = this.value;
            this.setSelectedItem(this.value);
        }
    },
    data() {
        return {
            selectedItem: {
                text: '',
                value: ''
            },
            filteredItems: [],
            searchText: '',
        }
    },
    methods: {
        filterItems(value) {
            this.searchText = value;
            if (this.searchText.length < 3) {
                this.filteredItems = [];
                return;
            }
            this.filteredItems = this.selectItems.filter((item) => {
                return String(item.text).toLowerCase().includes(this.searchText.toLowerCase());
            });
        },
        selectItem(item) {
            this.selectedItem = this.deepClone(item);
            this.searchText = '';
            if (!!item) {
                this.searchText = item.text;
            }
            this.filteredItems = [];
            console.log(this.selectedItem);
            this.$emit('input', this.selectedItem.value);
            this.selectedItem = {
                text: '',
                value: ''
            };
        },
        setSelectedItem(value) {
            if (!!value) {
                let selectedItem = this.selectItems.find((item) => {
                    return item.value == this.value;
                });
                if (selectedItem) {
                    this.searchText = selectedItem.text;
                } else {
                    this.searchText = '';
                    this.$emit('input', null);
                }
            } else {
                this.searchText = '';
                this.$emit('input', null);
                this.filteredItems = [];
            }
        }
    },
}
</script>

<style scoped>
.search-result-items {
    width: 100%;
    max-height: 200px;
    overflow-y: scroll;
}

.search-result-item {
    cursor: pointer;
    padding: 5px;
}

.search-result-item:hover {
    background-color: rgb(247, 247, 247);
}
</style>
