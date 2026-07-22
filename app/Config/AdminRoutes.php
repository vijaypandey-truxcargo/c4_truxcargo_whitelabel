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

    $routes->get('company', 'Company::index', ['filter' => 'adminAuth']);
    $routes->get('company/index/(:num)', 'Company::index/$1', ['filter' => 'adminAuth']);
    $routes->get('company/add_company', 'Company::add_company', ['filter' => 'adminAuth']);
    $routes->post('company/insert_company', 'Company::insert_company', ['filter' => 'adminAuth']);
    $routes->get('company/edit_company/(:num)', 'Company::edit_company/$1', ['filter' => 'adminAuth']);
    $routes->post('company/update_company/(:num)', 'Company::update_company/$1', ['filter' => 'adminAuth']);
    $routes->post('company/delete_company', 'Company::delete_company', ['filter' => 'adminAuth']);
    $routes->get('company/export_ticket_company', 'Company::export_ticket_company', ['filter' => 'adminAuth']);
    $routes->get('company/export_sample_company', 'Company::export_sample_company', ['filter' => 'adminAuth']);
    $routes->post('company/import_company', 'Company::import_company', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'master/hub_masters', 'Master::hub_masters', ['filter' => 'adminAuth']);
    $routes->get('master/hub_masters/(:num)', 'Master::hub_masters/$1', ['filter' => 'adminAuth']);
    $routes->get('master/add_hub', 'Master::add_hub', ['filter' => 'adminAuth']);
    $routes->post('master/insert_hub', 'Master::insert_hub', ['filter' => 'adminAuth']);
    $routes->get('master/edit_hub/(:num)', 'Master::edit_hub/$1', ['filter' => 'adminAuth']);
    $routes->post('master/update_hub/(:num)', 'Master::update_hub/$1', ['filter' => 'adminAuth']);
    $routes->post('master/delete_hub', 'Master::delete_hub', ['filter' => 'adminAuth']);
    $routes->get('master/export_ticket_hub', 'Master::export_ticket_hub', ['filter' => 'adminAuth']);
    $routes->get('master/export_sample_hub', 'Master::export_sample_hub', ['filter' => 'adminAuth']);
    $routes->post('master/import_hub', 'Master::import_hub', ['filter' => 'adminAuth']);

    $routes->get('logout', 'Login::logout');

    $routes->get('dashboard/plans', 'Plans::index', ['filter' => 'adminAuth']);

    $routes->post('dashboard/save_plans', 'Plans::save_plans', ['filter' => 'adminAuth']);

    $routes->get('activityLog', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->get('activityLog/index', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/user_login_activity', 'ActivityLog::user_login_activity', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/software_screen_time_report', 'ActivityLog::software_screen_time_report', ['filter' => 'adminAuth']);

});
