import axios from 'axios';
import endPoints from '../end-points';
import Basket from "../models/Basket";

export default {
    state: {
        shopInfo: {
            branchId: null,
            branchName: '',
            branchBalance: 0,
            branchCashiersBalance: 0,
            branchStockAtHand: 0,
            vatRate: 0,
            canCreateItem: false,
            canCreateSupplier: false,
        },
        salableProducts: [],
        purchasableProducts: [],
        basket: new Basket(),
    },
    getters: {
        GET_SHOP_INFO: (state) => {
            return state.shopInfo;
        },
        GET_SALABLE_PRODUCTS: (state) => {
            return state.salableProducts;
        },
        GET_PURCHASABLE_PRODUCTS: (state) => {
            return state.purchasableProducts;
        },
        GET_BASKET: (state) => {
            return state.basket;
        },
    },
    mutations: {
        SET_SHOP_INFO: (state, payload) => {
            state.shopInfo = payload || {};
        },
        SET_SALABLE_PRODUCTS: (state, payload) => {
            state.salableProducts = payload || [];
        },
        SET_PURCHASABLE_PRODUCTS: (state, payload) => {
            state.purchasableProducts = payload || [];
        },
        CLEAR_BASKET: (state, payload) => {
            let basket = state.basket;
            basket.clear();
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_TAX_RATE: (state, payload) => {
            let basket = state.basket;
            basket.setTaxRate(payload);
            state.basket = Object.assign(new Basket(), basket);
        },
        ADD_ITEM: (state, payload) => {
            let basket = state.basket;
            basket.addItem(payload.barcode, payload.item);
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_ITEM_QUANTITY: (state, payload) => {
            let basket = state.basket;
            basket.setItemQuantity(payload.barcode, payload.quantity);
            state.basket = Object.assign(new Basket(), basket);
        },
        UPDATE_ITEM_QUANTITY: (state, payload) => {
            let basket = state.basket;
            basket.updateItemQuantity(payload.barcode, payload.quantity);
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_ITEM_DISCOUNT: (state, payload) => {
            let basket = state.basket;
            basket.setItemDiscount(payload.barcode, payload.rate);
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_ITEM_PRICE: (state, payload) => {
            let basket = state.basket;
            basket.setItemPrice(payload.barcode, payload.price);
            state.basket = Object.assign(new Basket(), basket);
        },
        REMOVE_ITEM: (state, payload) => {
            let basket = state.basket;
            basket.removeItem(payload.barcode);
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_TENDERED_AMOUNT: (state, payload) => {
            let basket = state.basket;
            basket.setTenderedAmount(payload.amount);
            state.basket = Object.assign(new Basket(), basket);
        },
        SET_PAID_AMOUNT: (state, payload) => {
            let basket = state.basket;
            basket.setPaidAmount(payload.amount);
            state.basket = Object.assign(new Basket(), basket);
        },
    },
    actions: {
        GET_SHOP_INFO: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.SHOP_INFO);
                commit('SET_SHOP_INFO', response.data);
                return Promise.resolve('Ok');
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_ITEM_BY_BARCODE: async ({commit}, payload) => {
            try {
                let response = await axios.get(endPoints.GET_ITEM_BY_BARCODE + '?barcode=' + payload.barcode);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_SALABLE_PRODUCTS: async ({commit}, payload = null) => {
            try {
                let param = "";
                if (!payload) {
                    param += "branch_id=" + payload.branch_id;
                }
                let response = await axios.get(endPoints.SALABLE_PRODUCTS + '?' + param);
                commit('SET_SALABLE_PRODUCTS', response.data);
                return Promise.resolve('Ok');
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        GET_PURCHASABLE_PRODUCTS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.PURCHASABLE_PRODUCTS);
                commit('SET_PURCHASABLE_PRODUCTS', response.data);
                return Promise.resolve('Ok');
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        COMPLETE_SALE_TRANSACTION: async ({commit}, payload) => {
            try {
                let response = await axios.post(endPoints.COMPLETE_SALE_TRANSACTION, payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        COMPLETE_PURCHASE_TRANSACTION: async ({commit}, payload) => {
            try {
                let response = await axios.post(endPoints.COMPLETE_PURCHASE_TRANSACTION, payload);
                return Promise.resolve(response.data);
            } catch (error) {
                return Promise.reject(error.response.data);
            }
        },
        CLEAR_BASKET: ({commit}) => {
            commit('CLEAR_BASKET');
        },
        SET_TAX_RATE: ({commit}, payload) => {
            commit('SET_TAX_RATE', payload);
        },
        ADD_ITEM: ({commit}, payload) => {
            commit('ADD_ITEM', payload);
        },
        SET_ITEM_QUANTITY: ({commit}, payload) => {
            commit('SET_ITEM_QUANTITY', payload);
        },
        UPDATE_ITEM_QUANTITY: ({commit}, payload) => {
            commit('UPDATE_ITEM_QUANTITY', payload);
        },
        SET_ITEM_DISCOUNT: ({commit}, payload) => {
            commit('SET_ITEM_DISCOUNT', payload);
        },
        SET_ITEM_PRICE: ({commit}, payload) => {
            commit('SET_ITEM_PRICE', payload);
        },
        REMOVE_ITEM: ({commit}, payload) => {
            commit('REMOVE_ITEM', payload);
        },
        SET_TENDERED_AMOUNT: ({commit}, payload) => {
            commit('SET_TENDERED_AMOUNT', payload);
        },
        SET_PAID_AMOUNT: ({commit}, payload) => {
            commit('SET_PAID_AMOUNT', payload);
        },
    }
}
