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

Route::post('/save_phone', 'KandidatController@store');
Route::post('/form', 'KandidatController@sendToForm');
Route::post('/save_info', 'KandidatController@save_info');
Route::post('/resend_pin', 'KandidatController@resend_pin');

Route::get('/save_contract/{id}', 'KandidatController@save_contract'); 
Route::get('/save_passport/{id}', 'KandidatController@save_passport');  
Route::get('/thanksmessage', 'KandidatController@thanksmessage'); 

Auth::routes();
 
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

