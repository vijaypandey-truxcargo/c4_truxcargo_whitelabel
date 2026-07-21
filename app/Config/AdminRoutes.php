<?php

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    $routes->get('/', 'Login::index');

    $routes->get('login', 'Login::index');

    $routes->post('login', 'Login::auth');

    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('logout', 'Login::logout');

});