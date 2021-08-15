@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Account Information</div>
                <div class="card-body">
                    <div class="row border">
                        <div class="col-md-12">
                            <div class="row ms-3 mt-2">
                                <div class="col-md-12">
                                    <span class="text-black">{{Auth::user()->name}}</span>

                                    <span class="badge bg-success rounded-pill p-2 float-end">IDR {{number_format($account->balance)}}</span>
                                </div>
                                <div class="col-md-12">
                                    <span class="text-black text-center" style="font-size: 80%">{{$account->account_number}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Transaction Log (Last 10 Logs)</div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($transaction_log as $key => $value)
                            <div class="col-md-6">
                                <span>{{ \Carbon\Carbon::parse($value->inserted_at)->format('D, d M Y') }}</span>
                                <p>{{ (($value->account_number != Auth::user()->account_number)? 'From : '. $value->name : (($value->destination_account != Auth::user()->account_number) ? 'To : '.\App\User::where('account_number', $value->destination_account)->pluck('name')[0] : 'Cash Withdrawal')) }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="{{(($value->account_number == Auth::user()->account_number)? 'fa fa-arrow-down text-danger' : 'fa fa-arrow-up text-success')}}">IDR {{ number_format($value->amount) }}</span>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{url('/js/jquery-3.3.1.min.js')}}"></script>
@endsection
