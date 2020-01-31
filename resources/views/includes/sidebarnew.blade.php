<!-- sidebar -->
<section class="sidebar">
    <!-- sidebar menu -->
    <ul class="sidebar-menu" data-widget="tree">
        <li class="user-profile treeview">
            <a href="#">
                @if(Sentinel::check())
                    <img src="{{Sentinel::getUser()->avatar}}" alt="user">
                    <span>{{Sentinel::getUser()->first_name}}</span>
                    <span class="small pull-right"><i class="fa fa-circle text-success"></i> Online</span>
                @else
                    <img src="/images/avatar.png" alt="user">
                    <span>Admin Panel</span>
                @endif
            </a>
        </li>

        @if(Sentinel::check() && Sentinel::inRole('admin'))
            @include('menu.admin_menu')
        @endif

        @if(Sentinel::check() && (Sentinel::inRole('admin') || Sentinel::inRole('manager')))
            @include('menu.manager_menu')
        @endif

        @if(Sentinel::check() && Sentinel::inRole('cashier'))
            @include('menu.cashier_menu')
        @endif
    </ul>
</section>
