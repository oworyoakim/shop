import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        items: [],
    },
    getters: {
        GET_ITEMS: (state) => {
            return state.items;
        }
    },
    mutations: {
        SET_ITEMS: (state, payload) => {
            state.items = payload || [];
        }
    },
    actions: {
        GET_ITEMS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.ITEMS);
                commit("SET_ITEMS", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_ITEM: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.ITEMS, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.ITEMS, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_ITEM: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.ITEMS + '?item_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
