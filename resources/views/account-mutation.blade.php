@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Account Mutation</div>
                <div class="card-body">
                    <div id="account_mutation" class="row">
                        <div class="col-md-4 text-center">
                            <label for="start_date">Start Date :</label>
                            <input type="date" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-4 text-center">
                            <label for="end_date">End Date :</label>
                            <input type="date" id="end_date" name="end_date">
                        </div>
                        <input id="account_number_id" type="hidden" value="{{$account_number}}">
                        <div class="col-md-4 text-start">
                            <button type="button" onclick="getAccountMutation()" class="btn btn-primary">Submit</button>
                        </div>
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="table_content"></tbody>
                            </table>
                        </div>
                        {{-- @foreach ($transaction_log as $key => $value)
                            <div class="col-md-6">
                                <span>{{ \Carbon\Carbon::parse($value->created_at)->format('D, d M Y') }}</span>
                                <p>{{ (($value->account_number != Auth::user()->account_number)? 'From : '. $value->name : (($value->destination_account != Auth::user()->account_number) ? 'To : '.\App\User::where('account_number', $value->destination_account)->pluck('name')[0] : 'Cash Withdrawal')) }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="{{(($value->account_number == Auth::user()->account_number)? 'fa fa-arrow-down text-danger' : 'fa fa-arrow-up text-success')}}">IDR {{ number_format($value->amount) }}</span>
                            </div>
                            <hr>
                        @endforeach --}}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
<script src="{{url('/js/jquery-3.3.1.min.js')}}"></script>
<script type="text/javascript">
    function getAccountMutation(){
        let arr_month = [
            'January', 'February', 'March',
            'April', 'May', 'June',
            'July', 'August', 'September',
            'October', 'November', 'December'
        ];

        start_date = document.getElementById('start_date').value;
        end_date = document.getElementById('end_date').value;
        account_number = document.getElementById('account_number_id').value;
        $.ajax({
            url: `/api/account-mutation?account_number=${account_number}&start_date=${start_date}&end_date=${end_date}`,
            method: 'GET',
        })
        .then((res) => {
            if (res.success) {
                // moment(val.inserted_at).format('DD M YYYY hh:mm:ss')
                console.log(res.transaction_log);
                document.getElementById('table_content').innerHTML = '';
                res.transaction_log.map((val,key) => {
                    document.getElementById('table_content').innerHTML += `
                        <tr>
                            <td>${moment(val.inserted_at).format('DD')} ${arr_month[moment(val.inserted_at).format('M')-1]} ${moment(val.inserted_at).format('YYYY')}</td>
                            <td>${val.account_number == account_number ? val.destination_account == account_number ? 'Withdrawn by Me' : `To : ${val.destination_account}` : `From : ${val.account_number}`}</td>
                            <td>${val.account_number == account_number ? val.destination_account == account_number ? '<span class="fa fa-arrow-down text-danger"> Withdraw</span>' : '<span class="fa fa-arrow-down text-danger"> Credit</span>' : '<span class="fa fa-arrow-up text-success"> Debit</span>'}</td>
                            <td>IDR ${new Number(val.amount).toLocaleString("en-EN")}</td>
                        </tr>
                    `
                });
            }
        })
        .catch((err) => {
            console.log(err);
        })
    }
</script>

@endsection
