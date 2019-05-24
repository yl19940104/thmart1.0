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

/*Route::get('/', function () {    
    return view('welcome');
});

Route::group(['prefix' => 'Ads/AdsPosition','namespace' => 'Ads\AdsPosition'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->get('detail', 'DetailController@index');
});

Route::group(['prefix' => 'Ads/Home','namespace' => 'Ads\Home'],function ($router) {
    $router->post('list', 'ListController@index');
});

Route::group(['prefix' => 'Ads','namespace' => 'Ads'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('detail', 'DetailController@index');
    $router->post('list', 'ListController@index');
});

Route::group(['prefix' => 'Article','namespace' => 'Article'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('list', 'ListController@index');
    $router->post('detail', 'DetailController@index');
});

Route::group(['prefix' => 'Category','namespace' => 'Category'],function ($router) {
    $router->post('create', 'CreateController@index');
    $router->post('edit', 'EditController@index');
    $router->post('prop/edit', 'PropController@index');
});

Route::group(['prefix' => 'Category/Prop','namespace' => 'Category\Prop'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('delete', 'DeleteController@index');
});

Route::group(['prefix' => 'Item','namespace' => 'Item'],function ($router) {
    $router->post('create', 'CreateController@index');
    $router->post('edit', 'EditController@index');
    $router->post('list', 'ListController@index')->middleware('checkToken');
    $router->get('detail', 'DetailController@index');
});

Route::group(['prefix' => 'Item/Prop','namespace' => 'Item\Prop'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('delete', 'DeleteController@index');
});

Route::group(['prefix' => 'Item/Brand','namespace' => 'Item\Brand'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('list', 'ListController@index');
});

Route::group(['prefix' => 'Sku','namespace' => 'Sku'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('detail', 'DetailController@index');
});

Route::group(['prefix' => 'Sku/PriceStock','namespace' => 'Sku\PriceStock'],function ($router) {
    $router->post('edit', 'EditController@index');
});

Route::group(['prefix' => 'Category','namespace' => 'Category'],function ($router) {
    $router->post('list', 'ListController@index');
});

Route::group(['prefix' => 'User','namespace' => 'User'],function ($router) {
    $router->post('mobileCode', 'MobileCodeController@index');
    $router->post('mobileRegister', 'MobileRegisterController@index');
    $router->post('mobileLogin', 'MobileLoginController@index');
    $router->post('checkMobileAndCode', 'CheckMobileAndCodeController@index');
    $router->post('resetPassword', 'ResetPasswordController@index');
});

Route::group(['prefix' => 'User/Wx', 'namespace' => 'User\Wx'],function ($router) {
    $router->get('login', 'LoginController@index');
});

Route::group(['prefix' => 'Brand','namespace' => 'Brand'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('list', 'ListController@index');
    $router->post('detail', 'DetailController@index');
});

Route::group(['prefix' => 'Cart','namespace' => 'Cart'],function ($router) {
    $router->post('edit', 'EditController@index');
    $router->post('list', 'ListController@index');
});

Route::post('token', 'TokenController@index');*/