<?php

namespace App\Http\Controllers;

use App\Account;
use App\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $account = Account::where('account_number', Auth::user()->account_number)->first();
        $transaction_log = History::join('users', 'histories.account_number', '=', 'users.account_number')
                                    ->where('histories.account_number', Auth::user()->account_number)
                                    ->orWhere('histories.destination_account', Auth::user()->account_number)
                                    ->take(10)
                                    ->orderBy('histories.inserted_at', 'desc')
                                    ->get();

        return view('balance-inquiry', compact('account','transaction_log'));
    }

    public function accountMutation()
    {
        $account_number = Auth::user()->account_number;
        return view('account-mutation', compact('account_number'));
    }
}
