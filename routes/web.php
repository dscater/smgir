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

Route::get('/', function () {

    return view('auth.login');
})->name('inicio');

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {

    // USUARIOS
    Route::get('users', 'UserController@index')->name('users.index');

    Route::get('users/create', 'UserController@create')->name('users.create');

    Route::post('users/store', 'UserController@store')->name('users.store');

    Route::get('users/edit/{usuario}', 'UserController@edit')->name('users.edit');

    Route::put('users/update/{usuario}', 'UserController@update')->name('users.update');

    Route::delete('users/destroy/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('users/getInfo', 'UserController@getInfo')->name('users.getInfo');

    // Configuración de cuenta
    Route::GET('users/configurar/cuenta/{user}', 'UserController@config')->name('users.config');

    // contraseña
    Route::PUT('users/configurar/cuenta/update/{user}', 'UserController@cuenta_update')->name('users.config_update');

    // foto de perfil
    Route::POST('users/configurar/cuenta/update/foto/{user}', 'UserController@cuenta_update_foto')->name('users.config_update_foto');

    // MACRO DISTRITOS
    Route::get('macro_distritos', 'MacroDistritoController@index')->name('macro_distritos.index');

    Route::get('macro_distritos/create', 'MacroDistritoController@create')->name('macro_distritos.create');

    Route::post('macro_distritos/store', 'MacroDistritoController@store')->name('macro_distritos.store');

    Route::get('macro_distritos/edit/{macro_distrito}', 'MacroDistritoController@edit')->name('macro_distritos.edit');

    Route::put('macro_distritos/update/{macro_distrito}', 'MacroDistritoController@update')->name('macro_distritos.update');

    Route::delete('macro_distritos/destroy/{macro_distrito}', 'MacroDistritoController@destroy')->name('macro_distritos.destroy');

    // DISTRITOS
    Route::get('distritos', 'DistritoController@index')->name('distritos.index');

    Route::get('distritos/create', 'DistritoController@create')->name('distritos.create');

    Route::post('distritos/store', 'DistritoController@store')->name('distritos.store');

    Route::get('distritos/edit/{distrito}', 'DistritoController@edit')->name('distritos.edit');

    Route::put('distritos/update/{distrito}', 'DistritoController@update')->name('distritos.update');

    Route::delete('distritos/destroy/{distrito}', 'DistritoController@destroy')->name('distritos.destroy');

    Route::get('distritos/getInfo/getOptionsPorMacroDistrito', 'DistritoController@getOptionsPorMacroDistrito')->name('distritos.getOptionsPorMacroDistrito');

    // ZONAS
    Route::get('zonas', 'ZonaController@index')->name('zonas.index');

    Route::get('zonas/create', 'ZonaController@create')->name('zonas.create');

    Route::post('zonas/store', 'ZonaController@store')->name('zonas.store');

    Route::get('zonas/edit/{zona}', 'ZonaController@edit')->name('zonas.edit');

    Route::put('zonas/update/{zona}', 'ZonaController@update')->name('zonas.update');

    Route::delete('zonas/destroy/{zona}', 'ZonaController@destroy')->name('zonas.destroy');

    // BASES
    Route::get('bases', 'BaseController@index')->name('bases.index');

    Route::get('bases/create', 'BaseController@create')->name('bases.create');

    Route::post('bases/store', 'BaseController@store')->name('bases.store');

    Route::get('bases/edit/{base}', 'BaseController@edit')->name('bases.edit');

    Route::put('bases/update/{base}', 'BaseController@update')->name('bases.update');

    Route::delete('bases/destroy/{base}', 'BaseController@destroy')->name('bases.destroy');

    // OBRAS
    Route::get('obras', 'ObraController@index')->name('obras.index');

    Route::get('obras/create', 'ObraController@create')->name('obras.create');

    Route::post('obras/store', 'ObraController@store')->name('obras.store');

    Route::get('obras/edit/{obra}', 'ObraController@edit')->name('obras.edit');

    Route::put('obras/update/{obra}', 'ObraController@update')->name('obras.update');

    Route::delete('obras/destroy/{obra}', 'ObraController@destroy')->name('obras.destroy');

    // OBRAS - REPORTES
    Route::get('obras/obra_reportes/{obra}', 'ObraReporteController@index')->name('obra_reportes.index');

    Route::post('obras/obra_reportes/store/{obra}', 'ObraReporteController@store')->name('obra_reportes.store');

    Route::post('obras/obra_reportes/update/{obra_reporte}', 'ObraReporteController@update')->name('obra_reportes.update');

    Route::delete('obras/obra_reportes/destroy/{obra_reporte}', 'ObraReporteController@destroy')->name('obra_reportes.destroy');

    //OBRAS - TECNICOS
    Route::delete('obra_tecnicos/destroy/{obra_tecnico}', 'ObraTecnicoController@destroy')->name('obra_tecnicos.destroy');

    // MANTENIMIENTOS
    Route::get('mantenimientos', 'MantenimientoController@index')->name('mantenimientos.index');

    Route::get('mantenimientos/create', 'MantenimientoController@create')->name('mantenimientos.create');

    Route::post('mantenimientos/store', 'MantenimientoController@store')->name('mantenimientos.store');

    Route::get('mantenimientos/edit/{mantenimiento}', 'MantenimientoController@edit')->name('mantenimientos.edit');

    Route::put('mantenimientos/update/{mantenimiento}', 'MantenimientoController@update')->name('mantenimientos.update');

    Route::delete('mantenimientos/destroy/{mantenimiento}', 'MantenimientoController@destroy')->name('mantenimientos.destroy');

    // MANTENIMIENTOS - REPORTES
    Route::get('mantenimientos/mantenimiento_reportes/{mantenimiento}', 'MantenimientoReporteController@index')->name('mantenimiento_reportes.index');

    Route::post('mantenimientos/mantenimiento_reportes/store/{mantenimiento}', 'MantenimientoReporteController@store')->name('mantenimiento_reportes.store');

    Route::post('mantenimientos/mantenimiento_reportes/update/{mantenimiento_reporte}', 'MantenimientoReporteController@update')->name('mantenimiento_reportes.update');

    Route::delete('mantenimientos/mantenimiento_reportes/destroy/{mantenimiento_reporte}', 'MantenimientoReporteController@destroy')->name('mantenimiento_reportes.destroy');

    //MANTENIMIENTOS - TECNICOS
    Route::delete('mantenimiento_tecnicos/destroy/{mantenimiento_tecnico}', 'MantenimientoTecnicoController@destroy')->name('mantenimiento_tecnicos.destroy');

    // GAEM
    Route::get('gaems', 'GaemController@index')->name('gaems.index');

    Route::get('gaems/create', 'GaemController@create')->name('gaems.create');

    Route::post('gaems/store', 'GaemController@store')->name('gaems.store');

    Route::get('gaems/edit/{gaem}', 'GaemController@edit')->name('gaems.edit');

    Route::put('gaems/update/{gaem}', 'GaemController@update')->name('gaems.update');

    Route::delete('gaems/destroy/{gaem}', 'GaemController@destroy')->name('gaems.destroy');

    // GAEM - REPORTES
    Route::get('gaems/gaem_reportes/{gaem}', 'GaemReporteController@index')->name('gaem_reportes.index');

    Route::post('gaems/gaem_reportes/store/{gaem}', 'GaemReporteController@store')->name('gaem_reportes.store');

    Route::post('gaems/gaem_reportes/update/{gaem_reporte}', 'GaemReporteController@update')->name('gaem_reportes.update');

    Route::delete('gaems/gaem_reportes/destroy/{gaem_reporte}', 'GaemReporteController@destroy')->name('gaem_reportes.destroy');

    //GAEM - TECNICOS
    Route::delete('gaem_tecnicos/destroy/{gaem_tecnico}', 'GaemTecnicoController@destroy')->name('gaem_tecnicos.destroy');

    // REPORTES
    Route::get('reportes', 'ReporteController@index')->name('reportes.index');

    Route::get('reportes/usuarios', 'ReporteController@usuarios')->name('reportes.usuarios');

    Route::get('reportes/trabajo_tecnicos', 'ReporteController@trabajo_tecnicos')->name('reportes.trabajo_tecnicos');
});
