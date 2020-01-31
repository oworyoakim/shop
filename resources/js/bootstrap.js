import Vue from  'vue';
import axios from 'axios';
import moment from "moment";
import numeral from "numeral";
window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js').default;
require('bootstrap');
window._ = Vue.prototype.$lodash = require('lodash');
window.moment = Vue.prototype.$moment = moment;
window.numeral = Vue.prototype.$numeral = numeral;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

let url = document.head.querySelector('meta[name="base-url"]');
axios.defaults.baseURL = url.content;

export const baseUrl = url.content;

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.axios = Vue.prototype.$http = axios;

window.toastr = require('toastr');
window.swal = window.sweetAlert = require('sweetalert');
require('select2');
require("../../public/admin/js/jquery.slimscroll.min.js");
require("../../public/admin/js/datatables.min.js");
require("../../public/admin/js/template.js");
// bootstrap-daterangepicker
require('../../public/admin/bootstrap-daterangepicker/daterangepicker');
// bootstrap-datepicker
require('../../public/admin/bootstrap-datepicker/js/bootstrap-datepicker.min');

