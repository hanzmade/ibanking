<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();


Route::get('/', 'DashboardController@index')->name('dashboard');

Route::get('/balance-inquiry', 'AccountController@index')->name('balance-inquiry');

Route::get('/cash-withdrawal', 'TransactionController@cashWithdrawal')->name('cash-withdrawal');
Route::post('/withdraw', 'TransactionController@withdraw')->name('withdraw');
Route::post('/exec-withdraw', 'TransactionController@execWithdraw')->name('exec-withdraw');

Route::get('/transfer', 'TransactionController@transfer')->name('transfer');
Route::post('/check-transfer', 'TransactionController@checkTransfer')->name('check-transfer');
Route::post('/exec-transfer', 'TransactionController@execTransfer')->name('exec-transfer');

Route::get('/account-mutation', 'AccountController@accountMutation')->name('account-mutation');
