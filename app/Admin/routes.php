<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->group([
        'prefix' => 'auth',
    ], function (Router $router) {

        $router->resource('users', "UserController");
    });
    $router->resource('doctors', "DoctorController");
    $router->resource('surgeries', "SurgeryController");
    $router->resource('departments', "DepartmentController");
});
