<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use duncan3dc\Sonos\Network;

Route::group(['prefix' => 'v1'], function() {
	Route::get('current/{type}/{name?}', 'SensorController@query');
	Route::post('set/{type}/{value}', 'ActuatorController@set');
});