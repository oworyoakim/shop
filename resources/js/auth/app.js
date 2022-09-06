import Vue from "vue";
import "../bootstrap";

import AuthApp from "./App.vue";

Vue.component("auth-app", AuthApp);


import store from "./store";

const app = new Vue({
    store: store,
});
app.$mount("#auth-app");
