<?php

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

Route::get('/home', function () {
    return view('home');
});

Route::get('/login', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/logout', 'Auth\LoginController@logout');

// Rutas Choferes
Route::get('/drivers', 'TransFigureController@index')->name('drivers');
Route::get('/drivers/create', 'TransFigureController@create')->name('crear_driver');
Route::post('/drivers', 'TransFigureController@store')->name('guardar_driver');
Route::get('/drivers/{id}/edit', 'TransFigureController@edit')->name('editar_driver');
Route::put('/drivers/{id}', 'TransFigureController@update')->name('actualizar_driver');
Route::delete('/drivers/{id}', 'TransFigureController@destroy')->name('eliminar_driver');
Route::put('/drivers/recover/{id}', 'TransFigureController@recover')->name('recuperar_driver');

// Rutas Transportistas
Route::get('/carriers', 'CarrierController@index')->name('carriers');
Route::get('/carriers/create', 'CarrierController@create')->name('crear_carrier');
Route::post('/carriers', 'CarrierController@store')->name('guardar_carrier');
Route::get('/carriers/{id}/edit', 'CarrierController@edit')->name('editar_carrier');
Route::put('/carriers/{id}', 'CarrierController@update')->name('actualizar_carrier');
Route::delete('/carriers/{id}', 'CarrierController@destroy')->name('eliminar_carrier');
Route::put('/carriers/recover/{id}', 'CarrierController@recover')->name('recuperar_carrier');

//Rutas Vehiculos
Route::get('/vehicles', 'VehicleController@index')->name('vehicles');
Route::get('/vehicles/create', 'VehicleController@create')->name('crear_vehicle');
Route::post('/vehicles', 'VehicleController@store')->name('guardar_vehicle');
Route::get('/vehicles/{id}/edit', 'VehicleController@edit')->name('editar_vehicle');
Route::put('/vehicles/{id}', 'VehicleController@update')->name('actualizar_vehicle');
Route::delete('/vehicles/{id}', 'VehicleController@destroy')->name('eliminar_vehicle');
Route::put('/vehicles/recover/{id}', 'VehicleController@recover')->name('recuperar_vehicle');