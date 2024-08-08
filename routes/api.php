<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('umis_server')->group(function () {
Route::namespace('App\Http\Controllers\Employee')->group(function () {
    Route::get('FetchData', 'ImportEmployeeController@FetchList');

});
});



Route::group(['middleware' => ['stripTags']], function () {
    // Define your routes here
});
