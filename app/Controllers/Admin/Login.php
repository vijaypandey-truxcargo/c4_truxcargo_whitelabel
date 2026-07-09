<?php

namespace App\Controllers\Admin;

class Login extends \App\Controllers\Login
{
    protected string $loginPage = '/admin/login';
    protected string $redirectAfterLogin = '/admin/dashboard';
    protected string $redirectAfterLogout = '/admin/login';
}
