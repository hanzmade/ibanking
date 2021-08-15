<?php

namespace App\Http\Controllers\api;

use App\History;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {

    }

    public function getDataDashboard(Request $request)
    {
        $barchart = DB::select(DB::raw('select sum(amount) as data FROM histories WHERE YEAR(inserted_at)='.Carbon::now()->format('Y').' GROUP BY MONTH(inserted_at)'));

        $piechart_daily_debit = DB::select(DB::raw(('select sum(amount) as debit FROM histories WHERE DATE(inserted_at) = curdate() AND destination_account='.(int)$request->account_number)));
        $piechart_daily_credit = DB::select(DB::raw(('select sum(amount) as credit FROM histories WHERE DATE(inserted_at) = curdate() AND account_number='.(int)$request->account_number)));

        $piechart_monthly_debit = DB::select(DB::raw(('select sum(amount) as debit FROM histories WHERE MONTH(inserted_at) = '.Carbon::today()->format('m').' AND destination_account='.(int)$request->account_number)));
        $piechart_monthly_credit = DB::select(DB::raw(('select sum(amount) as credit FROM histories WHERE MONTH(inserted_at) = '.Carbon::today()->format('m').' AND account_number='.(int)$request->account_number)));

        $piechart_annual_debit = DB::select(DB::raw(('select sum(amount) as debit FROM histories WHERE YEAR(inserted_at) = '.Carbon::today()->format('Y').' AND destination_account='.(int)$request->account_number)));
        $piechart_annual_credit = DB::select(DB::raw(('select sum(amount) as credit FROM histories WHERE YEAR(inserted_at) = '.Carbon::today()->format('Y').' AND account_number='.(int)$request->account_number)));


        $charts = [
            'barchart' => $barchart,
            'piechart_daily' => [
                'debit' => $piechart_daily_debit,
                'credit' => $piechart_daily_credit
            ],
            'piechart_monthly' => [
                'debit' => $piechart_monthly_debit,
                'credit' => $piechart_monthly_credit,
            ],
            'piechart_annual' => [
                'debit' => $piechart_annual_debit,
                'credit' => $piechart_annual_credit,
            ]
        ];

        return response()->json([
            'success' => true,
            'datas' => $charts
        ]);
    }
}
