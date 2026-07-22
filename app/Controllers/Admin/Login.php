<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use App\Models\SupportModel;
use Config\Database;

class Login extends BaseController
{
    protected $supportModel;
    protected $loginModel;
    protected $db;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->db = Database::connect();
        $this->supportModel = new SupportModel();
        $this->loginModel = new LoginModel();
    }

    public function index()
    {
        if (session()->get('admin_login_id')) {
            return redirect()->to('/admin/dashboard');
        }

        $data = [
            'page_title' => 'Admin Login',
            'body'       => 'bg-dark',
            'data'       => $this->supportModel->find_col('config', 'logo', 1),
        ];

        return view('admin/login', $data);
    }

    public function auth()
    {
       
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }

        $username       = $this->request->getPost('username');
        $password       = $this->request->getPost('password');
        $continue_login = $this->request->getPost('continue_login');

        $login_id = $this->loginModel->login_valid($username, $password);
 
        if (! $login_id) {
            session()->setFlashdata('error', 'Invalid Username/Password');
            session()->setFlashdata('error_class', 'alert-danger');
            return redirect()->to('/admin/login');
        }

        $check = $this->loginModel->profile($login_id);

        $loginName = trim(
            ($check->first ?? '') . ' ' . ($check->last ?? '')
        );

        if (empty($loginName)) {
            $loginName = $check->userName ?? $check->username ?? $check->name ?? 'Admin';
        }

        $this->db->table('user_login_activity')->insert([
            'user_id'    => $login_id,
            'name'       => $loginName,
            'login_as'   => 'Admin',
            'login_time' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'session_id' => session_id(),
            'status'     => 'LOGIN',
        ]);

        session()->set('admin_login_activity_id', $this->db->insertID());

        if (! empty($check->last_activity)) {
            $minutes = (time() - strtotime($check->last_activity)) / 60;

            if ($minutes > 30) {
                $this->supportModel->update(
                    'users',
                    [
                        'is_login'    => 0,
                        'login_token' => '',
                    ],
                    $login_id
                );

                $check->is_login = 0;
            }
        }

        if ($check->is_login == 1 && empty($continue_login)) {
            session()->setFlashdata(
                'login_warning',
                'Your account is currently open on another device. Continue logging in here? The previous session will be signed out automatically.'
            );

            return redirect()->to('/admin/login');
        }

        $token = md5(uniqid());

        $this->supportModel->update(
            'users',
            [
                'is_login'      => 1,
                'login_token'   => $token,
                'last_activity' => date('Y-m-d H:i:s'),
            ],
            $login_id
        );

        $permissions = $this->loginModel->permission('admin');
        if (empty($permissions)) {
            $permissions = $this->loginModel->permission('Admin');
        }

        session()->set([
            'admin_id'             => $login_id,
            'admin_login_token'    => $token,
            'admin_user'           => $check,
            'admin_login_id'       => $login_id,
            'admin_permission'     => $permissions,
        ]);

        session()->remove('new');

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        $session = session();
        $id = $session->get('admin_login_id');
        $activityId = $session->get('admin_login_activity_id');

        if ($activityId) {
            $this->db->table('user_login_activity')
                ->where('id', $activityId)
                ->update([
                    'logout_time' => date('Y-m-d H:i:s'),
                    'status'      => 'LOGOUT',
                ]);
        }

        if (! empty($id)) {
            $this->supportModel->update(
                'users',
                [
                    'is_login'    => 0,
                    'login_token' => '',
                ],
                $id
            );
        }

        $session->destroy();

        return redirect()->to('/admin/login');
    }

    public function dashboard()
    {
        return redirect()->to('/admin/dashboard');
    }
}
