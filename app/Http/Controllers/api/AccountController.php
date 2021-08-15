<?php

namespace App\Http\Controllers\api;

use App\Account;
use App\History;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function accountMutation(Request $request)
    {
        // $account = Account::where('account_number', (int)$request->account_number)->first();
        $transaction_log = History::join('users', 'histories.account_number', '=', 'users.account_number')
                                    ->where(function($query) use ($request) {
                                        $query->where('histories.account_number', (int)$request->account_number)
                                        ->orWhere('histories.destination_account', (int)$request->account_number);
                                    })
                                    ->where(function($query) use ($request) {
                                        if ($request->end_date != null) {
                                            $query->whereDate('histories.inserted_at','>=',Carbon::parse($request->start_date)->format('Y-m-d'))
                                            ->whereDate('histories.inserted_at','<=',Carbon::parse($request->end_date)->format('Y-m-d'));
                                        } else {
                                            $query->whereDate('histories.inserted_at','=',Carbon::parse($request->start_date)->format('Y-m-d'));
                                        }

                                    })
                                    ->orderBy('histories.inserted_at', 'desc')
                                    ->get();

        return response()->json([
            'success' => true,
            'transaction_log' => $transaction_log
        ]);
    }
}
