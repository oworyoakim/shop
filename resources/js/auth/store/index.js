import Vue from "vue";
import Vuex from "vuex";
import {resolveError} from "../../utils/helpers";
import {httpClient} from "../../utils/httpClient";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {},
    state: {},
    getters: {},
    mutations: {},
    actions: {
        async LOGIN({commit}, payload) {
            try {
                let response = await httpClient.post("/login", payload);
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        }
    },
});
