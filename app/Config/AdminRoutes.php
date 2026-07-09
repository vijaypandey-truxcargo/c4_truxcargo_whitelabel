<?php

$routes->group('admin', function ($routes) {

    $routes->get('/', 'Admin\Login::index');

    $routes->get('login', 'Admin\Login::index');

    $routes->post('login', 'Admin\Login::auth');

    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->get('logout', 'Admin\Login::logout');

});