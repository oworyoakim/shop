import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);


import Dashboard from "../views/Dashboard/Index";
import Tenants from "../views/Tenants/Index";
import Units from "../views/Units/Index";
import Users from "../views/Users/Index";
import Profile from "../views/Profile/Index";
import Settings from "../views/Settings/Index";



const routes = [
    {
        path: "/",
        redirect: {name: "Dashboard"},
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: Dashboard
    },
    {
        path: "/tenants",
        name: "Tenants",
        component: Tenants
    },
    {
        path: "/users",
        name: "Users",
        component: Users
    },
    {
        path: "/units",
        name: "Units",
        component: Units
    },
    {
        path: "/settings",
        name: "Settings",
        component: Settings
    },
    {
        path: "/profile",
        name: "Profile",
        component: Profile
    },
];


export default new VueRouter({
    mode: "history",
    routes,
});
