import {httpClient} from '@/utils/httpClient';
import endPoints from "../../../endPoints";
import {prepareQueryParams} from "../../../../utils/helpers";

export default {
    state: {
        categoriesInfo: null,
        itemsInfo: null,
        stocks: [],
    },
    getters: {
        CATEGORIES(state) {
            return state.categoriesInfo;
        },
        ITEMS(state) {
            return state.itemsInfo;
        },
        STOCKS(state) {
            return state.stocks || [];
        },
    },
    mutations: {
        SET_CATEGORIES(state, payload) {
            state.categoriesInfo = payload;
        },
        SET_ITEMS(state, payload) {
            state.itemsInfo = payload || null
        },
        SET_STOCKS(state, payload) {
            state.stocks = payload || []
        },
    },
    actions: {
        async GET_CATEGORIES({commit}, payload = {}) {
            try {
                let params = prepareQueryParams(payload);
                let response = await httpClient.get(endPoints.CATEGORIES + params);
                commit("SET_CATEGORIES", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async SAVE_CATEGORY({commit}, payload) {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await httpClient.put(endPoints.CATEGORIES + '/'+ payload.id, payload);
                } else {
                    //create
                    response = await httpClient.post(endPoints.CATEGORIES, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async DELETE_CATEGORY({commit}, payload) {
            try {
                let response = await httpClient.delete(endPoints.CATEGORIES + '/' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async GET_ITEMS({commit}, payload = {}) {
            try {
                let params = prepareQueryParams(payload);
                let response = await httpClient.get(endPoints.ITEMS + params);
                commit("SET_ITEMS", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async SAVE_ITEM({commit}, payload) {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await httpClient.put(endPoints.ITEMS + '/' + payload.id, payload);
                } else {
                    //create
                    response = await httpClient.post(endPoints.ITEMS, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async DELETE_ITEM({commit}, payload) {
            try {
                let response = await axios.delete(endPoints.ITEMS  + '/' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async GET_STOCKS({commit}, payload = {}) {
            try {
                let params = prepareQueryParams(payload);
                let response = await httpClient.get(endPoints.STOCKS  +  params);
                commit("SET_STOCKS", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
