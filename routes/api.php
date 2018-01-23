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

Route::get('/postcodes/search', ['as' => 'api.postcodes.search', 'uses' => 'PostcodesSearchController@index']);
Route::get('/postcodes/position', ['as' => 'api.postcodes.position', 'uses' => 'PostcodesPositionController@index']);
