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

/*   */

Route::group(['prefix' => 'thmartApi'], function () {
    Route::post('token', 'TokenController@index');
    Route::post('mail', 'TokenController@mail');
    Route::post('code', 'TokenController@code');
});

Route::group(['prefix' => 'thmartApi/Ads/AdsPosition','namespace' => 'Ads\AdsPosition'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::get('detail', 'DetailController@index');
});

Route::group(['prefix' => 'thmartApi/Ads/Home','namespace' => 'Ads\Home'],function ($router) {
    Route::post('list', 'ListController@index');
    Route::post('clearHomepageData', 'ClearHomepageDataController@index');
});

Route::group(['prefix' => 'thmartApi/Ads','namespace' => 'Ads'],function ($router) {
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('detail', 'DetailController@index');
    Route::post('list', 'ListController@index');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
});

Route::group(['prefix' => 'thmartApi/Article','namespace' => 'Article'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('list', 'ListController@index');
    Route::post('detail', 'DetailController@index');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('adminArticleDetail', 'AdminArticleDetailController@index');
});

Route::group(['prefix' => 'thmartApi/Category','namespace' => 'Category'],function ($router) {
    Route::post('create', 'CreateController@index');
    Route::post('edit', 'EditController@index');
    Route::post('prop/edit', 'PropController@index');
    Route::post('list', 'ListController@index');
    Route::post('loopList', 'LoopListController@index');
});

Route::group(['prefix' => 'thmartApi/Category/Prop','namespace' => 'Category\Prop'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('delete', 'DeleteController@index');
    Route::post('list', 'ListController@index');
});

Route::group(['prefix' => 'thmartApi/Item','namespace' => 'Item'],function ($router) {
    Route::post('create', 'CreateController@index')->middleware('apiCheckSession');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('list', 'ListController@index');
    Route::post('detail', 'DetailController@index');
    Route::post('groupBuying', 'GroupBuyingListController@index');
    Route::post('itemEditDetail', 'ItemEditDetailController@index');
    Route::post('changeAudited', 'ChangeAuditedController@index')->middleware('apiCheckSession');
    Route::post('idItemList', 'ItemListController@index');
    Route::post('hotProducts', 'HotProductsController@index');
    Route::post('excel', 'ExcelController@index');
    Route::post('itemList', 'ListController@itemList');
});
   
Route::group(['prefix' => 'thmartApi/Item/Prop','namespace' => 'Item\Prop'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('delete', 'DeleteController@index');
    Route::post('list', 'ListController@index');
});

Route::group(['prefix' => 'thmartApi/Item/Brand','namespace' => 'Item\Brand'],function ($router) {
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('list', 'ListController@index');
});

Route::group(['prefix' => 'thmartApi/Sku','namespace' => 'Sku'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('detail', 'DetailController@index');
});

Route::group(['prefix' => 'thmartApi/Sku/PriceStock','namespace' => 'Sku\PriceStock'],function ($router) {
    Route::post('edit', 'EditController@index');
});

Route::group(['prefix' => 'thmartApi/User','namespace' => 'User'],function ($router) {
    Route::post('mobileCode', 'MobileCodeController@index');
    Route::post('mobileRegister', 'MobileRegisterController@index');
    Route::post('mobileLogin', 'MobileLoginController@index');
    Route::post('checkMobileAndCode', 'CheckMobileAndCodeController@index');
    Route::post('mobileRegisterByPc', 'MobileRegisterByPcController@index');
    Route::post('resetPassword', 'ResetPasswordController@index');
    Route::post('changeHead', 'ChangeHeadController@index');
    Route::post('detail', 'DetailController@index');
    Route::post('changeNickName', 'ChangeNickNameController@index');
    Route::post('changePassword', 'ChangePasswordController@index');
});

