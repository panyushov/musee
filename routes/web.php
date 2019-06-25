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

use Illuminate\Support\Facades\Route;

Route::get('/', 'MainController@index')->name('musee.index');
Route::get('/generator', 'MainController@generator')->name('musee.generator');
Route::post('/generator', 'MainController@startGeneration')->name('musee.generator.post')->middleware('generating');
Route::post('/config', 'MainController@processConfig')->name('musee.config.post')->middleware('generating');
Route::get('/config', 'MainController@config')->name('musee.config');