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

//Route::get('/', function () { return view('welcome'); });

Route::auth();

Route::group(['middleware' => 'auth'], function () {
	Route::get('/', 'HomeController@index');

	Route::get('user', ['uses'=>'UserController@index', 'as'=>'user.index']);
	Route::get('user/{id}', ['uses'=>'UserController@show', 'as'=>'user.show']);
	Route::get('user/{id}/edit', ['uses'=>'UserController@edit', 'as'=>'user.edit']);

	// clients
	Route::get('client', ['uses'=>'ClientController@index', 'as'=>'client.index']);
	Route::get('client/{id}', ['uses'=>'ClientController@show', 'as'=>'client.show']);
	Route::get('client/{id}/edit', ['uses'=>'ClientController@edit', 'as'=>'client.edit']);
	Route::get('client/{id}/charge', ['uses'=>'ClientController@charge', 'as'=>'client.charge']);

	// Order Route
	Route::get('order', ['uses'=>'OrderController@index', 'as'=>'order.index']);
	Route::get('order/pickup', ['uses'=>'OrderController@pickup', 'as'=>'order.pickup']);
	Route::post('order/pickup', ['uses'=>'OrderController@pickup']);
	Route::get('order/{id}/show', ['uses'=>'OrderController@show', 'as'=>'order.show']);
	Route::get('order/{id}/tracking', ['uses'=>'OrderController@tracking', 'as'=>'order.tracking']);

	// Product Route
	Route::get('product', ['uses'=>'ProductController@index', 'as'=>'product.index']);
	Route::post('product/search', ['uses'=>'ProductController@search', 'as'=>'product.search']);
	Route::post('product', ['uses'=>'ProductController@store', 'as'=>'product.store']);

	// Finance Route
	Route::get('transcation', ['uses'=>'TranscationController@index', 'as'=>'transcation.index']);

	// batches
	Route::get('batch', ['uses'=>'BatchController@index', 'as'=>'batch.index']);
	Route::get('batch/{id}', ['uses'=>'BatchController@show', 'as'=>'batch.show']);
	Route::post('batch', ['uses'=>'BatchController@store', 'as'=>'batch.store']);
	Route::post('batch/{id}/add', ['uses'=>'BatchController@add', 'as'=>'batch.add']);
	Route::post('batch/{id}/remove', ['uses'=>'BatchController@remove', 'as'=>'batch.remove']);
	
	// batches logistics router group
	Route::post('batch/{id}/to1', ['uses'=>'BatchController@to1', 'as'=>'batch.to1']);
	Route::post('batch/{id}/to5', ['uses'=>'BatchController@to5', 'as'=>'batch.to5']);
	Route::post('batch/{id}/to10', ['uses'=>'BatchController@to10', 'as'=>'batch.to10']);
	Route::post('batch/{id}/to15', ['uses'=>'BatchController@to15', 'as'=>'batch.to15']);
	Route::post('batch/{id}/to20', ['uses'=>'BatchController@to20', 'as'=>'batch.to20']);
	Route::post('batch/{id}/to25', ['uses'=>'BatchController@to25', 'as'=>'batch.to25']);
	
	// settings
	Route::get('setting', ['uses'=>'SettingController@index', 'as'=>'setting.index']);
	Route::post('setting', ['uses'=>'SettingController@store', 'as'=>'setting.store']);

	Route::resource('address', 'AddressController');
});

/*
|--------------------------------------------------------------------------
| Dingo API
|--------------------------------------------------------------------------
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    	
    	// // NIM Server cc end point
    	// $api->post('/cc', 'CCController@cc');

     //    $api->post('/user/pin', 'UserController@pin');
     //    $api->post('/user/verifypin', 'UserController@verifyPin');

        // Alipay notify
        $api->post('/tracking', 'KD100APIController@tracking');

     //    // IAP notify
     //    $api->post('/iap/notify', 'IAPController@notify');

    });
});
