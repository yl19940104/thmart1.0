<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['prefix' => 'thmartAdmin', 'middleware' => 'checkSession'], function () {
    Route::get('homepage', 'Index\HomepageController@index');
    Route::group(['prefix' => 'User', 'namespace' => 'User'], function () {
	    Route::get('list', 'ListController@index');
	    Route::get('edit', 'EditController@index');
	    Route::get('Role/list', 'Role\ListController@index');
	    Route::get('Role/edit', 'Role\EditController@index');
	    Route::get('Auth/list', 'Auth\ListController@index');
	    Route::get('Auth/edit', 'Auth\EditController@index');
	});
	Route::group(['prefix' => 'Item', 'namespace' => 'Item'], function () {
	    Route::get('first', 'FirstController@index');
	    Route::get('second', 'SecondController@index');
	    Route::get('list', 'ListController@index');
	    Route::get('Brand/list', 'Brand\ListController@index');
	    Route::get('Brand/detail', 'Brand\DetailController@index');
	});
	Route::group(['prefix' => 'Supplier', 'namespace' => 'Supplier'], function () {
	    Route::get('list', 'ListController@index');
	    Route::get('detail', 'DetailController@index');
	});
	Route::group(['prefix' => 'Sale', 'namespace' => 'Sale'], function () {
	    Route::get('typeOneList', 'TypeOneListController@index');
	    Route::get('typeTwoList', 'TypeTwoListController@index');
        Route::get('typeThreeList', 'TypeThreeListController@index');
	});
	Route::group(['prefix' => 'Coupon', 'namespace' => 'Coupon'], function () {
	    Route::get('list', 'ListController@index');
	    Route::get('skuList', 'SkuListController@index');
	});
	Route::group(['prefix' => 'Order', 'namespace' => 'Order'], function () {
	    Route::get('list', 'ListController@index');
	});
	Route::group(['prefix' => 'Ads', 'namespace' => 'Ads'], function () {
	    Route::get('position', 'PositionListController@index');
	    Route::get('list', 'ListController@index');
	});
	Route::group(['prefix' => 'Article', 'namespace' => 'Article'], function () {
	    Route::get('list', 'ListController@index');
	    Route::get('detail', 'DetailController@index');
	});
    Route::group(['prefix' => 'Comment', 'namespace' => 'Comment'], function () {
        Route::get('list', 'ListController@index');
        Route::get('edit', 'EditController@index');
    });
});

Route::get('thmartAdmin/login', 'User\LoginController@index');
Route::get('thmartAdmin/logout', 'User\LoginController@logout');

