<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SupportModel;
use App\Models\UserModel;
use Config\Database;

class Login extends BaseController
{
    protected $supportModel;
    protected $userModel;
    protected $db;
    protected string $loginPage = '/login';
    protected string $redirectAfterLogin = '/dashboard';
    protected string $redirectAfterLogout = '/login';

    public function __construct()
    {
        helper(['url','form']);
        $this->db = Database::connect();
        $this->supportModel = new SupportModel();
        $this->userModel    = new UserModel();
    }

    public function index()
    {
        if (session()->get('login_id')) {
            return redirect()->to($this->redirectAfterLogin);
        }

        $data = [
            'page_title' => 'Client Panel',
            'body'       => 'bg-dark',
            'data'       => $this->supportModel->find_col('config', 'logo', 1),
        ];  

        return view('head', $data)
            . view('login', $data)
            . view('footer1');
    }


    public function insert()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {

            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $username       = $this->request->getPost('username');
        $password       = $this->request->getPost('password');
        $continue_login = $this->request->getPost('continue_login');

        $login_id = $this->userModel->login_valid($username, $password);

        if ($login_id) {

            $check = $this->supportModel->search('registration', ['id' => $login_id]);

            $this->db->table('user_login_activity')->insert([
                'user_id'    => $login_id,
                'name'       => $check->first . ' ' . $check->last,
                'login_as'   => 'Customer',
                'login_time' => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'session_id' => session_id(),
                'status'     => 'LOGIN',
            ]);

            session()->set('login_activity_id', $this->db->insertID());

            if (! empty($check->last_activity)) {

                $minutes = (time() - strtotime($check->last_activity)) / 60;

                if ($minutes > 30) {

                    $this->supportModel->update(
                        'registration',
                        [
                            'is_login'      => 0,
                            'login_token'   => '',
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

                return redirect()->to($this->loginPage);
            }

            $token = md5(uniqid());

            $this->supportModel->update(
                'registration',
                [
                    'is_login'      => 1,
                    'login_token'   => $token,
                    'last_activity' => date('Y-m-d H:i:s'),
                ],
                $login_id
            );

            session()->set([
                'customer_id'          => $login_id,
                'customer_login_token' => $token,
                'customer_user'        => $check,
                'login_id'             => $login_id,
            ]);

            session()->remove('new');

            return redirect()->to($this->redirectAfterLogin);

        }

        session()->setFlashdata('error', 'Invalid Username/Password');
        session()->setFlashdata('error_class', 'alert-danger');

        return redirect()->to($this->loginPage);
    }

    public function auth()
    {
        return $this->insert();
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to($this->redirectAfterLogout);
    }
}
