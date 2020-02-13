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
          content="Point of Sale System, Inventory, Stock Management, Items Barcode Generator, Businesses, Multi-Outlets, User Management, Suppliers and Customers Management"/>
    <meta name="description" content="Cutting Edge solution for perfect PoS Businesses and Outlets."/>
    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- App Styles -->
    <link rel="stylesheet" href="/css/app.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title>{{settings()->get('company_name')}} - Authorization</title>
</head>
<body class="hold-transition login-page" style="background-color: #ffffff;">
<div class="login-box" id="main-app">
{{--    <div class="login-logo">--}}
{{--        <h1 class="text-small">--}}
{{--            <a href="#" style="color: #000000;" class="btn-link">--}}
{{--                <b><span>{{ settings()->get('company_name') }}</span></b>--}}
{{--            </a>--}}
{{--        </h1>--}}
{{--    </div>--}}
    <!-- /.login-logo -->
    <app-login
        return-url="{{request()->session()->get('url.intended')}}"
        message="{{session()->get('error')}}">
    </app-login>
</div>
<!-- /.login-box -->
<script src="/js/app.js"></script>
</body>
</html>
