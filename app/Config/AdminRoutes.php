<?php

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    $routes->get('/', 'Login::index');

    $routes->get('login', 'Login::index');

    $routes->post('login', 'Login::auth');

    $routes->match(['get', 'post'], 'dashboard', 'Dashboard::index');

    $routes->post('dashboard/search', 'Dashboard::search');

    $routes->get('dashboard/config', 'Dashboard::config');

    $routes->post('dashboard/save_config/(:num)', 'Dashboard::save_config/$1');

    $routes->get('logout', 'Login::logout');

    $routes->get('dashboard/plans', 'Plans::index');

    $routes->post('dashboard/save_plans', 'Plans::save_plans');

});
