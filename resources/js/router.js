import Vue from 'vue';
import VueRouter from 'vue-router';
// Shop
import ShopComponent from "./components/shop/Shop";
import ShopDashboard from "./components/shop/Dashboard";
import ShopIndex from "./components/shop/Index";
import ShopReports from "./components/shop/Reports";
import ShopChangePassword from "./components/shop/ChangePassword";
// Stores
import InventoryComponent from "./components/inventory/Inventory";
import Stocks from "./components/inventory/Stocks";
import Purchase from "./components/inventory/Purchase";
// Admin
import AdminComponent from "./components/admin/Admin";
import AdminDashboard from "./components/admin/Dashboard";
import CategoriesComponent from "./components/admin/items/Categories";
import UnitsComponent from "./components/admin/items/Units";
import ItemsComponent from "./components/admin/items/Items";
import UsersComponent from "./components/admin/users/Users";
import RolesComponent from "./components/admin/users/Roles";
import SettingsComponent from "./components/admin/settings/Settings";
import ReportsComponent from "./components/admin/reports/Reports";
import SalesReportComponent from "./components/admin/reports/Sales";
import BranchesComponent from "./components/admin/branches/Branches";


Vue.use(VueRouter);

const routes = [
    {
        path: '/shop',
        component: ShopComponent,
        children: [
            {
                path: '',
                redirect: 'dashboard',
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: ShopDashboard,
            },
            {
                path: 'pos',
                name: 'pos',
                component: ShopIndex,
            },
            {
                path: 'reports',
                name: 'reports',
                component: ShopReports,
            }, {
                path: 'change-password',
                name: 'change-password',
                component: ShopChangePassword,
            },
        ]
    },
    {
        path: '/stores',
        component: InventoryComponent,
        children: [
            {
                path: '',
                redirect: 'stocks',
            },
            {
                path: 'stocks',
                name: 'stocks',
                component: Stocks,
            },
            {
                path: 'purchase',
                name: 'purchase',
                component: Purchase,
            },
        ]
    },
    {
        path: '/admin',
        component: AdminComponent,
        children: [
            {path: '', name: 'admin', component: AdminDashboard},
            {path: 'dashboard', name: 'admin-dashboard', component: AdminDashboard},
            {path: 'units', name: 'units', component: UnitsComponent},
            {path: 'items', name: 'items', component: ItemsComponent},
            {path: 'categories', name: 'categories', component: CategoriesComponent},
            {path: 'users', name: 'users', component: UsersComponent},
            {path: 'branches', name: 'branches', component: BranchesComponent},
            {path: 'roles', name: 'roles', component: RolesComponent},
            {path: 'settings', name: 'settings', component: SettingsComponent},
            {
                path: 'reports',
                component: ReportsComponent,
                children: [
                    {path: 'sales', name: 'sales-report', component: SalesReportComponent},
                ]
            },
        ]
    },
];

export default new VueRouter({
    mode: 'history',
    routes: routes,
});
