import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        units: [],
    },
    getters: {
        GET_UNITS: (state) => {
            return state.units;
        }
    },
    mutations: {
        SET_UNITS: (state, payload) => {
            state.units = payload || [];
        }
    },
    actions: {
        GET_UNITS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.UNITS);
                commit("SET_UNITS", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_UNIT: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.UNITS, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.UNITS, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_UNIT: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.UNITS + '?unit_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
