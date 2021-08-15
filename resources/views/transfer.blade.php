@extends('layouts.app')

@section('content')
@if($errors->any())
    @if (strpos($errors->first(),'Thankyou'))
        <h4 class="bg-success text-white text-center p-2">{{$errors->first()}}</h4>
    @else
        <h4 class="bg-warning text-white text-center p-2">{{$errors->first()}}</h4>
    @endif
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Transfer</div>
                <div class="card-body">
                    <div class="row border">
                        <div class="col-md-12">
                            <div class="row mt-2">
                                <div class="col-md-12 text-center mb-2">
                                    <form action="{{ route('check-transfer') }}" method="post">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Destination Account</span>
                                            <input type="number" name="destination_account" class="form-control" placeholder="...." aria-label="Username" aria-describedby="basic-addon1">
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">IDR</span>
                                            <input type="number" name="amount" class="form-control" placeholder="...." aria-label="Username" aria-describedby="basic-addon1">
                                        </div>
                                        <button type="submit" class="btn btn-success">Submit</button>
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
