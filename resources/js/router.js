import Vue from 'vue';
import VueRouter from 'vue-router';
import ShopDashboard from "./components/shop/Dashboard";
import ShopIndex from "./components/shop/Index";
import AdminComponent from "./components/admin/Admin";
import AdminDashboard from "./components/admin/Dashboard";
import CategoriesComponent from "./components/admin/items/Categories";
import UnitsComponent from "./components/admin/items/Units";
import ItemsComponent from "./components/admin/items/Items";
import StocksComponent from "./components/admin/items/Stocks";
import UsersComponent from "./components/admin/users/Users";
import RolesComponent from "./components/admin/users/Roles";
import SettingsComponent from "./components/admin/settings/Settings";
import ReportsComponent from "./components/admin/reports/Reports";
import SalesReportComponent from "./components/admin/reports/Sales";
import BranchesComponent from "./components/admin/branches/Branches";

Vue.use(VueRouter);

const routes = [
    {
        path: '',
        name: 'dashboard',
        component: ShopDashboard,
    },
    {
        path: '/shop',
        name: 'shop',
        component: ShopIndex,
    },
    {
        path: '/admin',
        component: AdminComponent,
        children: [
            {path: '', name: 'admin', component: ShopIndex},
            {path: 'dashboard', name: 'admin_dashboard', component: ShopIndex},
            {path: 'units', name: 'units', component: UnitsComponent},
            {path: 'items', name: 'items', component: ItemsComponent},
            {path: 'categories', name: 'categories', component: CategoriesComponent},
            {path: 'stocks', name: 'stocks', component: StocksComponent},
            {path: 'users', name: 'users', component: UsersComponent},
            {path: 'branches', name: 'branches', component: BranchesComponent},
            {path: 'roles', name: 'roles', component: RolesComponent},
            {path: 'settings', name: 'settings', component: SettingsComponent},
            {
                path: 'reports',
                component: ReportsComponent,
                children: [
                    {path: 'sales', name: 'sales_report', component: SalesReportComponent},
                ]
            },
        ]
    },
];

export default new VueRouter({
    mode: 'history',
    routes: routes,
});
