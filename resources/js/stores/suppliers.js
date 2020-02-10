import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        suppliers: []
    },
    getters: {
        GET_SUPPLIERS: (state) => {
            return state.suppliers;
        }
    },
    mutations: {
        SET_SUPPLIERS: (state, payload) => {
            state.suppliers = payload || [];
        }
    },
    actions: {
        GET_SUPPLIERS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.SUPPLIERS);
                commit("SET_SUPPLIERS", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_SUPPLIER: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.SUPPLIERS, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.SUPPLIERS, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_SUPPLIER: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.SUPPLIERS + '?supplier_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
