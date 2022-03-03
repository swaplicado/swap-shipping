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

Route::get('/logoutFromVerify', function(){
    Auth::logout();
    return redirect('/');
});

Auth::routes(['verify' => true]);

Route::post('/login', 'Auth\LoginController@authenticate')->name('MyLogin');

Route::middleware(['auth', 'verified', 'menu'])->group( function () {

    Route::get('/home', 'HomeController@index')->name('home')->middleware('home');
    Route::get('/logout', 'Auth\LoginController@logout');

    // Rutas Choferes
    Route::get('/drivers', 'DriverController@index')->name('drivers');
    Route::get('/drivers/create', 'DriverController@create')->name('crear_driver')->middleware('form');
    Route::post('/drivers', 'DriverController@store')->name('guardar_driver');
    Route::get('/drivers/{id}/edit', 'DriverController@edit')->name('editar_driver');
    Route::put('/drivers/{id}', 'DriverController@update')->name('actualizar_driver');
    Route::delete('/drivers/{id}', 'DriverController@destroy')->name('eliminar_driver');
    Route::put('/drivers/recover/{id}', 'DriverController@recover')->name('recuperar_driver');

    // Rutas Transportistas
    Route::get('/carriers', 'CarrierController@index')->name('carriers');
    Route::get('/carriers/create', 'CarrierController@create')->name('crear_carrier');
    Route::post('/carriers', 'CarrierController@store')->name('guardar_carrier');
    Route::get('/carriers/{id}/edit', 'CarrierController@edit')->name('editar_carrier');
    Route::put('/carriers/{id}', 'CarrierController@update')->name('actualizar_carrier');
    Route::delete('/carriers/{id}', 'CarrierController@destroy')->name('eliminar_carrier');
    Route::put('/carriers/recover/{id}', 'CarrierController@recover')->name('recuperar_carrier');
    Route::get('/carriersFiscalData/{id}', 'CarrierController@editFiscalData')->name('editar_carrierFiscalData');
    Route::put('/carriersFiscalData/{id}', 'CarrierController@updateFiscalData')->name('actualizar_carrierFiscalData');

    // Rutas asociados
    Route::get('/parners', 'BussinesParnerController@index')->name('parners');
    Route::get('/parners/create', 'BussinesParnerController@create')->name('crear_parner')->middleware('form');
    Route::post('/parners', 'BussinesParnerController@store')->name('guardar_parner');
    Route::get('/parners/{id}/edit', 'BussinesParnerController@edit')->name('editar_parner');
    Route::put('/parners/{id}', 'BussinesParnerController@update')->name('actualizar_parner');
    Route::delete('/parners/{id}', 'BussinesParnerController@destroy')->name('eliminar_parner');
    Route::put('/parners/recover/{id}', 'BussinesParnerController@recover')->name('recuperar_parner');

    //Rutas Vehiculos
    Route::get('/vehicles', 'VehicleController@index')->name('vehicles');
    Route::get('/vehicles/create', 'VehicleController@create')->name('crear_vehicle')->middleware('form');
    Route::post('/vehicles', 'VehicleController@store')->name('guardar_vehicle');
    Route::get('/vehicles/{id}/edit', 'VehicleController@edit')->name('editar_vehicle');
    Route::put('/vehicles/{id}', 'VehicleController@update')->name('actualizar_vehicle');
    Route::delete('/vehicles/{id}', 'VehicleController@destroy')->name('eliminar_vehicle');
    Route::put('/vehicles/recover/{id}', 'VehicleController@recover')->name('recuperar_vehicle');

    //Rutas Trailers
    Route::get('/trailers', 'TrailerController@index')->name('trailers');
    Route::get('/trailers/create', 'TrailerController@create')->name('crear_trailer')->middleware('form');
    Route::post('/trailers', 'TrailerController@store')->name('guardar_trailer');
    Route::get('/trailers/{id}/edit', 'TrailerController@edit')->name('editar_trailer');
    Route::put('/trailers/{id}', 'TrailerController@update')->name('actualizar_trailer');
    Route::delete('/trailers/{id}', 'TrailerController@destroy')->name('eliminar_trailer');
    Route::put('/trailers/recover/{id}', 'TrailerController@recover')->name('recuperar_trailer');

    //Rutas Usuarios
    Route::get('/users', 'UserController@index')->name('users');
    Route::get('/users/create', 'UserController@create')->name('crear_user');
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
    Route::get('/cfdiToPdf/{id}', 'CfdiController@index')->name('cfdiToPdf');
    Route::get('/verify', 'VerifyController@verifyJson')->name('verify');

    //Rutas config catalogos
        //Estados
    Route::get('states', 'StatesController@index')->name('states');
    Route::get('/states/{id}/edit', 'StatesController@edit')->name('editar_states');
    Route::put('/states/{id}', 'StatesController@update')->name('actualizar_states');
    Route::delete('##', 'StatesController@destroy')->name('eliminar_states');
    Route::put('##', 'StatesController@recover')->name('recuperar_states');
        //Municipios
    Route::get('municipalities', 'MunicipalitiesController@index')->name('municipalities');
    Route::get('/municipalities/{id}/edit', 'MunicipalitiesController@edit')->name('editar_municipalitie');
    Route::put('/municipalities/{id}', 'MunicipalitiesController@update')->name('actualizar_municipalitie');
        //Aseguradoras
    Route::get('/insurances', 'InsurancesController@index')->name('insurances');
    Route::get('/insurances/create', 'InsurancesController@create')->name('crear_insurance')->middleware('form');
    Route::post('/insurances', 'InsurancesController@store')->name('guardar_insurance');
    Route::get('/insurances/{id}/edit', 'InsurancesController@edit')->name('editar_insurance');
    Route::put('/insurances/{id}', 'InsurancesController@update')->name('actualizar_insurance');
    Route::delete('/insurances/{id}', 'InsurancesController@destroy')->name('eliminar_insurance');
    Route::put('/insurances/recover/{id}', 'InsurancesController@recover')->name('recuperar_insurance');
    
    //Series
    Route::get('/series', 'SeriesController@index')->name('series');
    Route::get('/series/create', 'SeriesController@create')->name('crear_serie')->middleware('form');
    Route::post('/series', 'SeriesController@store')->name('guardar_serie');
    Route::get('/series/{id}/edit', 'SeriesController@edit')->name('editar_serie');
    Route::put('/series/{id}', 'SeriesController@update')->name('actualizar_serie');
    Route::delete('/series/{id}', 'SeriesController@destroy')->name('eliminar_serie');
    Route::put('/series/recover/{id}', 'SeriesController@recover')->name('recuperar_serie');
    
    // Rutas Documentos
    Route::get('documents/{id?}', 'DocumentController@index')->name('documents');
    Route::resource('documents', 'DocumentController');
    Route::put('documents/restore/{id}', 'DocumentController@restore')->name('documents.restore');
    Route::get('documents/sign/{id}', 'DocumentController@sign')->name('documents.sign');
    Route::get('documents/cancel/{id}', 'DocumentController@cancel')->name('documents.cancel');

    // Configuraciones
    Route::prefix('config')->group(function () {
        // Rutas de configuración de impuestos
        Route::get('taxes', 'TaxConfigurationController@index')->name('config.taxes');
        Route::get('taxes/create', 'TaxConfigurationController@create')->name('config.taxes.create')->middleware('form');
        Route::post('taxes/store', 'TaxConfigurationController@store')->name('config.taxes.store');
        Route::get('taxes/edit/{id}', 'TaxConfigurationController@edit')->name('config.taxes.edit');
        Route::put('taxes/update/{id}', 'TaxConfigurationController@update')->name('config.taxes.update');
        Route::delete('taxes/{id}', 'TaxConfigurationController@destroy')->name('config.taxes.delete');
        Route::put('taxes/recover/{id}', 'TaxConfigurationController@recover')->name('config.taxes.recovery');

        // Rutas de configuración de certificados
        Route::post('certificates/store', 'CarrierController@storeCertificate')->name('config.certificates.store');
    });
    
    //Rutas Configuración
    Route::get('/config', 'ConfigController@index')->name('config');
    Route::get('/config/edit', 'ConfigController@edit')->name('editar_config');
    Route::put('/config/{id}', 'ConfigController@update')->name('actualizar_config');

    //Rutas mi perfil
    Route::get('/profile', 'ProfileController@index')->name('profile')->middleware('form');
    Route::put('/profileAdmin/{id}', 'ProfileController@updateAdmin')->name('actualizar_profileAdmin');
    Route::put('/profileClient/{id}', 'ProfileController@updateClient')->name('actualizar_profileClient');
    Route::put('/profileCarrier/{id}', 'ProfileController@updateCarrier')->name('actualizar_profileCarrier');
    Route::put('/profileDriver/{id}', 'ProfileController@updateDriver')->name('actualizar_profileDriver');
});


