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
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});

// Admin
Auth::routes();

Route::group(['middleware' => 'user.auth.status.check'], function () {
	
	Route::get('dashboard', 'DashboardController@index')->name('dashboard');

	Route::get('my-profile', 'ProfileController@index')->name('my-profile');
	Route::put('my-profile/{user}/update', 'ProfileController@update')->name('my-profile.update');

	Route::get('settings', 'SettingController@index')->name('settings.index');
	Route::get('settings/edit', 'SettingController@edit')->name('settings.edit');
	Route::put('settings', 'SettingController@update')->name('settings.update');

	Route::get('cards/{user}/create', 'CardController@createToUser')->name('cards.create-to-user');
	Route::post('cards/{user}', 'CardController@storeToUser')->name('cards.store-to-user');
	Route::post('cards/{card}/activate', 'CardController@activate')->name('cards.activate');
	Route::post('cards/{card}/cancel', 'CardController@cancel')->name('cards.cancel');
	Route::resource('cards', 'CardController');

	Route::get('reports/{date}', 'ReportController@getByDate')->name('reports.get-by-date');
	Route::post('reports/generate', 'ReportController@generateReport')->name('reports.generate');
	Route::get('reports/pdf', 'ReportController@returnPdfView')->name('reports.pdf');
	Route::resource('reports', 'ReportController');
	
	Route::post('notifications/enable', 'NotificationController@enableNotification')->name('notifications.enable');
	Route::post('notifications/disable', 'NotificationController@disableNotification')->name('notifications.disable');
	Route::get('notifications/fetch', 'NotificationController@fetchNotifications')->name('notifications.fetch');
	Route::get('notifications/mark-as-read', 'NotificationController@markNotificationsAsRead')->name('notifications.mark-as-read');
	Route::resource('notifications', 'NotificationController');

	Route::group(['middleware' => 'admin.check'], function () {
		Route::resource('users', 'UserController');

		Route::post('departments/{department}/detach/{user}', 'DepartmentController@detachUser')->name('departments.detach');
		Route::resource('departments', 'DepartmentController');

		Route::post('positions/{position}/detach/{user}', 'PositionController@detachUser')->name('positions.detach');
		Route::resource('positions', 'PositionController');

		Route::resource('devices', 'DeviceController');
	});
});

// Public
Route::get('/', 'ScanController@index')->name('scan.index');
Route::get('scan', 'ScanController@index');
Route::any('scan', 'ScanController@scan')->name('scan.scan');
Route::get('welcome/{card}', 'ScanController@welcome')->name('scan.welcome');
Route::any('action', 'ScanController@action')->name('scan.action');
