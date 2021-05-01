<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AcoesBaratasController;

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

Route::get('/acoesBaratas' , [AcoesBaratasController::class, 'index']);
Route::post('/acoesBaratas', [AcoesBaratasController::class, 'store']);


// Laravel 8 - using a controller on the route
// Route::get('/', [MyController::class, 'methodName']);

// Laravel 8 - using a controller on the route
// Route::get('/', 'App\Http\Controllers\MyController@methodName');















// return string
// Route::get('/', funtion() {
//     return '';
// })

// return array
// Route::get('/', function() {
//     return ['','',];
// })

// return json object
// Route::get('/', function() {
//     return response()->json([
//         'name' => 'nome',
//     ]);
// })

// return function
// Route::get('/url', function() {
//     return redirect('/');
// })

