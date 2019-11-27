<?php

use Illuminate\Routing\Router;

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

Route::group([
    'prefix' => config('app.api-route.prefix'),
    'namespace' => config('app.api-route.namespace'),
    'middleware' => config('app.api-route.middleware'),
], function (Router $router) {

    // 获取Token
    $router->group(['middleware' => 'client'], function (Router $router) {
        $router->post('/token', 'TokenController@getToken');
    });

    // 支付路径
    $router->group(['middleware' => ['maintain', 'payToken']], function (Router $router) {
        $router->post('/buy', 'IndexController@recharge');
        $router->get('/buy', 'IndexController@recharge');
    });

    // 出款路径
    $router->group(['middleware' => ['maintain', 'outToken']], function (Router $router) {
        $router->post('/out', 'OutIndexController@recharge');
    });

    // 快捷支付
    $router->group(['middleware' => ['checkPayment']], function (Router $router) {
        $router->post('/quick', 'QuickController@payment');
    });

    // 入款 异步回调路径
    $router->any('/{callback}/callback', 'CallbackController@callback');
    // 出款 异步回调路径
    $router->any('/{callback}/out_callback', 'OutCallbackController@callback');

    $router->any('/notifyBack', 'QuickPayController@callback');

    $router->get('/alipay', 'QuickPayController@csApi');
});

Route::group([
    'prefix' => config('app.api-route2.prefix'),
    'namespace' => config('app.api-route2.namespace'),
    'middleware' => config('app.api-route2.middleware'),
], function (Router $router) {

    // 获取Token
    $router->group(['middleware' => 'client'], function (Router $router) {
        $router->post('/token', 'TokenController@getToken');
    });

    // 支付路径
    $router->group(['middleware' => ['maintain', 'payToken']], function (Router $router) {
        $router->post('/buy', 'IndexController@recharge');
        $router->get('/buy', 'IndexController@recharge');
    });

    // 出款路径
    $router->group(['middleware' => ['maintain', 'outToken']], function (Router $router) {
        $router->post('/out', 'OutIndexController@recharge');
    });

    // 快捷支付
    $router->group(['middleware' => ['checkPayment']], function (Router $router) {
        $router->post('/quick', 'QuickController@payment');
    });

    // 入款 异步回调路径
    $router->any('/{callback}/callback', 'CallbackController@callback');
    // 出款 异步回调路径
    $router->any('/{callback}/out_callback', 'OutCallbackController@callback');

    $router->any('/notifyBack', 'QuickPayController@callback');

    $router->post('/alipay', 'QuickPayController@csApi');
});
