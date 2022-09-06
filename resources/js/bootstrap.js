/**
 * First we will load Vue. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue';
window.Vue = Vue;

import moment from "moment";
import numeral from "numeral";

/**
 * We'll load jQuery, Bootstrap jQuery plugin, and all of this project's JavaScript dependencies which provides support
 * for JavaScript based Bootstrap features such as modals and tabs.
 */
try {
    window._ = require('lodash');
    window.Popper = require('admin-lte/plugins/popper/popper.min.js');
    // jQuery
    window.$ = window.jQuery = Vue.prototype.$jquery = require('admin-lte/plugins/jquery/jquery.min');
    // Bootstrap 4.6
    require('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min');
    // AdminLTE App
    require('admin-lte/dist/js/adminlte.min');
    Vue.prototype.$moment = moment;
    window.numeral = Vue.prototype.$numeral = numeral;
} catch (error) {
    console.error(error);
}

/**
 * Next we will register global Vue Mixins accessible from any Vue component.
 */
Vue.mixin({
    methods: {
        deepClone(object) {
            //return JSON.parse(JSON.stringify(object));
            return _.cloneDeep(object);
        },
        isEqual(object1, object2) {
            return _.isEqual(object1, object2);
        },
    },
});

/**
 * Next we will register global Vue Filters accessible from any Vue component.
 */
Vue.filter('separator', (amount) => {
    if(!!!amount){
        return '';
    }
    return numeral(amount).format('0,0');
});
Vue.filter('percentage', (value) => {
    return numeral(value).format('0.[00]%');
});
