<?php

// $routes->get('/', 'Home::index');   
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');
$routes->post('login/insert', 'Login::insert');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('logout', 'Login::logout');