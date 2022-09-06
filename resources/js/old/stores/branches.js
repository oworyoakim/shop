import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        branches: [],
    },
    getters: {
        GET_BRANCHES: (state) => {
            return state.branches;
        }
    },
    mutations: {
        SET_BRANCHES: (state, payload) => {
            state.branches = payload || [];
        }
    },
    actions: {
        GET_BRANCHES: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.BRANCHES);
                commit("SET_BRANCHES", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_BRANCH: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.BRANCHES, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.BRANCHES, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_BRANCH: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.BRANCHES + '?branch_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        LOCK_BRANCH: async ({commit}, payload) => {
            try {
                let response = await axios.patch(endPoints.BRANCHES + '/lock?branch_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        UNLOCK_BRANCH: async ({commit}, payload) => {
            try {
                let response = await axios.patch(endPoints.BRANCHES + '/unlock?branch_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
