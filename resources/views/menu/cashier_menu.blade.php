@if(Sentinel::hasAccess('cashier.dashboard'))
<ul class="sidebar-menu">
    <li id="dashboard" class="@if(Request::is('cashier/dashboard')) active @endif">
        <a href="{{url('cashier/dashboard')}}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
    </li>
</ul>
@endif

@if(Sentinel::hasAnyAccess(['sales','sales.view']))
    <ul class="sidebar-menu">
        <li id="sales" class="@if(Request::is('cashier/sales')) active @endif">
            <a href="{{url('cashier/sales')}}">
                <i class="fa fa-money"></i> <span>Sales</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
        </li>
    </ul>
@endif

@if(Sentinel::hasAccess('sales.create'))
    <ul class="sidebar-menu">
        <li id="sales" class="@if(Request::is('cashier/sales/create')) active @endif">
            <a href="{{url('cashier/sales/create')}}">
                <i class="fa fa-shopping-cart"></i> <span>New Sale</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
        </li>
    </ul>
@endif

@if(Sentinel::hasAnyAccess(['expenses','expenses.view']))
    <ul class="sidebar-menu">
        <li id="expenses" class="@if(Request::is('cashier/expenses')) active @endif">
            <a href="{{url('cashier/expenses')}}">
                <i class="fa fa-bullhorn"></i> <span>Expenses</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
        </li>
    </ul>
@endif
