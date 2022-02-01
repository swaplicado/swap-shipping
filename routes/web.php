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

Route::get('/login', function () {
    return view('auth/login');
});

Auth::routes();

Route::post('/login', 'Auth\LoginController@authenticate')->name('MyLogin');

Route::get('/cfdi', 'CfdiController@generatePDF')->name('cfdi');

Route::middleware('auth')->group( function () {

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

    //Rutas Trailers
    Route::get('/trailers', 'TrailerController@index')->name('trailers');
    Route::get('/trailers/create', 'TrailerController@create')->name('crear_trailer');
    Route::post('/trailers', 'TrailerController@store')->name('guardar_trailer');
    Route::get('/trailers/{id}/edit', 'TrailerController@edit')->name('editar_trailer');
    Route::put('/trailers/{id}', 'TrailerController@update')->name('actualizar_trailer');
    Route::delete('/trailers/{id}', 'TrailerController@destroy')->name('eliminar_trailer');
    Route::put('/trailers/recover/{id}', 'TrailerController@recover')->name('recuperar_trailer');

    //Rutas Usuarios
    Route::get('/users', 'UserController@index')->name('users');
    Route::post('/users', 'UserController@store')->name('guardar_user');
    Route::get('/users/{id}/edit', 'UserController@edit')->name('editar_user');
    Route::put('/users/{id}', 'UserController@update')->name('actualizar_user');
    Route::delete('/users/{id}', 'UserController@destroy')->name('eliminar_user');
    Route::put('/users/recover/{id}', 'UserController@recover')->name('recuperar_user');
    //Rutas Roles de usuario
    Route::get('/role', 'RoleUserController@index')->name('role');
    Route::get('/role/create', 'RoleUserController@create')->name('crear_role');
    Route::post('/role', 'RoleUserController@store')->name('guardar_role');
    Route::get('/role/{id}/edit', 'RoleUserController@edit')->name('editar_role');
    Route::put('/role/{id}', 'RoleUserController@update')->name('actualizar_role');
    Route::delete('/role/{id}', 'RoleUserController@destroy')->name('eliminar_role');
    Route::put('/role/recover/{id}', 'RoleUserController@recover')->name('recuperar_role');

    //Rutas cfdi
    Route::get('/cfdiToPdf', 'CfdiController@index')->name('cfdiToPdf');
});

// Rutas Documentos
Route::resource('documents', 'DocumentController');
Route::put('documents/restore/{id}', 'DocumentController@restore')->name('documents.restore');

