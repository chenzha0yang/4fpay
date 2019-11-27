<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => config('app.api-route.namespace'),
], function (Router $router) {
    $router->get('/', 'ViewPageController@showHome');
    $router->get('/home', 'ViewPageController@showHome');
    $router->get('/tokenErr', 'ViewPageController@showError');
    $router->any('/return_url', 'ViewPageController@showReturn');

    $router->get('/quickPage', 'QuickPayController@showQuickPage');
    $router->post('/quickPage', 'QuickPayController@quickPage')->name('quickPage');

});
