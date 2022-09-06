<template>
    <div style="position:relative">
        <input type="text"
               class="form-control"
               :value="searchText"
               :required="required"
               id="autocompleteInput"
               :autofocus="autofocus"
               @keyup.prevent="filterItems($event.target.value)"
               @change.prevent="findItemByBarcode($event.target.value)"
               :placeholder="placeholder"
        >
        <ul class="dropdown-menu" style="width:100%;" v-bind:style="{display: searchText.length >= 3 ? 'block' : 'none'}">
            <template v-if="filteredItems.length >= 1">
                <li class="search-result-item"
                    v-for="item in filteredItems"
                    v-bind:class="{'active': item.value === selectedItem.value}"
                    @click="selectItem(item)">
                    {{item.text}}
                </li>
            </template>
            <li v-else class="search-result-item">Item <strong>{{searchText}}</strong> not found!</li>
        </ul>
    </div>
</template>

<script>
    import {deepClone} from "../../utils";

    export default {
        props: {
            selectItems: {
                type: Array,
                required: true,
                default: () => [],
            },
            value: '',
            placeholder: '',
            required: Boolean,
            autofocus: Boolean,
        },
        mounted() {
            if (this.value) {
                this.searchText = this.value;
            } else {
                this.searchText = this.selectedItem.value
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
            findItemByBarcode(barcode) {
                let item = this.selectItems.find((item) => {
                    return String(item.value).toLowerCase() === String(barcode).toLowerCase();
                });
                if (!!item) {
                    this.selectItem(item);
                }
            },
            selectItem(item) {
                this.selectedItem = deepClone(item);
                this.searchText = '';
                this.filteredItems = [];
                console.log(this.selectedItem);
                this.$emit('input', this.selectedItem.value);
                this.selectedItem = {
                    text: '',
                    value: ''
                };
            }
        },
    }
</script>

<style scoped>
    .search-result-item {
        cursor: pointer;
        padding: 5px;
    }
    .search-result-item:hover {
        background-color: rgb(247, 247, 247);
    }
</style>
