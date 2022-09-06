import Vue from "vue";
import "../bootstrap";

import landlordApp from "./App.vue";

Vue.component("landlord-app", landlordApp);


import router from "./router";
import store from "./store";

const app = new Vue({
    store,
    router,
});
app.$mount("#landlord-app");
