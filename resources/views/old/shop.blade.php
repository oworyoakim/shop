<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="keywords"
          content="Point of Sale Landlord, Inventory, Stock Management, Items Barcode Generator, Businesses, Multi-Outlets, User Management, Suppliers and Customers Management"/>
    <meta name="description" content="Cutting Edge solution for perfect PoS Businesses and Outlets."/>
    <meta name="author" content="Owor Yoakim"/>
<!-- App Styles -->
    <link rel="stylesheet" href="/css/app.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title>{{settings()->get('company_name')}} - @yield('title')</title>
</head>
<body class="hold-transition skin-purple fixed layout-top-nav">
<!-- Site wrapper -->
<div class="wrapper" id="main-app">
    <header class="main-header">
        <app-shop-navbar
            avatar="{{$user->avatar}}"
            username="{{$user->username}}"
            email="{{$user->email}}"
        />
    </header>

    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <app-shop-container/>
        </section>
    </div>
    <footer class="main-footer">
        <app-footer/>
    </footer>
</div>
<!-- ./wrapper -->
<script src="/js/app.js"></script>
<script>
    @if(session('success'))
    toastr.success('{{session('success')}}');
    @endif
    @if(session('info'))
    toastr.info('{{session('info')}}');
    @endif
    @if(session('warning'))
    toastr.warning('{{session('warning')}}');
    @endif
    @if(session('error'))
    toastr.error('{{session('error')}}');
    @endif
</script>
</body>
</html>
