import {httpClient} from "@/utils/httpClient";
import {prepareQueryParams, resolveError} from "@/utils/helpers";
import endPoints from '../../../endPoints';

export default {
    state: {
        branches: [],
    },
    getters: {
        GET_BRANCHES(state) {
            return state.branches;
        }
    },
    mutations: {
        SET_BRANCHES(state, payload) {
            state.branches = payload || [];
        }
    },
    actions: {
        async GET_BRANCHES({commit}) {
            try {
                let response = await httpClient.get(endPoints.BRANCHES);
                commit("SET_BRANCHES", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async SAVE_BRANCH({commit}, payload) {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await httpClient.put(endPoints.BRANCHES + '/' + payload.id, payload);
                } else {
                    //create
                    response = await httpClient.post(endPoints.BRANCHES, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async DELETE_BRANCH({commit}, payload) {
            try {
                let response = await httpClient.delete(endPoints.BRANCHES + '/' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async LOCK_BRANCH({commit}, payload) {
            try {
                let response = await httpClient.patch(endPoints.BRANCHES + '/' + payload.id + '/lock', payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        async UNLOCK_BRANCH({commit}, payload) {
            try {
                let response = await httpClient.patch(endPoints.BRANCHES + '/' + payload.id + '/unlock', payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
