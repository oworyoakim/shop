import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        categories: [],
    },
    getters: {
        GET_CATEGORIES: (state) => {
            return state.categories;
        }
    },
    mutations: {
        SET_CATEGORIES: (state, payload) => {
            state.categories = payload || [];
        }
    },
    actions: {
        GET_CATEGORIES: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.CATEGORIES);
                commit("SET_CATEGORIES", response.data);
                return Promise.resolve("Ok");
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        SAVE_CATEGORY: async ({commit}, payload) => {
            try {
                let response;
                if (!!payload.id) {
                    //update
                    response = await axios.put(endPoints.CATEGORIES, payload);
                } else {
                    //create
                    response = await axios.post(endPoints.CATEGORIES, payload);
                }
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        DELETE_CATEGORY: async ({commit}, payload) => {
            try {
                let response = await axios.delete(endPoints.CATEGORIES + '?category_id=' + payload.id);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    }
}
