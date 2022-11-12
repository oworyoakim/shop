import {httpClient} from '@/utils/httpClient';
import {prepareQueryParams} from "@/utils/helpers";
import endPoints from "../../../endPoints";

export default {
    state: {
        purchasesInfo: null,
    },
    getters: {
        PURCHASES(state){
            return state.purchasesInfo || null;
        },
    },
    mutations: {
        SET_PURCHASES(state, payload){
            state.purchasesInfo = payload || null;
        },
    },
    actions: {
        async GET_PURCHASES({commit}, payload = {}){
            try {
                let params = prepareQueryParams(payload);
                let response = await httpClient.get(endPoints.PURCHASES + params);
                commit("SET_PURCHASES", response.data);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        }
    },
}
