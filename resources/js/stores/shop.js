import axios from 'axios';
import endPoints from '../end-points';

export default {
    state: {
        shopInfo: {},
        salableProducts: [
            {
                id: 1,
                barcode: '12345678910',
                title: 'Test fgfdfItem 1',
                category: 'Test Category',
                unit: 'kgs',
                price: 6500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 2,
                barcode: '46345678910',
                title: 'Tesertfdft Item 2',
                category: 'Test Category',
                unit: 'kgs',
                price: 1000,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 543,
                barcode: '18345678910',
                title: 'Test rtredfItem 3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 83,
                barcode: '938345678910',
                title: 'Test rewerfItem 3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 13,
                barcode: '898345678910',
                title: 'Test erewrfItem 3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 63,
                barcode: '65345678910',
                title: 'Test sssssItem 3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 53,
                barcode: '903456789104',
                title: 'Test Itemssssss 3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 33,
                barcode: '45345678910',
                title: 'Test Item cccc3',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            },
            {
                id: 31,
                barcode: '90345678910',
                title: 'Test Itemffff',
                category: 'Test Category',
                unit: 'kgs',
                price: 2500,
                discount: 0,
                stockQty: 96,
                margin: 15,
                avatar: '',
                type: 'sell',
            }
        ],
        purchasableProducts: [],
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
        GET_SALABLE_PRODUCTS: async ({commit}) => {
            try {
                let response = await axios.get(endPoints.SALABLE_PRODUCTS);
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
    }
}
