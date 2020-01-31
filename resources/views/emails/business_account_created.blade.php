@extends('email')
@section('title')
    {{$subject}}
@endsection
@section('content')
    <style type="text/css">

    </style>
    <div class="jumbotron">
        <h1>Hello, {{$business->name}}</h1>
        <h3>Thank you for creating your business account with our sales and inventory management system.</h3>
        <h3>
            <span>E-mail: {{$business->email}}</span><br/>
            <span>Current password: {{$business->password}}</span>
        </h3>
        <h3>Please login using the above credentials and change your password before continuing.</h3>
        <h3>Warm Regards</h3>
    </div>
@endsection

