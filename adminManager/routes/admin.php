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

/**
 * 不走验证登陆和权限验证中间件
 * 基本权限
 */
Route::group([
    'prefix'    => config('app.admin-router.prefix'),
    'namespace' => config('app.admin-router.namespace'),
], function(Router $router) {

    //用户登录
    $router->group([
        'prefix'    => 'index',
        'namespace' => 'Index',
    ], function(Router $router) {
        $router->post('/login', "loginController@login");
        $router->post('/logout', "loginController@logout");
        $router->get('/verification', "CaptchaController@createCode");

    });
    //退出登录
    $router->group([
        'prefix'    => 'index',
        'namespace' => 'Index',
    ], function(Router $router) {
        $router->post('/login', "LoginController@login");
        $router->post('/logout', "LoginController@logout");
    });
});

/**
 * 通过验证登陆中间件
 */
Route::group(config('app.admin-router'), function(Router $router) {

    /**
     * 通过操作日志中间件
     */
    $router->group(['middleware' => 'doLog'], function(Router $router) {

        $router->group([
            'prefix'    => 'index',
            'namespace' => 'Index',
        ], function(Router $router) {
            $router->put('/editUserPassword', "LoginController@editLoginPassword");
        });
        //平台线路下拉
        $router->group([
            'prefix'    => '',
            'namespace' => 'Config',
        ], function(Router $router) {
            $router->get('/client', "ApiClientsController@clientSelect");

        });

        //主页展示图表
        $router->group([
            'prefix'    => 'index',
            'namespace' => 'Index',
        ], function(Router $router) {
            $router->get('/echarts', "EchatsController@showChat");

        });

        //Excel
        $router->group([
            'prefix'    => '',
            'namespace' => 'Excel',
        ], function(Router $router) {
            $router->get('/excel/export', "ExcelController@export");
        });
        //银行管理
        $router->group([
            'prefix'    => 'bank',
            'namespace' => 'Bank',
        ], function(Router $router) {
            $router->get('/inBank', "InBankController@selectInBank");
            $router->post('/inBank', "InBankController@addInBank");
            $router->put('/inBank', "InBankController@editInBank");
            $router->delete('/inBank', "InBankController@delInBank");

            $router->get('/outBank', "OutBankController@selectOutBank");
            $router->post('/outBank', "OutBankController@addOutBank");
            $router->put('/outBank', "OutBankController@editOutBank");
            $router->delete('/outBank', "OutBankController@delOutBank");
        });

        //订单管理
        $router->group([
            'prefix'    => 'order',
            'namespace' => 'Order',
        ], function(Router $router) {
            $router->get('/inOrder', "InOrderController@inOrderSelect");
            $router->post('/inOrder', "InOrderController@inOrderLower");
            $router->get('/inOrderFind', "InOrderController@inOrderFind");

            $router->get('/outOrder', "OutOrderController@outOrderSelect");
            $router->post('/outOrder', "OutOrderController@outOrderLower");
            $router->get('/outOrderFind', "OutOrderController@outOrderFind");
        });

        //白名单管理
        $router->group([
            'prefix'    => 'whitelist',
            'namespace' => 'Whitelist',
        ], function(Router $router) {
            $router->get('/payWhitelist', "WhitelistController@payWhitelistSelect");
            $router->post('/payWhitelist', "WhitelistController@payWhitelistAdd");
            $router->put('/payWhitelist', "WhitelistController@payWhitelistUpdate");
        });

        //配置管理
        $router->group([
            'prefix'    => 'config',
            'namespace' => 'Config',
        ], function(Router $router) {
            $router->get('/payConfig', "PayConfigController@payConfigSelect");
            $router->post('/payConfig', "PayConfigController@payConfigAdd");
            $router->put('/payConfig', "PayConfigController@payConfigUpdate");
            $router->get('/configLists', "PayConfigController@getConfigLists");
            $router->get('/topTwenty', "PayConfigController@topTwenty");

            $router->get('/callBack', "PayCallBackUrlController@payCallBackUrlSelect");
            $router->post('/callBack', "PayCallBackUrlController@payCallBackUrlAdd");
            $router->put('/callBack', "PayCallBackUrlController@payCallBackUrlUpdate");

            $router->get('/payType', "PayTypeController@payTypeSelect");
            $router->get('/payTypeList', "PayTypeController@payTypeList");
            $router->post('/payType', "PayTypeController@payTypeAdd");
            $router->put('/payType', "PayTypeController@payTypeUpdate");

            $router->get('/apiClients', "ApiClientsController@apiClientsSelect");
            $router->post('/apiClients', "ApiClientsController@apiClientsAdd");
            $router->put('/apiClients', "ApiClientsController@apiClientsUpdate");
            $router->delete('/apiClients', "ApiClientsController@apiClientsDelete");
        });

        //商户管理
        $router->group([
            'prefix'    => 'merchant',
            'namespace' => 'Merchant',
        ], function(Router $router) {
            $router->get('/inMerchant', "InMerchantController@inMerchantSelect");
            $router->post('/inMerchant', "InMerchantController@inMerchantAdd");
            $router->put('/inMerchant', "InMerchantController@inMerchantUpdate");
            $router->delete('/inMerchant', "InMerchantController@inMerchantDel");
            $router->get('/ownType', "InMerchantController@getOwnPayType");

            $router->get('/outMerchant', "OutMerchantController@OutMerchantSelect");
            $router->post('/outMerchant', "OutMerchantController@OutMerchantAdd");
            $router->put('/outMerchant', "OutMerchantController@OutMerchantUpdate");
            $router->delete('/outMerchant', "OutMerchantController@OutMerchantDel");
        });

        //维护管理
        $router->group([
            'prefix'    => 'maintain',
            'namespace' => 'Maintain',
        ], function(Router $router) {
            $router->get('/Maintain', "MaintainController@maintainSelect");
            $router->put('/Maintain', "MaintainController@maintainUpdate");
            $router->post('/Maintain', "MaintainController@maintainAdd");
        });

        // 缓存管理
        $router->group([
            'prefix'    => 'cache',
            'namespace' => 'Cache',
        ], function(Router $router) {
            $router->post('/cache/{option}', 'CacheController@cacheOption');
        });

        //账号管理
        $router->group([
            'prefix'    => 'auth',
            'namespace' => 'Auth',
        ], function(Router $router) {
            $router->get('/users', "UsersController@usersSelect");
            $router->put('/users', "UsersController@usersUpdate");
            $router->put('/editPassword', "UsersController@usersPwdUpdate");
            $router->put('/editOwnPwd', "UsersController@editOwnPwd");
            $router->post('/users', "UsersController@usersAdd");
            $router->delete('/users', "UsersController@usersDel");
            $router->get('/clientTree', "UsersController@clientTree");
            $router->get('/rolesList', "UsersController@getRoleList");
            $router->get('/getInfo', "UsersController@getUsers");

            //角色管理
            $router->get('/roles', "RolesController@rolesSelect");
            $router->put('/roles', "RolesController@rolesUpdate");
            $router->post('/roles', "RolesController@rolesAdd");
            $router->delete('/roles', "RolesController@rolesDel");

            //权限管理
            $router->get('/permission', "PermissionController@permissionSelect");
            $router->get('/getPermissions', "PermissionController@getPermissions");
            $router->get('/getMenus', "PermissionController@getMenus");
            $router->put('/permission', "PermissionController@permissionUpdate");
            $router->post('/permission', "PermissionController@permissionAdd");
            $router->delete('/permission', "PermissionController@permissionDel");

            //菜单管理
            $router->get('/menu', "MenusController@menusSelect");
            $router->put('/menu', "MenusController@menusUpdate");
            $router->post('/menu', "MenusController@menusAdd");
            $router->delete('/menu', "MenusController@menusDel");
        });

        //日志管理
        $router->group([
            'prefix'    => 'logs',
            'namespace' => 'Logs',
        ], function(Router $router) {
            $router->get('/loginLogs', 'LoginLogController@loginLogSelect');
            $router->get('/operateLogs', 'DoLogController@doLogSelect');
            $router->get('/callbackLogs', 'CallbackLogsController@callbackLogSelect');
            $router->get('/sendCallbackLogs', 'SendCallbackLogsController@sendCallbackLogSelect');
            $router->get('/orderLogs', 'OrderLogsController@orderLogSelect');
            $router->get('/apiLogs', 'ApiOperationLogsController@apiOperationLogSelect');
            $router->get('/errorLogsA', 'ErrorLogsController@ErrorLogsAdminSelect');
            $router->get('/errorLogsF', 'ErrorLogsController@ErrorLogsFrontSelect');
            $router->get('/requestLogs', 'RequestLogsController@requestLogSelect');

        });

        // 运营分析
        $router->group([
            'prefix'    => 'chart',
            'namespace' => 'Chart',
        ], function(Router $router) {
            $router->get('/getChartData', 'ChartAnalysisController@getChartData');
        });

    });
});
