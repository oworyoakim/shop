import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

import {httpClient} from "@/utils/httpClient";
import {prepareQueryParams, resolveError} from "@/utils/helpers";

import globalState from "../../../shared/store/state";
import globalGetters from "../../../shared/store/getters";
import globalMutations from "../../../shared/store/mutations";
import globalActions from "../../../shared/store/actions";
import branches from "./modules/branches";

export default new Vuex.Store({
    modules: {
        branches,
    },
    state: {
        ...globalState,
        user: null,
    },
    getters: {
        ...globalGetters,
        LOGGED_IN_USER(state) {
            return state.user || null;
        },
        HAS_ACCESS(state){
            return (permission = '') => {
                return !!permission && !!state.user && !!state.user.permissions[permission];
            }
        },
        HAS_ANY_ACCESS(state){
            return (permissions = []) => {
                return !!state.user && permissions.some((permission) => !!state.user.permissions[permission]);
            }
        },
    },
    mutations: {
        ...globalMutations,
        SET_LOGGED_IN_USER (state, payload)  {
            state.user = payload || null;
        },
    },
    actions: {
        ...globalActions,
        async LOGOUT({commit}) {
            try {
                let response = await httpClient.post('/logout', {});
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },
        async GET_LOGGED_IN_USER ({commit}) {
            try {
                let response = await httpClient.get('/user-data');
                commit('SET_LOGGED_IN_USER', response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },
        async GET_FORM_OPTIONS({commit}, payload = {}) {
            try {
                let params = prepareQueryParams(payload)
                let response = await httpClient.get('/form-options' + params);
                commit("SET_FORM_OPTIONS", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },
        async CHANGE_PASSWORD({commit}, payload= {}) {
            try {
                let response = await httpClient.patch('/change-password', payload)
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },
    },
});
