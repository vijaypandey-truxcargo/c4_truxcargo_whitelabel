<?php

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    $routes->get('/', 'Login::index');

    $routes->get('login', 'Login::index');

    $routes->post('login', 'Login::auth');

    $routes->match(['get', 'post'], 'dashboard', 'Dashboard::index', ['filter' => 'adminAuth']);

    $routes->post('dashboard/search', 'Dashboard::search', ['filter' => 'adminAuth']);

    $routes->get('dashboard/config', 'Dashboard::config', ['filter' => 'adminAuth']);

    $routes->post('dashboard/save_config/(:num)', 'Dashboard::save_config/$1', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'users', 'Users::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'users/index', 'Users::index', ['filter' => 'adminAuth']);
    $routes->get('users/index/(:num)', 'Users::index/$1', ['filter' => 'adminAuth']);
    $routes->get('users/add', 'Users::add', ['filter' => 'adminAuth']);
    $routes->post('users/insert', 'Users::insert', ['filter' => 'adminAuth']);
    $routes->get('users/edit_user/(:num)', 'Users::edit_user/$1', ['filter' => 'adminAuth']);
    $routes->post('users/update_user/(:num)', 'Users::update_user/$1', ['filter' => 'adminAuth']);
    $routes->post('users/delete', 'Users::delete', ['filter' => 'adminAuth']);
    $routes->get('users/user_export', 'Users::user_export', ['filter' => 'adminAuth']);

    $routes->get('logout', 'Login::logout');

    $routes->get('dashboard/plans', 'Plans::index', ['filter' => 'adminAuth']);

    $routes->post('dashboard/save_plans', 'Plans::save_plans');

    $routes->get('activityLog', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->get('activityLog/index', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/user_login_activity', 'ActivityLog::user_login_activity', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/software_screen_time_report', 'ActivityLog::software_screen_time_report', ['filter' => 'adminAuth']);

});
