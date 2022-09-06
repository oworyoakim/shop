<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>{{$name ?? $companyName ?? ''}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{mix("css/manager.css")}}" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini sidebar-collapse">
<div id="manager-app">
    <manager-app company-name="{{$name ?? $companyName ?? ''}}"></manager-app>
</div>
<script src="{{mix('js/manager.js')}}"></script>
</body>
</html>
