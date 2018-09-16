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

        //category
        Route::get('/admin/goods/category/{id}/findByParentId', 'Goods\CategoryController@findByParentId');
        Route::get('/admin/goods/category/categorySort', 'Goods\CategoryController@categorySort');
        Route::get('/admin/goods/category/getCategory', 'Goods\CategoryController@getCategory');
        Route::resource('/admin/goods/category', 'Goods\CategoryController');

        Route::get('/admin/goods/brand/brandSort', 'Goods\BrandController@brandSort');
        Route::resource('/admin/goods/brand', 'Goods\BrandController');

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

        //speed
        Route::get('/admin/speed/findGoods', 'Market\GoodSpeedController@findGoods');
        Route::resource('/admin/speed', 'Market\GoodSpeedController');

        //order
        Route::post('/admin/order/{id}/refundment', 'Order\OrderController@refundment');
        Route::post('/admin/order/{id}/cancel', 'Order\OrderController@cancel');
        Route::post('/admin/order/{id}/deliver', 'Order\OrderController@deliver');
        Route::resource('/admin/order', 'Order\OrderController');

        //ad
        Route::post('/admin/ad/destroyAll', 'Tool\AdPositionController@destroyAll');
        Route::resource('/admin/ad', 'Tool\AdPositionController');

        //system
        Route::get('/admin/system/areas/getArea', 'System\AreasController@getArea');
        Route::resource('/admin/system/areas', 'System\AreasController');

        //adminUser
        Route::get('/admin/adminUser/editPwd', 'System\AdminUserController@editPassword');
        Route::post('/admin/adminUser/updatePwd', 'System\AdminUserController@updatePassword');
        Route::post('/admin/adminUser/destroyAll', 'System\AdminUserController@destroyAll');
        Route::resource('/admin/adminUser', 'System\AdminUserController');

        //user
        Route::get('/admin/user/merchantApply', 'Member\UserController@merchantApplyIndex');
        Route::post('/admin/user/{id}/merchantAudit', 'Member\UserController@merchantAudit');
        Route::get('/admin/user/wechatUsers', 'Member\UserController@wechatUserIndex');
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

    Route::group(array('namespace' => 'Users', 'middleware' => array('routeHistory','autoLogin')), function () {
        Route::resource('/', 'IndexController');

        Route::post('/category/getCategoryList', 'CategoryController@getCategoryList');
        Route::post('/category/getGoodsList', 'CategoryController@getGoodsList');
        Route::resource('/category', 'CategoryController');

        Route::post('/home/updatePwd', 'HomeController@updatePwd')->middleware('userAuth');
        Route::get('/home/changePwd', 'HomeController@changePwd')->middleware('userAuth');
        Route::get('/home/business', 'HomeController@business')->middleware('userAuth');
        Route::post('/home/saveBusiness', 'HomeController@saveBusiness')->middleware('userAuth');
        Route::resource('/home', 'HomeController')->middleware('userAuth');

        Route::post('/goods/search', 'GoodsController@search');
        Route::post('/goods/purchase', 'GoodsController@purchase')->middleware('userAuth');
        Route::post('/goods/changeSpec', 'GoodsController@changeSpec');
        Route::post('/goods/changeNum', 'GoodsController@changeNum');
        Route::resource('/goods', 'GoodsController');

        Route::get('/order/orderComplate/{ordersn}', 'OrderController@orderComplate')->middleware('userAuth');
        Route::post('/order/prepay', 'OrderController@prepay')->middleware('userAuth');
        Route::get('/order/cashPay/{ordersn}', 'OrderController@cashPay')->middleware('userAuth');
        Route::post('/order/confirmReceipt', 'OrderController@confirmReceipt')->middleware('userAuth');
        Route::post('/order/cancle', 'OrderController@cancle')->middleware('userAuth');
        Route::get('/order/detail/{ordersn}', 'OrderController@detail')->middleware('userAuth');
        Route::post('/order/getData', 'OrderController@getData')->middleware('userAuth');
        Route::get('/order/searchOrderResult/{ordersn}', 'OrderController@searchOrderResult')->middleware('userAuth');
        Route::get('/order/searchOrderRefundResult/{ordersn}', 'OrderController@searchOrderRefundResult')->middleware('userAuth');
        Route::post('/order/cartStore', 'OrderController@cartStore')->middleware('userAuth');
        Route::resource('/order', 'OrderController')->middleware('userAuth');

        Route::get('/address/getAllAreas', 'ExpressAddressController@getAllAreas')->middleware('userAuth');
        Route::get('/address/setDefault', 'ExpressAddressController@setDefault')->middleware('userAuth');
        Route::post('/address/getExpressAddress', 'ExpressAddressController@getExpressAddress')->middleware('userAuth');
        Route::resource('/address', 'ExpressAddressController')->middleware('userAuth');

        Route::post('/cart/purchase', 'CartController@purchase')->middleware('userAuth');
        Route::post('/cart/delCart', 'CartController@delCart');
        Route::resource('/cart', 'CartController');

        Route::resource('/promotions', 'PromotionsController');

        Route::get('/login/findPwd', 'LoginController@findPwd');
        Route::post('/login/sendSmsCode', 'LoginController@sendSmsCode');
        Route::post('/login/changePwd', 'LoginController@changePwd');
        Route::post('/login/doLogin', 'LoginController@doLogin');
        Route::get('/login/logout', 'LoginController@logout');
        Route::get('/login/showLog', 'LoginController@showLog');
        Route::get('/login/clearLog', 'LoginController@clearLog');
        Route::get('/login/showNotify', 'LoginController@showNotify');
        Route::resource('/login', 'LoginController');

        Route::resource('/register', 'RegisterController');
        Route::post('/register/doRegister', 'RegisterController@doRegister');
        Route::post('/register/sendSmsCode', 'RegisterController@sendSmsCode');
    });
    Route::any('/wechat/oauthCallback', 'WeChatController@oauthCallback');
});

Route::group(array('middleware' => array('api')), function () {
    Route::any('/wechat/payNotice', 'WeChatController@payNotice');
    Route::any('/wechat/refundNotice', 'WeChatController@refundNotice');
    Route::any('/wechat/templateMessageNotice/{noticeId}');
});
