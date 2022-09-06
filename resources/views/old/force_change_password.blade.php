<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{strtoupper(settings()->get('company_name'))}} | Password Change</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('plugins/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('plugins/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('adminlte/css/AdminLTE.min.css')}}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
          folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('adminlte/css/skins/skin-blue.min.css')}}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-box-body">
        <p class="login-box-msg">Password Reset (Username: {{Sentinel::getUser()->username}})</p>
        @if($errors->count())
            <ul class="alert-danger alert-danger">
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
            </ul>
        @endif
        @if(session()->has('error'))
            <ul class="alert-danger alert-danger">
                <li>{{session()->get('error')}}</li>
            </ul>
        @endif
        <form action="{{url('force-password-change')}}" method="post">
            {{csrf_field()}}
            <div class="form-group has-feedback">
                <input type="password" name="old_password" class="form-control" placeholder="Old Password">
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="new_password" class="form-control" placeholder="New Password">
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="confirm_password" class="form-control" placeholder="Retype password">
                <span class="fa fa-sign-in form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <button type="submit" class="btn btn-primary btn-block">Change Password</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->

<!-- jQuery 3 -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script>

</script>
</body>
</html>
