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

//Auth::routes();
Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
	Route::get('/', function () {
	    return redirect('/login');
	});

    //Home
	Route::get('/home', 'IndexController@index')->name('home');

	Route::group(['prefix' => 'profile', 'as'=>'profile'], function(){
		Route::get('/', 'ProfileController@profile')->name('.profile');
        Route::get('/query/login', 'ProfileController@query_login')->name('.query.login');
        Route::get('/query/activity', 'ProfileController@query_activity')->name('.query.activity');
		Route::get('/edit', 'ProfileController@edit_profile')->name('.edit.profile');
		Route::post('/edit', 'ProfileController@update_profile')->name('.update.profile');
	});
    
    Route::group(['prefix' => 'users', 'as'=>'users', 'namespace' => 'Users'], function(){
        Route::get('/', 'UserController@index')->name('.index');
        Route::post('/query', 'UserController@query')->name('.query');
        Route::get('/create', 'UserController@create')->name('.create');
        Route::post('/store', 'UserController@store')->name('.store');
        Route::get('/edit/{id}', 'UserController@edit')->name('.edit');
        Route::post('/edit/{id}', 'UserController@update')->name('.update');
    });
    
    //Topup
    Route::group(['prefix' => 'topup', 'namespace' => 'Topup', 'as'=>'topup'], function(){
        Route::get('/', 'IndexController@index')->name('.index');
        Route::post('/brand-lists', 'IndexController@getBrands')->name('.getBrands');
        Route::post('/product-denomination', 'IndexController@getProductList')->name('.getProductList');
        Route::post('/create-transaction', 'IndexController@createTransaction')->name('.createTransaction');
    });

    //Transaction
    Route::group(['prefix' => 'transactions', 'namespace' => 'Transactions', 'as'=>'transactions'], function(){
        Route::get('/', 'IndexController@index')->name('.index');
        Route::post('/query', 'IndexController@query')->name('.query');
        Route::get('/details/{uuid}', 'IndexController@details')->name('.details');
        Route::post('/inquiry/{uuid}', 'IndexController@inquiry')->name('.inquiry');
        Route::post('/export', 'IndexController@export')->name('.export');
    });

    Route::match(['get','post'],'transaction-search', 'Transactions\IndexController@search')->name('transaction.search');
	
});
