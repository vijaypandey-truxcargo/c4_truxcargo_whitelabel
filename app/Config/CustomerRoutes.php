<?php

// $routes->get('/', 'Home::index');   
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');
$routes->post('login/insert', 'Login::insert');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('plans', 'Plans::index');
$routes->get('logout', 'Login::logout');

$routes->get('jobstatus/checkNdr', 'Jobstatus::checkNdr');
$routes->get('jobstatus/status/(:segment)', 'Jobstatus::status/$1');
$routes->get('jobstatus/pod_details_get/(:segment)', 'Jobstatus::pod_details_get/$1');
