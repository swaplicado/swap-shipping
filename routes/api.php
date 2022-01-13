<?php

use Illuminate\Http\Request;

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

Route::post('login', [
         'uses' => 'api\\AuthController@login'
     ]);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('requestdocument', [
         'uses' => 'api\\DocumentRequestController@store'
     ]);

Route::group(['middleware' => 'auth:api'], function() {

    /**
     * API Routes
    */

    /*
    * get employees
    **/
    // Route::get('employees', [
    //     'uses' => 'AccessControlController@getEmployees'
    // ]);
});
