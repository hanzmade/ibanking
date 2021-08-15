@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transaction Statistic</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <figure class="highcharts-figure">
                        <div id="container"></div>
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="card">
                        <div class="card-header">Daily Transaction</div>
                        <div class="card-body">
                            <figure class="highcharts-figure">
                                <div id="piechart-daily"></div>
                                <div class="row">
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-up text-success"> Debit</span></div>
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-down text-danger"> Credit</span></div>
                                </div>
                            </figure>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="card">
                        <div class="card-header">Monthly Transaction</div>
                        <div class="card-body">
                            <figure class="highcharts-figure">
                                <div id="piechart-monthly"></div>
                                <div class="row">
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-up text-success"> Debit</span></div>
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-down text-danger"> Credit</span></div>
                                </div>
                            </figure>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Annual Transaction</div>
                        <div class="card-body">
                            <figure class="highcharts-figure">
                                <div id="piechart-annual"></div>
                                <div class="row">
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-up text-success"> Debit</span></div>
                                    <div class="col-md-6 text-center"><span class="fa fa-arrow-down text-danger"> Credit</span></div>
                                </div>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input id="account_number" type="hidden" value="{{Auth::user()->account_number}}">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{url('/js/jquery-3.3.1.min.js')}}"></script>
<script type="text/javascript">
    const arr_months = [
        'January', 'February', 'March',
        'April', 'May', 'June',
        'July', 'August', 'September',
        'October', 'November', 'December'
    ]
    let date = new Date();
    let this_month = arr_months[date.getMonth()];
    let this_year = date.getFullYear();

    let data_barchart = [];

    const account_number = document.getElementById('account_number').value;

    $.ajax({
        url: `/api/get-data-dashboard?account_number=${account_number}`,
        method: 'GET',
    })
    .then((res) => {
        if (res.success) {
            bar_chart(res.datas.barchart);
            piechart_daily(res.datas.piechart_daily);
            piechart_monthly(res.datas.piechart_monthly);
            piechart_annual(res.datas.piechart_annual);
        }
    })
    .catch((err) => {
        console.log(err);
    })

    // Start BarChart
    const bar_chart = (datas = []) => {
        datas.map((val, key) => {
            let temp_data = [];
            temp_data.push(arr_months[key]);
            temp_data.push(parseInt(val.data));
            data_barchart.push(temp_data);
        });

        Highcharts.chart('container', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Transaction This Year'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Balance (IDR)'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: `Balances in ${this_month} ${this_year}: <b>IDR {point.y:.1f}</b>`
            },
            series: [{
                name: 'Months',
                data: data_barchart,
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    format: '{point.y:.1f}', // one decimal
                    y: 10, // 10 pixels down from the top
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
            }]
        });
    }
    // End BarChart

    // Start PieChart Daily
    const piechart_daily = (datas) => {
        console.log('daily : ', datas.debit[0].debit);
        Highcharts.chart('piechart-daily', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Debit & Credit'
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Debit',
                    y: parseInt(datas.debit[0].debit),
                    color: 'green'
                }, {
                    name: 'Credit',
                    y: parseInt(datas.credit[0].credit),
                    color: 'red'
                }]
            }]
        });
    }
    // End PieChart Daily

    // Start PieChart Monthly
    const piechart_monthly = (datas = []) => {
        Highcharts.chart('piechart-monthly', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Debit & Credit'
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Debit',
                    y: parseInt(datas.debit[0].debit),
                    color: 'green'
                }, {
                    name: 'Credit',
                    y: parseInt(datas.credit[0].credit),
                    color: 'red'
                }]
            }]
        });
    }
    // End PieChart Monthly

    // Start PieChart Annual
    const piechart_annual = (datas = []) => {
        console.log('annual : ', datas);
        Highcharts.chart('piechart-annual', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Debit & Credit'
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Debit',
                    y: parseInt(datas.debit[0].debit),
                    color: 'green'
                }, {
                    name: 'Credit',
                    y: parseInt(datas.credit[0].credit),
                    color: 'red'
                }]
            }]
        });
    }
    // End PieChart Annual

    bar_chart();
    piechart_daily();
    piechart_monthly();
    piechart_annual();
</script>
@endsection
