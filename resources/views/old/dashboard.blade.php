@extends('layoutnew')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-4 col-md-4 col">
            <div class="box box-inverse bg-info">
                <div class="box-header">
                    <h5 class="">Today</h5>
                    <div class="box-tools pull-right">
                        <time class="text-white" datetime="{{date('Y-m-d H:i:s')}}">{{date('F d, Y')}}</time>
                    </div>
                </div>
                <div class="box-body">
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.sale',2)}}</span>
                        <span class="info-box-number">{{number_format($days_total_sales)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.receivable',2)}}</span>
                        <span class="info-box-number">{{number_format($days_total_receivables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.purchase',2)}}</span>
                        <span class="info-box-number">{{number_format($days_total_purchases)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.payable',2)}}</span>
                        <span class="info-box-number">{{number_format($days_total_payables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.expense',2)}}</span>
                        <span class="info-box-number">{{number_format($days_total_expenses)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.cash',1)}}</span>
                        <span class="info-box-number">{{number_format($days_total_cash)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col">
            <div class="box box-inverse bg-success">
                <div class="box-header">
                    <h5 class="">This Month</h5>
                    <div class="box-tools pull-right">
                        <time class="text-white" datetime="{{date('Y-m-d H:i:s')}}">{{date('F, Y')}}</time>
                    </div>
                </div>
                <div class="box-body">
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.sale',2)}}</span>
                        <span class="info-box-number">{{number_format($months_total_sales)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.receivable',2)}}</span>
                        <span class="info-box-number">{{number_format($months_total_receivables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.purchase',2)}}</span>
                        <span class="info-box-number">{{number_format($months_total_purchases)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.payable',2)}}</span>
                        <span class="info-box-number">{{number_format($months_total_payables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.expense',2)}}</span>
                        <span class="info-box-number">{{number_format($months_total_expenses)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.cash',1)}}</span>
                        <span class="info-box-number">{{number_format($months_total_cash)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col">
            <div class="box box-inverse bg-warning">
                <div class="box-header">
                    <h5 class="">All Time</h5>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.sale',2)}}</span>
                        <span class="info-box-number">{{number_format($overall_total_sales)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.receivable',2)}}</span>
                        <span class="info-box-number">{{number_format($overall_total_receivables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.purchase',2)}}</span>
                        <span class="info-box-number">{{number_format($overall_total_purchases)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.payable',2)}}</span>
                        <span class="info-box-number">{{number_format($overall_total_payables)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.expense',2)}}</span>
                        <span class="info-box-number">{{number_format($overall_total_expenses)}}</span>
                    </div>
                    <div class="flexbox">
                        <span class="info-box-text">{{trans_choice('general.net_worth',1)}}</span>
                        <span class="info-box-number">{{number_format($net_worth)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Best Selling Items (<b>Price</b>)</h3>
                    <div class="box-tools pull-right">
                        <span class="badge badge-purple">Top 5</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Title</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($top_selling_by_price as $row)
                            <tr>
                                <td>{{$row->barcode}}</td>
                                <td>{{$row->title}}</td>
                                <td>{{number_format($row->total)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Best Selling Items (<b>Qty</b>)</h3>
                    <div class="box-tools pull-right">
                        <span class="badge badge-purple">Top 5</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Title</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($top_selling_by_qty as $row)
                            <tr>
                                <td>{{$row->barcode}}</td>
                                <td>{{$row->title}}</td>
                                <td>{{number_format($row->total)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Recent Transactions</h3>
                    <div class="box-tools pull-right">
                        <span class="badge badge-purple">Latest 5</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="nav-tabs-custom" role="tablist">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab"
                                   href="#sales">{{ trans_choice('general.sale',2) }}</a></li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#purchases">{{ trans_choice('general.purchase',2) }}</a></li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                   href="#expenses">{{ trans_choice('general.expense',2) }}</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="sales" role="tabpanel">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($latest_sales as $row)
                                        <tr>
                                            <td>{{$row->created_at->diffForHumans()}}</td>
                                            <td>{{$row->transcode}}</td>
                                            <td>{{number_format($row->net_amount)}}</td>
                                            <td>
                                                @if($row->payment_status === 'settled')
                                                    <span class="label label-success">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'partial')
                                                    <span class="label label-info">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'pending')
                                                    <span class="label label-warning">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'canceled')
                                                    <span class="label label-danger">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane pad" id="purchases" role="tabpanel">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($latest_purchases as $row)
                                        <tr>
                                            <td>{{$row->created_at->diffForHumans()}}</td>
                                            <td>{{$row->transcode}}</td>
                                            <td>{{number_format($row->net_amount)}}</td>
                                            <td>
                                                @if($row->payment_status === 'settled')
                                                    <span class="label label-success">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'partial')
                                                    <span class="label label-info">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'pending')
                                                    <span class="label label-warning">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'canceled')
                                                    <span class="label label-danger">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane pad" id="expenses" role="tabpanel">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Expense</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($latest_expenses as $row)
                                        <tr>
                                            <td>{{$row->created_at->diffForHumans()}}</td>
                                            <td>{{$row->transcode}}</td>
                                            <td>{{$row->type->title}}</td>
                                            <td>
                                                @if($row->payment_status === 'settled')
                                                    <span class="label label-success">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'partial')
                                                    <span class="label label-info">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'pending')
                                                    <span class="label label-warning">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                                @if($row->payment_status === 'canceled')
                                                    <span class="label label-danger">{{ucfirst($row->payment_status)}}</span>
                                                @endif
                                            </td>
                                            <td>{{number_format($row->net_amount)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Best Selling Items (<b>{{date('F')}}</b>)</h3>
                    <div class="box-tools pull-right">
                        <span class="badge badge-purple">Top 5</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Title</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($top_selling_for_month as $row)
                            <tr>
                                <td>{{$row->barcode}}</td>
                                <td>{{$row->title}}</td>
                                <td>{{number_format($row->total)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>

    </script>
@endsection