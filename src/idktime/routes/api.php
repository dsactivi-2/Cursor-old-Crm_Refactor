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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('reports/get-reports', 'ReportController@getReports')->name('reports.get-reports');
Route::get('reports/{user_id}/get-report', 'ReportController@getReport')->name('reports.get-report');
Route::get('reports/get-employees', 'ReportController@getEmployees')->name('reports.get-employees');
Route::get('reports/{user_id}/get-employee', 'ReportController@getEmployee')->name('reports.get-employee');
Route::post('reports/filter', 'ReportController@reportFilter')->name('reports.filter');

