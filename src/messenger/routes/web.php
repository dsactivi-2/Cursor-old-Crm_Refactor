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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/phonenumber', 'HomeController@phoneChange')->name('phoneChange');

Route::post('/updateNjemJezik', 'HomeController@updateNjemJezik');
Route::post('/addDatumApl', 'HomeController@addDatumApl');
Route::post('/addDatumTermina', 'HomeController@addDatumTermina');
Route::post('/addProcjenaTermina', 'HomeController@addProcjenaTermina');
Route::post('/updateVozacka', 'HomeController@updateVozacka');
Route::post('/addSrednjaSkola', 'HomeController@addSrednjaSkola');
Route::post('/addVisokaSkola', 'HomeController@addVisokaSkola');
Route::post('/addDodatnaEdukacija', 'HomeController@addDodatnaEdukacija');
Route::post('/addIskustvo', 'HomeController@addIskustvo');
Route::post('/updateStatusMessenger', 'HomeController@updateStatusMessenger');
Route::post('/addViza', 'HomeController@addViza');
Route::post('/addKategorijeVozacke', 'HomeController@addKategorijeVozacke');
Route::post('/editUserAnswers', 'HomeController@editUserAnswers');
Route::post('/updateStatusObrade', 'HomeController@updateStatusObrade');
Route::post('/editSkole', 'HomeController@editSkole');
Route::post('/editIskustva', 'HomeController@editIskustva');
Route::post('/deleteDoc', 'HomeController@deleteDoc');
Route::post('/updateAdresa', 'HomeController@updateAdresa');
Route::post('/editInformacije', 'HomeController@editInformacije');
Route::post('/updateLastOnline', 'HomeController@updateLastOnline');
Route::post('/editUserDipl', 'HomeController@editUserDipl');
Route::post('/editBotOglasi', 'HomeController@editBotOglasi');
Route::post('/updateNjemJezikNovi', 'HomeController@updateNjemJezikNovi');
Route::post('/insertDatumRod', 'HomeController@insertDatumRod');
Route::post('/updateTel', 'HomeController@updateTel');
Route::post('/updateStatusNotf', 'HomeController@updateStatusNotf');
Route::post('/getHoursForAppointment', 'HomeController@getHoursForAppointment');

/* ROUTES Lang */
Route::get('lang/{locale}', function ($locale){
  Session::put('locale', $locale);
  return redirect()->back(); 
  //return redirect(url()->previous()); 
});