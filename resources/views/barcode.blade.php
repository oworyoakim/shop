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
    <title>{{$companyName}}</title>
</head>
<body onload="printBarcode()">
<!-- Site wrapper -->
<div class="container-fluid" id="main-app">
    @foreach($chunks as $chunk)
        <div class="row">
            @foreach($chunk as $i)
                <div class="col-3 dot-outline">
                    <div class="box-body text-center font-size-20">
                        <img src="data:image/png;base64,{{$barcodeImg}}" alt="barcode"
                             class="img-responsive h-40"/><br/>
                        <span><small>{{$barcode}}</small></span>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
<script src="/js/app.js"></script>
<script>
    function printBarcode() {
        //window.document.close();
        window.focus();
        window.print();
        window.location = 'about:blank';
        window.close();
    }
</script>
</body>
</html>
