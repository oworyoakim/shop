import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import endPoints from "./end-points";
import rolesModule from "./stores/roles";
import unitsModule from "./stores/units";
import categoriesModule from "./stores/categories";
import itemsModule from "./stores/items";
import suppliersModule from "./stores/suppliers";
import branchesModule from "./stores/branches";
import shopModule from "./stores/shop";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: null,
        formSelectionOptions: {
            categories: [],
            units: [],
            suppliers: [],
            customers: [],
        },
        countries:[],
    },
    getters: {
        GET_USER: (state) => {
            return state.user || null;
        },
        GET_FORM_SELECTION_OPTIONS: (state) => {
            return state.formSelectionOptions;
        },
        GET_COUNTRIES:(state) => {
            return state.countries;
        }
    },
    mutations: {
        SET_USER_DATA: (state, payload) => {
            state.user = payload;
        },
        SET_FORM_SELECTION_OPTIONS: (state, payload) => {
            state.formSelectionOptions = payload;
        },
        SET_COUNTRIES: (state, payload) => {
            state.countries = payload;
        },
    },
    actions: {
        LOGIN: async ({commit}, payload) => {
            try {
                let response = await axios.post(endPoints.LOGIN, payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        LOGOUT: async ({commit}) => {
            try {
                let response = await axios.post(endPoints.LOGOUT, {});
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_USER_DATA: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.GET_USER_DATA);
                commit("SET_USER_DATA", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_FORM_SELECTION_OPTIONS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.FORM_SELECTION_OPTIONS);
                commit("SET_FORM_SELECTION_OPTIONS", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_COUNTRIES: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.COUNTRIES);
                commit("SET_COUNTRIES", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
    },
    modules: {
        roles: rolesModule,
        units: unitsModule,
        categories: categoriesModule,
        items: itemsModule,
        branches: branchesModule,
        shop: shopModule,
        suppliers: suppliersModule,
    }
});
