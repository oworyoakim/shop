import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import Dashboard from '../views/Dashboard';
import Categories from '../views/Categories';
import Items from '../views/Items';
import Shops from '../views/Shops';
import Stocks from '../views/Stocks';
import Purchases from '../views/Purchases';
import Sales from '../views/Sales';
import Users from '../views/Users';
import Suppliers from '../views/Suppliers';
import Customers from '../views/Customers';
import Profile from '../views/Profile';
import Settings from '../views/Settings';

const routes = [
    {
        path: '',
        redirect: '/Dashboard',
    },
    {
        path: '/Dashboard',
        name: 'Dashboard',
        component: Dashboard
    },
    {
        path: '/Categories',
        name: 'Categories',
        component: Categories
    },
    {
        path: '/Items',
        name: 'Items',
        component: Items
    },
    {
        path: '/Shops',
        name: 'Shops',
        component: Shops
    },
    {
        path: '/Stocks',
        name: 'Stocks',
        component: Stocks
    },
    {
        path: '/Purchases',
        name: 'Purchases',
        component: Purchases
    },
    {
        path: '/Sales',
        name: 'Sales',
        component: Sales
    },
    {
        path: '/Users',
        name: 'Users',
        component: Users
    },
    {
        path: '/Settings',
        name: 'Settings',
        component: Settings
    },

    {
        path: '/Suppliers',
        name: 'Suppliers',
        component: Suppliers
    },
    {
        path: '/Customers',
        name: 'Customers',
        component: Customers
    },
    {
        path: '/Profile',
        name: 'Profile',
        component: Profile
    },

];


export default new VueRouter({
    mode: "history",
    routes,
});
