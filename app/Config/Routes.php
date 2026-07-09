<?php

    namespace Config;

    $routes = Services::routes();

    require APPPATH . 'Config/CustomerRoutes.php';
    require APPPATH . 'Config/AdminRoutes.php';

?>
