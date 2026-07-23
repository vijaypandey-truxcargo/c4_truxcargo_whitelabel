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

    $routes->match(['get', 'post'], 'pincode/criticalLog', 'Pincode::criticalLog', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/criticalLog/(:num)', 'Pincode::criticalLog/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_critical_log', 'Pincode::import_critical_log', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_critical_log', 'Pincode::delete_critical_log', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_critical_log', 'Pincode::export_critical_log', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/bluedart_surface', 'Pincode::bluedart_surface', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/bluedart_surface/(:num)', 'Pincode::bluedart_surface/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_bluedart_surface', 'Pincode::import_bluedart_surface', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_bluedart_surface', 'Pincode::delete_bluedart_surface', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_bluedart_surface', 'Pincode::export_bluedart_surface', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/bluedart_air', 'Pincode::bluedart_air', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/bluedart_air/(:num)', 'Pincode::bluedart_air/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_bluedart_air', 'Pincode::import_bluedart_air', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_bluedart_air', 'Pincode::delete_bluedart_air', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_bluedart_air', 'Pincode::export_bluedart_air', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/bluedart_dp', 'Pincode::bluedart_dp', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/bluedart_dp/(:num)', 'Pincode::bluedart_dp/$1', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/bluedart_dP', 'Pincode::bluedart_dp', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_bluedart_dp', 'Pincode::import_bluedart_dp', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_bluedart_dp', 'Pincode::delete_bluedart_dp', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_bluedart_dp', 'Pincode::export_bluedart_dp', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/bluedart', 'Pincode::bluedart', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/bluedart/(:num)', 'Pincode::bluedart/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_bluedart', 'Pincode::import_bluedart', ['filter' => 'adminAuth']);
    $routes->post('pincode/surface_bluedart', 'Pincode::surface_bluedart', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_bluedart', 'Pincode::delete_bluedart', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_bluedart', 'Pincode::export_bluedart', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/delhivery_pincode', 'Pincode::delhivery_pincode', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/delhivery_pincode/(:num)', 'Pincode::delhivery_pincode/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_delhivery_pincode', 'Pincode::import_delhivery_pincode', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_delhivery_pincode', 'Pincode::delete_delhivery_pincode', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_delhivery_pincode', 'Pincode::export_delhivery_pincode', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/xpressbees_pincode', 'Pincode::xpressbees_pincode', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/xpressbees_pincode/(:num)', 'Pincode::xpressbees_pincode/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_xpressbees_pincode', 'Pincode::import_xpressbees_pincode', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_xpressbees_pincode', 'Pincode::delete_xpressbees_pincode', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_xpressbees_pincode', 'Pincode::export_xpressbees_pincode', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'pincode/sales_pincode', 'Pincode::sales_pincode', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'pincode/sales_pincode/(:num)', 'Pincode::sales_pincode/$1', ['filter' => 'adminAuth']);
    $routes->post('pincode/import_sales_pincode', 'Pincode::import_sales_pincode', ['filter' => 'adminAuth']);
    $routes->post('pincode/delete_sales_pincode', 'Pincode::delete_sales_pincode', ['filter' => 'adminAuth']);
    $routes->get('pincode/export_sales_pincode', 'Pincode::export_sales_pincode', ['filter' => 'adminAuth']);

    $routes->get('logout', 'Login::logout');

    $routes->get('dashboard/plans', 'Plans::index', ['filter' => 'adminAuth']);

    $routes->post('dashboard/save_plans', 'Plans::save_plans', ['filter' => 'adminAuth']);

    $routes->get('activityLog', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->get('activityLog/index', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/user_login_activity', 'ActivityLog::user_login_activity', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/software_screen_time_report', 'ActivityLog::software_screen_time_report', ['filter' => 'adminAuth']);

});
