import Vue from "vue";
import "../../bootstrap";

import managerApp from "./App.vue";

Vue.component("manager-app", managerApp);


import router from "./router";
import store from "./store";

const app = new Vue({
    store,
    router,
});
app.$mount("#manager-app");
