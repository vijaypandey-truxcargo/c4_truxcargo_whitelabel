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
    
    $routes->get('role', 'Role::index', ['filter' => 'adminAuth']);
    $routes->get('role/index/(:num)', 'Role::index/$1', ['filter' => 'adminAuth']);
    $routes->get('role/add_role', 'Role::add_role', ['filter' => 'adminAuth']);
    $routes->post('role/insert_role', 'Role::insert_role', ['filter' => 'adminAuth']);
    $routes->get('role/edit_role/(:num)', 'Role::edit_role/$1', ['filter' => 'adminAuth']);
    $routes->post('role/update_role/(:num)', 'Role::update_role/$1', ['filter' => 'adminAuth']);
    $routes->post('role/delete_role', 'Role::delete_role', ['filter' => 'adminAuth']);
    $routes->get('role/export_ticket_role', 'Role::export_ticket_role', ['filter' => 'adminAuth']);
    $routes->get('role/export_sample_role', 'Role::export_sample_role', ['filter' => 'adminAuth']);
    $routes->post('role/import_role', 'Role::import_role', ['filter' => 'adminAuth']);

    $routes->get('kycType', 'KycType::index', ['filter' => 'adminAuth']);
    $routes->get('kycType/index/(:num)', 'KycType::index/$1', ['filter' => 'adminAuth']);
    $routes->get('kycType/add_kyc', 'KycType::add_kyc', ['filter' => 'adminAuth']);
    $routes->post('kycType/insert_kyc', 'KycType::insert_kyc', ['filter' => 'adminAuth']);
    $routes->get('kycType/edit_kyc/(:num)', 'KycType::edit_kyc/$1', ['filter' => 'adminAuth']);
    $routes->post('kycType/update_kyc/(:num)', 'KycType::update_kyc/$1', ['filter' => 'adminAuth']);
    $routes->post('kycType/delete_kyc', 'KycType::delete_kyc', ['filter' => 'adminAuth']);
    $routes->get('kycType/export_ticket_kyc', 'KycType::export_ticket_kyc', ['filter' => 'adminAuth']);
    $routes->get('kycType/export_sample_kyc', 'KycType::export_sample_kyc', ['filter' => 'adminAuth']);
    $routes->post('kycType/import_kyc', 'KycType::import_kyc', ['filter' => 'adminAuth']);

    $routes->get('state', 'State::index', ['filter' => 'adminAuth']);
    $routes->get('state/index/(:num)', 'State::index/$1', ['filter' => 'adminAuth']);
    $routes->get('state/add_state', 'State::add_state', ['filter' => 'adminAuth']);
    $routes->post('state/insert_state', 'State::insert_state', ['filter' => 'adminAuth']);
    $routes->get('state/edit_state/(:num)', 'State::edit_state/$1', ['filter' => 'adminAuth']);
    $routes->post('state/update_state/(:num)', 'State::update_state/$1', ['filter' => 'adminAuth']);
    $routes->post('state/delete_state', 'State::delete_state', ['filter' => 'adminAuth']);
    $routes->get('state/export_ticket_state', 'State::export_ticket_state', ['filter' => 'adminAuth']);
    $routes->get('state/export_sample_state', 'State::export_sample_state', ['filter' => 'adminAuth']);
    $routes->post('state/import_state', 'State::import_state', ['filter' => 'adminAuth']);

    $routes->match(['get', 'post'], 'vendor', 'Vendor::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'vendor/index', 'Vendor::index', ['filter' => 'adminAuth']);
    $routes->get('vendor/index/(:num)', 'Vendor::index/$1', ['filter' => 'adminAuth']);
    $routes->get('vendor/add_vendor', 'Vendor::add_vendor', ['filter' => 'adminAuth']);
    $routes->post('vendor/insert_vendor', 'Vendor::insert_vendor', ['filter' => 'adminAuth']);
    $routes->get('vendor/edit_vendor/(:num)', 'Vendor::edit_vendor/$1', ['filter' => 'adminAuth']);
    $routes->post('vendor/update_vendor/(:num)', 'Vendor::update_vendor/$1', ['filter' => 'adminAuth']);
    $routes->post('vendor/delete_vendor', 'Vendor::delete_vendor', ['filter' => 'adminAuth']);
    $routes->get('vendor/export_all', 'Vendor::export_all', ['filter' => 'adminAuth']);
    $routes->get('vendor/export_sample_vendor', 'Vendor::export_sample_vendor', ['filter' => 'adminAuth']);
    $routes->post('vendor/import_vendor', 'Vendor::import_vendor', ['filter' => 'adminAuth']);

    $routes->get('mode', 'Mode::index', ['filter' => 'adminAuth']);
    $routes->get('mode/index/(:num)', 'Mode::index/$1', ['filter' => 'adminAuth']);
    $routes->get('mode/add_mode', 'Mode::add_mode', ['filter' => 'adminAuth']);
    $routes->post('mode/insert_mode', 'Mode::insert_mode', ['filter' => 'adminAuth']);
    $routes->get('mode/edit_mode/(:num)', 'Mode::edit_mode/$1', ['filter' => 'adminAuth']);
    $routes->post('mode/update_mode/(:num)', 'Mode::update_mode/$1', ['filter' => 'adminAuth']);
    $routes->post('mode/delete_mode', 'Mode::delete_mode', ['filter' => 'adminAuth']);
    $routes->get('mode/export_ticket_mode', 'Mode::export_ticket_mode', ['filter' => 'adminAuth']);
    $routes->get('mode/export_sample_mode', 'Mode::export_sample_mode', ['filter' => 'adminAuth']);
    $routes->post('mode/import_mode', 'Mode::import_mode', ['filter' => 'adminAuth']);

    $routes->get('logout', 'Login::logout');

    $routes->get('dashboard/plans', 'Plans::index', ['filter' => 'adminAuth']);

    $routes->post('dashboard/save_plans', 'Plans::save_plans', ['filter' => 'adminAuth']);

    $routes->get('activityLog', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->get('activityLog/index', 'ActivityLog::index', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/user_login_activity', 'ActivityLog::user_login_activity', ['filter' => 'adminAuth']);
    $routes->match(['get', 'post'], 'activityLog/software_screen_time_report', 'ActivityLog::software_screen_time_report', ['filter' => 'adminAuth']);

});
