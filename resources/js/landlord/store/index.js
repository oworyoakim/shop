import Vue from "vue";
import Vuex from "vuex";
import moment from "moment";
import {httpClient} from "../../utils/httpClient";
import {prepareQueryParams, resolveError} from "../../utils/helpers";

Vue.use(Vuex);


import units from "./modules/units";
import tenants from "./modules/tenants";
import users from "./modules/users";

export default new Vuex.Store({
    modules: {
       units,
    },
    state: {
        user: null,
        dashboardStatistics: [],
        formOptions: {
            admins: [],
            countries: [],
            tenants: [],
            permissions: [],
        },
        singleDatePickerConfig: {
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            maxDate: moment(), // today
            opens: "center",
            locale: {
                format: "YYYY-MM-DD"
            }
        },
        dashboardDateRangePickerConfig: {
            showDropdowns: true,
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 3 Days': [moment().subtract(3, 'days').startOf('day'), moment()],
                'This Week': [moment().startOf('isoWeek'), moment()],
                'Last Week': [moment().subtract(6,'day').startOf('isoWeek'), moment().subtract(6,'day').endOf('isoWeek')],
                'This Month': [moment().startOf('month'), moment()],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            maxDate: moment(), // Current day
            opens: "center",
            locale: {
                format: 'YYYY-MM-DD'
            }
        },
    },
    getters: {
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
        DASHBOARD_STATISTICS(state){
            return state.dashboardStatistics;
        },
        FORM_OPTIONS(state) {
            return state.formOptions;
        },
        singleDatePickerConfig(state){
            return state.singleDatePickerConfig || null;
        },
        dashboardDateRangePickerConfig(state){
            return state.dashboardDateRangePickerConfig || null;
        },
    },
    mutations: {
        SET_LOGGED_IN_USER (state, payload)  {
            state.user = payload || null;
        },
        SET_DASHBOARD_STATISTICS(state, payload){
            state.dashboardStatistics = payload || [];
        },
        SET_FORM_OPTIONS(state, payload){
            state.formOptions = payload;
        },
    },
    actions: {
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
        async GET_DASHBOARD_STATISTICS({commit}, payload = {}) {
            try {
                let params = prepareQueryParams(payload);
                let response = await httpClient.get("/dashboard-statistics" + params);
                commit("SET_DASHBOARD_STATISTICS", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },
        async SAVE_SETTINGS({commit}, payload = {}) {
            try {
                let response = await httpClient.put('/v1/settings', payload);
                return Promise.resolve(response.data);
            } catch (error) {
                let message = resolveError(error);
                return Promise.reject(message);
            }
        },

        async GET_SETTINGS({commit}) {
            try {
                let response = await httpClient.get('/v1/settings');
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
