<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>{{$name ?? $companyName ?? ''}} | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{mix("css/auth.css")}}" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box shadow-lg" id="auth-app">
    <auth-app company-name="{{$name ?? $companyName ?? ''}}"></auth-app>
</div>
<script src="{{mix('js/auth.js')}}"></script>
</body>
</html>

