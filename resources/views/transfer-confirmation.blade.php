@extends('layouts.app')

@section('content')
@if($errors->any())
    <h4 class="bg-warning text-white text-center p-2">{{$errors->first()}}</h4>
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Transfer Confirmation</div>
                <div class="card-body">
                    <div class="row border">
                        <div class="col-md-12">
                            <div class="row mt-2">
                                <div class="col-md-12 text-center mb-2">
                                    <form action="{{ route('exec-transfer') }}" method="post">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">IDR</span>
                                            <input type="number" name="pin" class="form-control" placeholder="Insert Your Pin" aria-label="Username" aria-describedby="basic-addon1">
                                            <input type="hidden" name="amount" class="form-control" value="{{$amount}}">
                                            <input type="hidden" name="destination_account" class="form-control" value="{{$destination_account}}">
                                        </div>
                                        <button type="submit" class="btn btn-success">Transfer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
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
