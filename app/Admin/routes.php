<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('positions', 'PositionsController');
    $router->resource('apply_records', 'ApplyRecordsController');
    $router->resource('categories', 'CategoriesController');

});
