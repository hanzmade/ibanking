<?php

namespace App\Http\Controllers;

use App\Account;
use App\History;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class TransactionController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function cashWithdrawal()
    {
        return view('cash-withdrawal');
    }

    public function withdraw(Request $request)
    {
        $account = Account::where('account_number', Auth::user()->account_number)->first();

        $limit = Role::where('id_role', Auth::user()->id_role)->pluck('limit')[0];

        $daily_limit = History::whereDate('inserted_at', '>=', Carbon::today()->format('Y-m-d'))
                                ->where('account_number', Auth::user()->account_number)
                                ->sum('amount');

        $after_amount = (int)$daily_limit + (int)$request->amount;

        if($after_amount <= (int)$limit){
            if((int)$request->amount <= (int)$limit){
                if ($account->balance > $request->amount) {
                    $amount = $request->amount;
                    return view('withdrawal-confirmation', compact('amount'));
                }else {
                    return redirect()->back()->withErrors(['Sorry, your balance is not enough to make a transaction']);
                }
            }else{
                return redirect()->back()->withErrors(['Sorry, you exceed the transaction limit']);
            }
        }else {
            return redirect()->back()->withErrors(['Sorry, you reach the daily transaction limit']);
        }
    }

    public function execWithdraw(Request $request)
    {
        $account = Account::where('account_number', (int)Auth::user()->account_number)->first();

        $pin = User::where('account_number', (int)Auth::user()->account_number)->pluck('pin')[0];
        if ($pin == $request->pin) {
            if ($account->balance > $request->amount) {
                DB::beginTransaction();
                try {
                    $updated_balance = (int)$account->balance - (int)$request->amount;
                    $exec_update_balance = Account::where('account_number',(int)Auth::user()->account_number)
                                                ->update([
                                                    'balance' => $updated_balance
                                                ]);
                    if ($exec_update_balance) {
                        $history = History::insert([
                            'account_number' => Auth::user()->account_number,
                            'destination_account' => Auth::user()->account_number,
                            'amount' => $request->amount,
                            'status' => 2,
                        ]);

                        DB::commit();

                        return redirect('/cash-withdrawal')->withErrors(['You have successfully withdrawn cash, Thankyou']);
                    }else{
                        DB::rollback();
                    }

                } catch (\Throwable $th) {
                    DB::rollback();
                }
            }else {
                return redirect('/cash-withdrawal')->withErrors(['Sorry, your balance is not enough to make a transaction']);
            }
        } else {
            return redirect('/cash-withdrawal')->withErrors(['Sorry, your pin is wrong. Please Try Again.']);
        }

    }

    public function transfer()
    {
        return view('transfer');
    }

    public function checkTransfer(Request $request)
    {
        $account = Account::where('account_number', Auth::user()->account_number)->first();

        $limit = Role::where('id_role', Auth::user()->id_role)->pluck('limit')[0];

        $daily_limit = History::whereDate('inserted_at', '>=', Carbon::today()->format('Y-m-d'))
                                ->where('account_number', Auth::user()->account_number)
                                ->sum('amount');

        $after_amount = (int)$daily_limit + (int)$request->amount;

        if($after_amount <= (int)$limit){
            if((int)$request->amount <= (int)$limit){

                if ($account->balance > $request->amount) {
                    $amount = $request->amount;
                    $destination_account = $request->destination_account;
                    return view('transfer-confirmation', compact('amount','destination_account'));
                }else {
                    return redirect()->back()->withErrors(['Sorry, your balance is not enough to make a transaction']);
                }
            }else{
                return redirect()->back()->withErrors(['Sorry, you exceed the transaction limit']);
            }
        }else {
            return redirect()->back()->withErrors(['Sorry, you reach the daily transaction limit']);
        }
    }

    public function execTransfer(Request $request)
    {
        $account = Account::where('account_number', (int)Auth::user()->account_number)->first();
        $target_account = Account::where('account_number', (int)$request->destination_account)->first();

        $pin = User::where('account_number', (int)Auth::user()->account_number)->pluck('pin')[0];
        if ($pin == $request->pin) {
            if ($account->balance > $request->amount) {
                DB::beginTransaction();
                try {
                    $decr_balance = (int)$account->balance - (int)$request->amount;

                    $incr_balance = (int)$target_account->balance + (int)$request->amount;

                    $exec_update_target_balance = Account::where('account_number',(int)$request->destination_account)
                            ->update([
                                'balance' => $incr_balance
                            ]);

                    if ($exec_update_target_balance) {
                        $exec_update_balance = Account::where('account_number',(int)Auth::user()->account_number)
                                                    ->update([
                                                        'balance' => $decr_balance
                                                    ]);
                        if ($exec_update_balance) {
                            $history = History::insert([
                                'account_number' => Auth::user()->account_number,
                                'destination_account' => $request->destination_account,
                                'amount' => $request->amount,
                                'status' => 1,
                            ]);

                            DB::commit();

                            return redirect('/transfer')->withErrors(['You have successfully Transfer, Thankyou']);
                        }else{
                            DB::rollback();

                            return redirect('/transfer')->withErrors(['Failed to Transfer, Server Error.']);
                        }
                    } else {
                        DB::rollback();

                        return redirect('/transfer')->withErrors(['Failed to Transfer, Server Error.']);
                    }


                } catch (\Throwable $th) {
                    DB::rollback();
                }
            }else {
                return redirect('/transfer')->withErrors(['Sorry, your balance is not enough to make a transaction']);
            }
        } else {
            return redirect('/transfer')->withErrors(['Sorry, your pin is wrong. Please Try Again.']);
        }
    }
}
