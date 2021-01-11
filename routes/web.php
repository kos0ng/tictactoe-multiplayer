<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::get('/', function () {

    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index']);
Route::get('/play/{id}', [HomeController::class, 'play']);
Route::get('/notif', [HomeController::class, 'notif']);
Route::get('/leaderboard', [HomeController::class, 'leaderboard']);
Route::get('/game/{id}', [HomeController::class, 'game']);
Route::get('/update/{id}', [HomeController::class, 'update']);
Route::post('/game/addSign', [HomeController::class, 'addSign']);
// Route::get('/home', 'HomeController@index');

// Route::get('/play', 'HomeController@play');
// Route::get('/game', 'HomeController@game');
// Route::get('/update', 'HomeController@update');
// Route::post('/addSign', 'HomeController@addSign');
