<?php

use App\Http\Controllers\HomePage;
use App\Http\Controllers\LinearRegression;
use App\Http\Controllers\PolinomialRegression;
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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [HomePage::class, 'index'])->name('home');
Route::get('/linear', [LinearRegression::class, 'getRegress'])->name('linear');
Route::get('/polynomial', [PolinomialRegression::class, 'getRegress'])->name('polynomial');
