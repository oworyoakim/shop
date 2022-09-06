import Vue from "vue";
import "../../bootstrap";

import adminApp from "./App.vue";

Vue.component("admin-app", adminApp);


import router from "./router";
import store from "./store";

const app = new Vue({
    store,
    router,
});
app.$mount("#admin-app");
