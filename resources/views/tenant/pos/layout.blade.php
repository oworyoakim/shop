<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>{{$name ?? $companyName ?? ''}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{mix("css/app.css")}}" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini sidebar-collapse">
<div id="pos-app">
    <pos-app company-name="{{$name ?? $companyName ?? ''}}"></pos-app>
</div>
<script src="{{mix('js/app.js')}}"></script>
</body>
</html>
