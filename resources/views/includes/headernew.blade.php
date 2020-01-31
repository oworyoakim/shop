<style type="text/css">
    .user-links:hover{
        background-color: rgba(0,0,0,0.1);
    }
</style>
<!-- Logo -->
<a href="{{url('/')}}" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <b class="logo-mini">
        <i class="fa fa-shopping-cart"></i>
    </b>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg">
        <h3>{{settings()->get('company_name') }}</h3>
    </span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Menu</span>
    </a>

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
        <!-- User Account: style can be found in dropdown.less -->
            @if(Sentinel::check())
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{Sentinel::getUser()->avatar}}"
                             class="user-image rounded-circle"
                             alt="User Image">
                    </a>
                    <ul class="dropdown-menu scale-up">
                        <!-- User image -->
                        <li class="user-header bg-white">
                            <img src="{{Sentinel::getUser()->avatar}}"
                                 class="float-left rounded-circle"
                                 alt="User Image">
                            <p>
                                <span>{{Sentinel::getUser()->username}}</span>
                                <small class="mb-5">{{Sentinel::getUser()->email}}</small>
                                <a href="#" class="btn btn-danger btn-sm btn-rounded">View
                                    Profile</a>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row no-gutters">
                                @if(Sentinel::inRole('manager'))
                                    <div class="col-12 text-left">
                                        <a href="#" class="user-links" style="color: #455a64;"><i class="fa fa-dashboard"></i> Dashboard</a>
                                    </div>
                                @endif
                                @if(Sentinel::inRole('cashier'))
                                    <div class="col-12 text-left">
                                        <a href="#" class="user-links" style="color: #455a64;"><i class="fa fa-dashboard"></i> Dashboard</a>
                                    </div>
                                @endif
                                <div role="separator" class="divider col-12"></div>
                                <div class="col-12 text-left">
                                    <a href="#" class="user-links" style="color: #455a64;"><i class="fa fa-power-off"></i> Logout</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                    </ul>
                </li>
            @else
                <li><a href="{{url('login')}}">Login</a></li>
            @endif
        </ul>
    </div>
</nav>