Route::group(['prefix' => 'thmartApi/User/Wx', 'namespace' => 'User\Wx'],function ($router) {
    Route::get('login', 'LoginController@index');
    /*Route::get('PcLogin', 'PcLoginController@index');*/
    Route::post('wxBindMobile', 'WxBindMobileController@index');
    Route::post('wxPcBindMobile', 'WxPcBindMobileController@index');
    Route::post('miniProgram', 'MiniProgramController@index');
});

Route::group(['prefix' => 'thmartApi/User/Address', 'namespace' => 'User\Address'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('list', 'ListController@index');
    Route::post('defaultDetail', 'DefaultDetailController@index');
    Route::post('delete', 'DeleteController@index');
    Route::post('changeDefault', 'ChangeDefaultController@index');
    Route::post('detail', 'DetailController@index');
});

Route::group(['prefix' => 'thmartApi/Brand','namespace' => 'Brand'],function ($router) {
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('list', 'ListController@index');
    Route::post('detail', 'DetailController@index');
    Route::post('editNumber', 'EditNumberController@index');
});

Route::group(['prefix' => 'thmartApi/Cart','namespace' => 'Cart'],function ($router) {
    Route::post('edit', 'EditController@index');
    Route::post('list', 'ListController@index');
    Route::post('delete', 'DeleteController@index');
    Route::post('editNumber', 'EditNumberController@index');
    Route::post('changeSelectAndTotalPrice', 'ChangeSelectAndTotalPriceController@index');
});

Route::group(['prefix' => 'thmartApi/ItemSale','namespace' => 'ItemSale'],function ($router) {
    Route::post('edit', 'EditController@index')/*->middleware('apiCheckSession')*/;
    Route::post('delete', 'DeleteController@index')/*->middleware('apiCheckSession')*/;
    Route::post('excelEdit', 'ExcelEditController@index')/*->middleware('apiCheckSession')*/;
});

Route::group(['prefix' => 'thmartApi/Coupon','namespace' => 'Coupon'],function ($router) {
    Route::post('edit', 'EditController@index')/*->middleware('apiCheckSession')*/;
    Route::post('list', 'ListController@index');
    Route::post('itemList', 'ItemListController@index');
    Route::post('get', 'GetController@index');
    Route::post('delete', 'DeleteCouponController@index')/*->middleware('apiCheckSession')*/;
    Route::post('skuList', 'SkuListController@index');
    Route::post('deleteCouponSku', 'DeleteCouponSkuController@index');
    Route::post('editSkuList', 'EditSkuListController@index');
});

Route::group(['prefix' => 'thmartApi/Order','namespace' => 'Order'],function ($router) {
    Route::post('prepareOrder', 'PrepareOrderController@index');
    Route::post('placeOrder', 'PlaceOrderController@index');
    Route::post('payOrderDetail', 'PayOrderDetailController@index');
    Route::post('orderSuccess', 'OrderSuccessController@index');
    Route::post('list', 'OrderListController@index');
    Route::post('detail', 'DetailController@index');
    Route::post('delete', 'DeleteController@index');
    Route::post('excel', 'ExcelController@index')->middleware('apiCheckSession');
    Route::post('addLogistics', 'AddLogisticsController@index')->middleware('apiCheckSession');
    Route::post('ordersSkuDetail', 'OrdersSkuDetailController@index');
});

Route::group(['prefix' => 'thmartApi/OrderSpell','namespace' => 'OrderSpell'],function ($router) {
    Route::post('detail', 'DetailController@index');
    Route::post('check', 'CheckController@index');
    Route::post('checkTwo', 'CheckTwoController@index');
    Route::post('checkThree', 'CheckThreeController@index');
});

Route::group(['prefix' => 'thmartApi/Wx','namespace' => 'Wx'],function ($router) {
    Route::get('openidPayPage', 'OpenidPayPageController@index');
    Route::any('notify', 'WxController@notify');
    Route::post('orderQuery', 'WxController@orderQuery');
    Route::any('qrcode', 'WxController@qrcode');
    Route::post('miniProgramParam', 'MiniProgramParamController@index');
});

