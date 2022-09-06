require('./bootstrap');

import Vue from 'vue';

import store from "./store";

import router from "./router";

import Header from "./components/common/Header";
import Sidebar from "./components/common/Sidebar";
import AdminMenu from "./components/common/AdminMenu";
import Footer from "./components/common/Footer";
import TextBox from "./components/common/TextBox";
import Login from "./components/Login";
import Container from "./components/Container";
import ShopContainer from "./components/ShopContainer";
import Select2Input from "./components/common/Select2Input";
import AutocompleteInput from "./components/common/AutocompleteInput";
import DateRangePicker from "./components/common/DateRangePicker";
import ShopNavbar from "./components/common/ShopNavbar";
import RightMenu from "./components/common/RightMenu";
import LeftMenu from "./components/common/LeftMenu";
import ItemForm from "./components/admin/items/ItemForm";

Vue.component('app-select-box', Select2Input);
Vue.component('app-autocomplete-input', AutocompleteInput);
Vue.component('app-date-range-picker', DateRangePicker);
Vue.component('app-header', Header);
Vue.component('app-sidebar', Sidebar);
Vue.component('app-footer', Footer);
Vue.component('app-admin-menu', AdminMenu);
Vue.component('app-shop-navbar', ShopNavbar);
Vue.component('app-shop-left-menu', LeftMenu);
Vue.component('app-shop-right-menu', RightMenu);
Vue.component('app-login', Login);
Vue.component('app-container', Container);
Vue.component('app-shop-container', ShopContainer);
Vue.component('app-item-form', ItemForm);
Vue.component('app-text-box', TextBox);

// Create an Event Bus for communications
export const EventBus = new Vue();

const app = new Vue({
    el: '#main-app',
    store: store,
    router: router,
});

