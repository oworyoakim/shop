import axios from "axios";
import endPoints from "../end-points";

export default {
    state: {
        roles: [],
        permissions: [],
    },
    getters: {
        GET_ROLES: (state) => {
            return state.roles || [];
        },
        GET_PERMISSIONS: (state) => {
            return state.permissions || [];
        }
    },
    mutations: {
        SET_ROLES: (state, payload) => {
            state.roles = payload || [];
        },
        SET_PERMISSIONS: (state, payload) => {
            state.permissions = payload || [];
        },
    },
    actions: {
        GET_ROLES: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.ROLES);
                commit("SET_ROLES", response.data.roles);
                commit("SET_PERMISSIONS", response.data.permissions);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_ROLE: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.ROLES, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.ROLES, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_ROLE: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.ROLES + "?role_id=" + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        UPDATE_ROLE_PERMISSIONS: async ({commit}, payload) => {
            try {
                let response = await axios.patch(endPoints.ROLES,payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
    },
}
