<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
 */
Route::group(array('middleware' => array('web')), function () {
    Route::get('/getCaptcha', 'CaptchaController@getCaptcha');

    Route::any('/fileUpload/uploadFile', 'FileUploadController@uploadFile');
    Route::any('/fileUpload/uploadLocalFile', 'FileUploadController@uploadLocalFile');
    //fileUpload
    Route::resource('/fileUpload', 'FileUploadController');

    Route::get('/admin', 'Admins\LoginController@index');
    Route::post('/admin/login', 'Admins\LoginController@login');
    Route::group(array('namespace' => 'Admins', 'middleware' => array('adminLoginAuth', 'adminRightAuth', 'adminMenuSelect')), function () {
        Route::resource('/admin/home', 'HomeController');

        //system
        Route::get('/admin/system/areas/getArea', 'System\AreasController@getArea');
        Route::resource('/admin/system/areas', 'System\AreasController');
        Route::resource('/admin/system','System\SystemController');

        //category
        Route::get('/admin/goods/category/{id}/findByParentId', 'Goods\CategoryController@findByParentId');
        Route::get('/admin/goods/category/categorySort', 'Goods\CategoryController@categorySort');
        Route::get('/admin/goods/category/getCategory', 'Goods\CategoryController@getCategory');
        Route::resource('/admin/goods/category', 'Goods\CategoryController');

        //model
        Route::get('/admin/model/{id}/findSpec', 'Goods\ModelController@findSpecById');
        Route::post('/admin/model/destroyAll', 'Goods\ModelController@destroyAll');
        Route::resource('/admin/model', 'Goods\ModelController');

        //good
        Route::get('/admin/good/{id}/editGoodNum', 'Goods\GoodController@editGoodNum');
        Route::post('/admin/good/{id}/updateGoodNum', 'Goods\GoodController@updateGoodNum');
        Route::get('/admin/good/{id}/editGoodPrice', 'Goods\GoodController@editGoodPrice');
        Route::post('/admin/good/{id}/updateGoodPrice', 'Goods\GoodController@updateGoodPrice');
        Route::post('/admin/good/{id}/sort', 'Goods\GoodController@sort');
        Route::post('/admin/good/{id}/updateState', 'Goods\GoodController@updateState');
        Route::post('/admin/good/destroyAll', 'Goods\GoodController@destroyAll');
        Route::resource('/admin/good', 'Goods\GoodController');

        //order
        Route::post('/admin/order/{id}/refundment', 'Order\OrderController@refundment');
        Route::post('/admin/order/{id}/cancel', 'Order\OrderController@cancel');
        Route::post('/admin/order/{id}/deliver', 'Order\OrderController@deliver');
        Route::resource('/admin/order', 'Order\OrderController');

        //ad
        Route::post('/admin/ad/destroyAll', 'Tool\AdPositionController@destroyAll');
        Route::resource('/admin/ad', 'Tool\AdPositionController');

        //wechat

        Route::get('/admin/wechat/refund', 'Tool\WechatController@refund');
        Route::post('/admin/wechat/wechatRefund', 'Tool\WechatController@wechatRefund');
        Route::post('/admin/wechat/getOrderInfo', 'Tool\WechatController@getOrderInfo');
        Route::resource('/admin/wechat', 'Tool\WechatController');

        //adminUser
        Route::get('/admin/adminUser/editPwd', 'System\AdminUserController@editPassword');
        Route::post('/admin/adminUser/updatePwd', 'System\AdminUserController@updatePassword');
        Route::post('/admin/adminUser/destroyAll', 'System\AdminUserController@destroyAll');
        Route::resource('/admin/adminUser', 'System\AdminUserController');

        //agentType
        Route::resource('/admin/agentType', 'Member\AgentTypeController');

        //agent
        Route::post('/admin/agent/{id}/audit', 'Member\UserController@audit');
        Route::resource('/admin/agent', 'Member\AgentController');

        //user
        Route::post('/admin/user/{id}/updateState', 'Member\UserController@updateState');
        Route::resource('/admin/user', 'Member\UserController');

        //right
        Route::post('/admin/adminRight/{id}/sort', 'System\AdminRightController@sort');
        Route::post('/admin/adminRight/{id}/updateShowMenu', 'System\AdminRightController@updateShowMenu');
        Route::post('/admin/adminRight/destroyAll', 'System\AdminRightController@destroyAll');
        Route::resource('/admin/adminRight', 'System\AdminRightController');

        //role
        Route::post('/admin/adminRole/destroyAll', 'System\AdminRoleController@destroyAll');
        Route::resource('/admin/adminRole', 'System\AdminRoleController');

        //login
        Route::get('/admin/logout', 'LoginController@logout');
    });

    Route::group(array('namespace' => 'Users', 'middleware' => array('userAuth')), function () {
        Route::resource('/', 'IndexController');

        Route::post('/category/getCategoryList', 'CategoryController@getCategoryList');
        Route::post('/category/getGoodsList', 'CategoryController@getGoodsList');
        Route::resource('/category', 'CategoryController');

        Route::get('/home/withdraw', 'HomeController@withdraw');
        Route::get('/home/myTeam/{type}', 'HomeController@myTeam');
        Route::get('/home/shareQrCode', 'HomeController@shareQrCode');
        Route::get('/home/fund', 'HomeController@fund');
        Route::post('/home/getData', 'HomeController@getData');
        Route::get('/home/income/{payType}', 'HomeController@income');
        Route::get('/home/balance', 'HomeController@balance');
        Route::post('/home/getPayLogData', 'HomeController@getPayLogData');
        Route::resource('/home', 'HomeController');

		Route::get('/agent/orderComplate/{ordersn}', 'AgentController@orderComplate');
        Route::post('/agent/prepay', 'AgentController@prepay');
        Route::get('/agent/cashPay/{ordersn}', 'AgentController@cashPay');
        Route::resource('/agent', 'AgentController');

        Route::post('/goods/search', 'GoodsController@search');
        Route::any('/goods/purchase', 'GoodsController@purchase');
        Route::post('/goods/changeSpec', 'GoodsController@changeSpec');
        Route::post('/goods/changeNum', 'GoodsController@changeNum');
        Route::resource('/goods', 'GoodsController');

        Route::get('/order/orderComplate/{ordersn}', 'OrderController@orderComplate');
        Route::post('/order/withdraw', 'OrderController@withdraw');
        Route::post('/order/prepay', 'OrderController@prepay');
        Route::get('/order/cashPay/{ordersn}', 'OrderController@cashPay');
        Route::post('/order/confirmReceipt', 'OrderController@confirmReceipt');
        Route::post('/order/cancle', 'OrderController@cancle');
        Route::get('/order/detail/{ordersn}', 'OrderController@detail');
        Route::post('/order/getData', 'OrderController@getData');
        Route::get('/order/searchOrderResult/{ordersn}', 'OrderController@searchOrderResult');
        Route::get('/order/searchOrderRefundResult/{ordersn}', 'OrderController@searchOrderRefundResult');
        Route::post('/order/cartStore', 'OrderController@cartStore');
        Route::resource('/order', 'OrderController');

        Route::get('/address/getAllAreas', 'ExpressAddressController@getAllAreas');
        Route::get('/address/setDefault', 'ExpressAddressController@setDefault');
        Route::post('/address/getExpressAddress', 'ExpressAddressController@getExpressAddress');
        Route::resource('/address', 'ExpressAddressController');

        Route::get('/login/logout', 'LoginController@logout');
        Route::get('/login/showLog', 'LoginController@showLog');
        Route::get('/login/clearLog', 'LoginController@clearLog');
        Route::get('/login/showNotify', 'LoginController@showNotify');
        Route::resource('/login', 'LoginController');
    });

    //分享二维码
    Route::any('/wechat/shareQrCode/{id}', 'WeChatController@shareQrCode');
    Route::any('/wechat/oauthCallback', 'WeChatController@oauthCallback');
});

Route::group(array('middleware' => array('api')), function () {
	Route::any('/wechat/refundOrder', 'WeChatController@refundOrder');
    Route::any('/wechat/payNotice', 'WeChatController@payNotice');
    Route::any('/wechat/agentNotice', 'WeChatController@agentNotice');
    Route::any('/wechat/refundNotice', 'WeChatController@refundNotice');
    // Route::any('/wechat/templateMessageNotice/{noticeId}');

    Route::get('/wechat/getMaterialList', 'WeChatController@getMaterialList');
    Route::get('/wechat/createMenu', 'WeChatController@createMenu');
    Route::any('/wechat/response', 'WeChatController@response');
});
