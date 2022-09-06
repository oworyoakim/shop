import Vue from "vue";
import "../../bootstrap";

import posApp from "./App.vue";

Vue.component("pos-app", posApp);


import router from "./router";
import store from "./store";

const app = new Vue({
    store,
    router,
});
app.$mount("#pos-app");
