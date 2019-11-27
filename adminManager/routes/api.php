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
    'prefix' => config('app.api-router.prefix'),
    'namespace' => config('app.api-router.namespace'),
    'middleware' => ['requestLog']
], function (Router $router) {
    $router->post('/order/list', 'OrderListController@distribution');

    $router->post('/business/up', 'BusinessController@distribution');

    $router->post('/outBusiness/up', 'OutBusinessController@distribution');

    $router->post('/merchant/list', 'MerchantController@distribution');

    $router->post('/outMerchant/list', 'OutMerchantController@distribution');

    $router->post('/config/list', 'ConfigController@distribution');

    $router->post('/bank/list', 'BankListController@distribution');

    $router->post('/type/list', 'PayTypeController@distribution');

    $router->post('/paymentBank', 'PaymentBankController@distribution');

});
