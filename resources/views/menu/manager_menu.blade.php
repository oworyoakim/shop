@if(Sentinel::hasAccess('manager.dashboard'))
    <ul class="sidebar-menu">
        <li id="dashboard" class="@if(Request::is('manager/dashboard')) active @endif">
            <a href="{{url('manager/dashboard')}}">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['branches','branches.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/branches*')) active @endif">
            <a href="#">
                <i class="fa fa-home"></i> <span>{{trans_choice('general.branch',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('branches.view'))
                    <li><a href="{{ url('manager/branches') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',2)}} {{trans_choice('general.branch',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('branches.create'))
                    <li><a href="{{ url('manager/branches/create') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.add',1)}} {{trans_choice('general.branch',1)}}</span>
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['categories','categories.view']))
    <ul class="sidebar-menu">
        <li id="categories" class="@if(Request::is('manager/categories*')) active @endif">
            <a href="{{ url('manager/categories') }}">
                <i class="fa fa-tree"></i> <span>{{trans_choice('general.category',2)}}</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i></span>
            </a>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['units','units.view']))
    <ul class="sidebar-menu">
        <li id="units" class="@if(Request::is('manager/units*')) active @endif">
            <a href="{{ url('manager/units') }}"><i
                        class="fa fa-balance-scale"></i> <span>{{trans_choice('general.unit',2)}}</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i></span>
            </a>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['items','items.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/items*')) active @endif">
            <a href="#">
                <i class="fa fa-item-hunt"></i> <span>{{trans_choice('general.item',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('items.view'))
                    <li><a href="{{ url('manager/items') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.item',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('items.create'))
                    <li><a href="{{ url('manager/items/create') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.item',1)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAccess('stocks'))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/stocks*')) active @endif">
            <a href="#">
                <i class="fa fa-users"></i> <span>{{trans_choice('general.stock',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('stocks'))
                    <li><a href="{{ url('manager/stocks') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.manage',1)}} {{trans_choice('general.stock',1)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('stocks.transfer'))
                    <li><a href="{{ url('manager/stocks/transfer') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.transfer',1)}} {{trans_choice('general.stock',2)}}</span>
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['expenses','expenses.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/expenses*')) active @endif">
            <a href="#">
                <i class="fa fa-building-o"></i> <span>{{trans_choice('general.expense',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('expenses.view'))
                    <li><a href="{{ url('manager/expenses') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',2)}} {{trans_choice('general.expense',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('expenses.create'))
                    <li><a href="{{ url('manager/expenses/create') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('expenses.types'))
                    <li><a href="{{ url('manager/expenses/types') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.manage',2)}} {{trans_choice('general.type',2)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif


@if(Sentinel::hasAnyAccess(['sales','sales.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/sales*')) active @endif">
            <a href="#">
                <i class="fa fa-money"></i> <span>{{trans_choice('general.sale',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('sales.view'))
                    <li><a href="{{ url('manager/sales') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.sale',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('sales.create'))
                    <li><a href="{{ url('manager/sales/create') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.sale',1)}}
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('sales.receivables'))
                    <li><a href="{{ url('manager/sales/receivables') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.receivable',2)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif


@if(Sentinel::hasAnyAccess(['purchases','purchases.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/purchases*')) active @endif">
            <a href="#">
                <i class="fa fa-money"></i> <span>{{trans_choice('general.purchase',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('purchases.view'))
                    <li><a href="{{ url('manager/purchases') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.purchase',2)}}</span>
                        </a></li>
                @endif
                @if(Sentinel::hasAccess('purchases'))
                    <li><a href="{{ url('manager/purchases/orders') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.purchase',1)}} {{trans_choice('general.order',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('purchases.create'))
                    <li><a href="{{ url('manager/purchases/create') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.purchase',1)}}
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('purchases.payable'))
                    <li><a href="{{ url('manager/purchases/payable') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.payable',2)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['employees','employees.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/employees*')) active @endif">
            <a href="#">
                <i class="fa fa-users"></i> <span>{{trans_choice('general.employee',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('employees.view'))
                    <li><a href="{{ url('manager/employees') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.employee',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('employees.create'))
                    <li><a href="{{ url('manager/employees/create') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.employee',1)}}
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('employees.roles'))
                    <li><a href="{{ url('manager/employees/roles') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.manage',1)}} {{trans_choice('general.role',2)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif


@if(Sentinel::hasAnyAccess(['customers','customers.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/customers*')) active @endif">
            <a href="#">
                <i class="fa fa-users"></i> <span>{{trans_choice('general.customer',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAnyAccess(['customers.view','customers.create']))
                    <li><a href="{{ url('manager/customers') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.customer',2)}}</span>
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['suppliers','suppliers.view']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/suppliers*')) active @endif">
            <a href="#">
                <i class="fa fa-truck"></i> <span>{{trans_choice('general.supplier',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAccess('suppliers.view'))
                    <li><a href="{{ url('manager/suppliers') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.all',1)}} {{trans_choice('general.supplier',2)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('suppliers.create'))
                    <li><a href="{{ url('manager/suppliers/create') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.supplier',1)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['reports','reports.overall']))
    <ul class="sidebar-menu">
        <li class="treeview @if(Request::is('manager/reports*')) active @endif">
            <a href="#">
                <i class="fa fa-bar-chart"></i> <span>{{trans_choice('general.report',2)}}</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                @if(Sentinel::hasAnyAccess(['reports','reports.overall']))
                    <li><a href="{{ url('manager/reports') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>{{trans_choice('general.overall',1)}}</span>
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('reports.sales'))
                    <li><a href="{{ url('manager/reports/sales') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.sale',2)}} {{trans_choice('general.report',2)}}
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('reports.purchases'))
                    <li><a href="{{ url('manager/reports/purchases') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.purchase',2)}} {{trans_choice('general.report',2)}}
                        </a></li>
                @endif

                @if(Sentinel::hasAccess('reports.expenses'))
                    <li><a href="{{ url('manager/reports/expenses') }}"><i
                                    class="fa fa-circle-o"></i>{{trans_choice('general.expense',2)}} {{trans_choice('general.report',2)}}
                        </a></li>
                @endif
            </ul>
        </li>
    </ul>
@endif

@if(Sentinel::hasAccess('settings'))
    <ul class="sidebar-menu">
        <li id="settings" class="@if(Request::is('manager/settings*')) active @endif">
            <a href="{{url('manager/settings')}}">
                <i class="fa fa-gear"></i> <span>Settings</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
        </li>
    </ul>
@endif


