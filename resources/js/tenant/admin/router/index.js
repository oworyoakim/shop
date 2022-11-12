import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import Dashboard from '../views/Dashboard/Index';
import Shops from '../views/Shops/Index';
import Sales from '../views/Sales/Index';
import Users from '../views/Users/Index';
import Customers from '../views/Customers/Index';
import Profile from '../views/Profile/Index';
import Settings from '../views/Settings/Index';

// Inventory Routes
import Inventory from '../views/Inventory/Index';
import Purchases from '../views/Inventory/Purchases';
import Items from "../views/Inventory/Items";
import Categories from "../views/Inventory/Categories";
import Suppliers from "../views/Inventory/Suppliers";
import Stocks from "../views/Inventory/Stocks";
import AccountsPayable from "../views/Inventory/AccountsPayable";

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
        path: '/Shops',
        name: 'Shops',
        component: Shops
    },
    {
        path: '/Inventory',
        name: 'Inventory',
        component: Inventory,
        children: [
            {
                path: 'Items',
                name: 'Items',
                component: Items
            },
            {
                path: 'Categories',
                name: 'Categories',
                component: Categories
            },
            {
                path: 'Suppliers',
                name: 'Suppliers',
                component: Suppliers
            },
            {
                path: 'Purchases',
                name: 'Purchases',
                component: Purchases
            },
            {
                path: 'Stocks',
                name: 'Stocks',
                component: Stocks
            },
            {
                path: 'AccountsPayable',
                name: 'AccountsPayable',
                component: AccountsPayable
            },
        ],
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
