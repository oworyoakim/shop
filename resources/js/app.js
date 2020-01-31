require('./bootstrap');

import Vue from 'vue';

import store from "./store";
import router from "./router";
import Header from "./components/common/Header";

import Sidebar from "./components/common/Sidebar";
import AdminMenu from "./components/common/AdminMenu";
import ShopMenu from "./components/common/ShopMenu";
import Footer from "./components/common/Footer";
import Login from "./components/Login";
import Container from "./components/Container";
import Select2Input from "./components/common/Select2Input";
import DateRangePicker from "./components/common/DateRangePicker";

Vue.component('app-select-box', Select2Input);
Vue.component('app-date-range-picker', DateRangePicker);
Vue.component('app-header', Header);
Vue.component('app-sidebar', Sidebar);
Vue.component('app-footer', Footer);
Vue.component('app-admin-menu', AdminMenu);
Vue.component('app-shop-menu', ShopMenu);
Vue.component('app-login', Login);
Vue.component('app-container', Container);

// Create an Event Bus for communications
export const EventBus = new Vue();

const app = new Vue({
    el: '#main-app',
    store: store,
    router: router,
});