Route::group(['prefix' => 'thmartApi/Alipay','namespace' => 'Alipay'],function ($router) {
    Route::get('alipayapi', 'AlipayController@alipayapi');
    Route::post('notify', 'AlipayController@notifyurl');
    Route::get('return', 'AlipayController@returnurl');
    Route::post('orderQuery', 'AlipayController@orderQuery');
    Route::any('alipayapiPc', 'AlipayController@alipayapiPc');
    Route::any('notifyurlPc', 'AlipayController@notifyurlPc');
    Route::any('returnurlPc', 'AlipayController@returnurlPc');
});

Route::group(['prefix' => 'thmartApi/Collect','namespace' => 'Collect'],function ($router) {
    Route::post('collect', 'CollectController@index');
    Route::post('list', 'ListController@index');
});

Route::group(['prefix' => 'thmartApi/Supplier','namespace' => 'Supplier'],function ($router) {
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('detail', 'DetailController@index')->middleware('apiCheckSession');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('effective', 'EffectiveController@index')->middleware('apiCheckSession');
    Route::post('point', 'SupplierPrecentage\DetailController@index')/*->middleware('apiCheckSession')*/;
});

Route::group(['prefix' => 'thmartApi/Staff','namespace' => 'Staff'],function ($router) {
    Route::post('login', 'LoginController@index');
    Route::get('list', 'ListController@index')->middleware('apiCheckSession');
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('resetPassword', 'ResetPasswordController@index')->middleware('apiCheckSession');
    Route::get('Role/list', 'Role\ListController@index')->middleware('apiCheckSession');
    Route::post('Role/edit', 'Role\EditController@index')->middleware('apiCheckSession');
    Route::post('Role/detail', 'Role\DetailController@index')->middleware('apiCheckSession');
    Route::post('Role/delete', 'Role\DeleteController@index')->middleware('apiCheckSession');
    Route::get('Auth/list', 'Auth\ListController@index')->middleware('apiCheckSession');
    Route::post('Auth/edit', 'Auth\EditController@index')->middleware('apiCheckSession');
    Route::post('Auth/detail', 'Auth\DetailController@index')->middleware('apiCheckSession');
    Route::post('Auth/delete', 'Auth\DeleteController@index')->middleware('apiCheckSession');
    Route::post('Supplier/list', 'Supplier\ListController@index')->middleware('apiCheckSession');
});

Route::group(['prefix' => 'thmartApi/Logistics','namespace' => 'Logistics'],function ($router) {
    Route::post('detail', 'DetailController@index');
    Route::post('twoLatest', 'TwoLatestController@index');
});

Route::group(['prefix' => 'thmartApi/Comment','namespace' => 'Comment'],function ($router) {
    Route::post('delete', 'DeleteController@index')->middleware('apiCheckSession');
    Route::post('edit', 'EditController@index')->middleware('apiCheckSession');
    Route::post('detail', 'DetailController@index')->middleware('apiCheckSession');
    Route::post('changeStatus', 'ChangeStatusController@index')->middleware('apiCheckSession');
    Route::post('list', 'ListController@index');
    Route::post('add', 'UserAddController@index');
});

Route::group(['prefix' => 'thmartApi/Crontab','namespace' => 'Crontab'],function ($router) {
    Route::get('changeOrderStatus', 'ChangeOrderStatusController@index');
});

Route::group(['prefix' => 'thmartApi/Invite','namespace' => 'Invite'],function ($router) {
    Route::any('add', 'InviteController@add');
    Route::post('saveUserId', 'InviteController@saveUserId');
});

Route::get('thmartApi/sql', 'SqlController@index');
Route::get('thmartApi/linux', 'LinuxController@index');
Route::get('thmartApi/linuxTwo', 'LinuxTwoController@index');
Route::get('thmartApi/mongodb', 'MongodbController@index');

Route::get('user/{id}', function ($id) {
    return 'User '.$id;
});


