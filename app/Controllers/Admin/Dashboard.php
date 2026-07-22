<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Controllers\Admin\Secure;
use App\Models\LoginModel;
use App\Models\SupportModel;
class Dashboard extends Secure
{
    protected $supportModel;
    protected $loginModel;

    public function __construct()
    {
        parent::__construct();

        helper(['url', 'form']);
        $this->supportModel = new SupportModel();
        $this->loginModel = new LoginModel();
        $this->wconfig = $this->supportModel->find('config', 1);

        if (! session()->get('admin_login_id')) {
            return redirect()->to('/admin/login')->send();
        }

    }

    public function index()
    {
        $date = date('Y-m-d');

        if ($this->request->getPost('date')) {
            $selectedDate = $this->request->getPost('date');
            session()->setFlashdata('date', $selectedDate);

            if ($selectedDate === 'All') {
                $condition = $con = '';
                $condition1 = '1=1';
            } elseif ($selectedDate !== 'Custom Range') {
                $condition = $this->condition($selectedDate, 'date');
                $con = $this->condition_lastmonth($selectedDate, 'date');
                $condition1 = str_replace('and', '', $this->condition($selectedDate, 'pickup_date'));
            } else {
                $date1 = $this->request->getPost('date1');
                $date2 = $this->request->getPost('date2');
                $order_date2 = date('Y-m-d', strtotime('+1 days ' . $date2));
                $condition = " and date between  '" . $date1 . "' and '" . $order_date2 . "'";
                $con = " and date between  '" . date('Y-m-d', strtotime('-1 month ' . $date1)) . "' and '" . date('Y-m-d', strtotime('-1 month ' . $order_date2)) . "'";
                $condition1 = "pickup_date between '" . $date1 . "' and '" . $order_date2 . "'";
                session()->setFlashdata('date1', $date1);
                session()->setFlashdata('date2', $date2);
            }
        } else {
            session()->setFlashdata('date', 'Today');
            $date1 = date('Y-m-d');
            $condition = " and date like '%" . $date1 . "%'";
            $con = " and date like '%" . date('Y-m-d', strtotime('-1 month ' . $date1)) . "%'";
            $condition1 = "pickup_date like '%" . $date1 . "%'";
        }

        $condition2 = $condition1 . " and (panel REGEXP '[a-zA-Z]' or panel='')";
        $condition3 = $condition1 . " and panel>0 and panel!=''";
        // echo session()->get('admin_user');
        $permission = session()->get('admin_user');
        //echo '<pre>';print_r($permission);exit;
        $type = $permission->userName;
        $id = $permission->id;
        if (!empty($id)) {
            $user = $this->loginModel->profile($id); 
              // token mismatch => logout
            if ($user->login_token != session()->get('admin_login_token')) {
                session()->destroy();
                return redirect()->to('/admin/login');
            }

            // update activity time
            $data['last_activity'] = date('Y-m-d H:i:s');
            $this->supportModel->update('users', $data, $id);
        }
        
        $control = $this->supportModel->search_col( 'control', 'permission', array('type' => $type) );

        if ($control && !empty($control->permission)) {
            $permissions = array_values(array_filter(array_map(
                'trim',
                explode(',', $control->permission)
            )));
        } else {
            $permissions = [];
        }

        // Secure and the admin layout still read this global in a few places.
        $GLOBALS['permission'] = $permissions;
        // Keep subsequent pages (for example Configuration) on the same
        // role-specific permission set used to render the dashboard sidebar.
        session()->set('admin_permission', $permissions);

        $data = [
            'page_title'  => 'Admin Dashboard',
            'body'        => 'bg-light',
            'data'        => $this->supportModel->find_col('config', 'logo', 1),
            'admin_user'  => session()->get('admin_user'),
            'permission'  => $permissions,
            'condition'   => $condition,
            'con'         => $con,
            'condition2'  => $condition2,
            'condition3'  => $condition3,
            'wallet'      => $this->supportModel->invoice('wallet', 'AMOUNT', 'reason like "%Wallet Recharge%" and status="Confirm"' . $condition),
            'wallet_last' => $this->supportModel->invoice('wallet', 'AMOUNT', 'reason like "%Wallet Recharge%" and status="Confirm"' . $con),
            'deduction'   => $this->supportModel->invoice('wallet', 'AMOUNT', 'amount<0 and status="Confirm"' . $condition),
            'refund'      => $this->supportModel->invoice('wallet', 'AMOUNT', 'amount>0 and reason not like "%Wallet Recharge%" and reason not like "%COD%" and status="Confirm"' . $condition),
            'topay_amt'   => round(
                $this->supportModel->invoice('order_waybills', 'amount', 'status="Complete" and d_mode="CoD" and topay="Yes" and awb_status!="Not Picked"' . $condition)
                + $this->supportModel->invoice('b2c_waybills', 'cod_amount', 'status!="Cancel" and status!="Not Picked" and payment_mode="COD" and topay="Yes"' . $condition)
            ),
            'ftopay_amt'  => round(
                $this->supportModel->invoice('order_waybills', 'amount', 'status="Complete" and d_mode="CoD" and ftopay="Yes" and awb_status!="Not Picked"' . $condition)
                + $this->supportModel->invoice('b2c_waybills', 'cod_amount', 'status!="Cancel" and status!="Not Picked" and payment_mode="COD" and ftopay="Yes"' . $condition)
            ),
            'profit'      => round(
                $this->supportModel->invoice('order_waybills', 'profit', 'status="Complete" and d_mode="CoD" and ftopay="Yes" and awb_status!="Not Picked"' . $condition)
                + $this->supportModel->invoice('b2c_waybills', 'profit', 'status!="Cancel" and status!="Not Picked" and payment_mode="COD" and ftopay="Yes"' . $condition)
            ),
            'sum'         => function ($table, $col, $con) {
                $w1 = $this->supportModel->invoice($table, $col, $con);
                return empty($w1) ? 0 : round($w1, 2);
            },
            'count'       => function ($table, $con) {
                return $this->supportModel->getRows($table, $con);
            },
            'poc'         => function ($id) {
                $p = $this->supportModel->find_col('users', 'userName', $id);
                return $p ? $p->userName : 'NA';
            },
            'user'        => function ($id) {
                $show = $this->supportModel->find_col('registration', 'username,company', $id);
                if (empty($show)) {
                    return 'NA';
                }
                $k = $this->supportModel->search_col('sales_client', 'userid', ['login_id' => $id]);
                $poc = 'NA';
                if ($k) {
                    $p = $this->supportModel->find_col('users', 'userName', $k->userid);
                    if ($p) {
                        $poc = $p->userName;
                    }
                }
                return '<b>' . $show->username . '</b> <br>' . $show->company . '<br><span> POC : ' . $poc . '</span>';
            },
        ];

        $user_type = $this->supportModel->find('customer_type', 2);
        if ($user_type) {
            $data['dead'] = $user_type->dead;
            $data['rare'] = $user_type->rare;
            $data['active'] = $user_type->active;
        } else {
            $data['dead'] = $data['rare'] = $data['active'] = 0;
        }
        
        $data['wconfig'] = $this->wconfig;
       
        // print_r($data['wconfig']);
        // die();
        return view('admin/header', $data)
            . view('admin/dashboard', $data)
            . view('admin/footer');
    }

    public function config()
    {
        $config = $this->supportModel->find('config', 1);

        if (! $config) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Configuration record was not found.'
            );
        }

        $permissions = $this->permission;
        $data = [
            'page_title' => 'Configuration',
            'body'       => 'bg-light',
            'data'       => $config,
            'wconfig'    => $config,
            'admin_user' => session()->get('admin_user'),
            'permission' => $permissions,
        ];

        $GLOBALS['permission'] = $permissions;

        return view('admin/header', $data)
            . view('admin/config_setting', $data)
            . view('admin/footer');
    }

    public function save_config(int $id)
    {
        if (! $this->supportModel->find('config', $id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Configuration record was not found.'
            );
        }

        $post = $this->request->getPost();
        unset($post['submit']);

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->getName() !== '') {
            $rules = [
                'logo' => 'uploaded[logo]|max_size[logo,550]|is_image[logo]|ext_in[logo,jpg,jpeg,png]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', implode(' ', $this->validator->getErrors()))
                    ->with('error_class', 'alert-danger');
            }

            $fileName = $logo->getRandomName();
            $logo->move(ROOTPATH . 'uploads/profile', $fileName);
            $post['logo'] = $fileName;
        }

        if ($this->supportModel->update('config', $post, $id)) {
            return redirect()->to('/admin/dashboard/config')
                ->with('error', 'Successfully Updated.')
                ->with('error_class', 'alert-success');
        }

        return redirect()->to('/admin/dashboard/config')
            ->with('error', 'Failed To Update.')
            ->with('error_class', 'alert-danger');
    }

    public function search()
    {
        $lrn = $this->request->getPost('lrn');

        session()->remove('login_id');
        session()->remove('date1');
        session()->remove('date2');
        session()->set('lrn', $lrn);
        session()->set('apply', 'apply');

        $num = $this->supportModel->getRows('order_waybills', ['lrnum' => $lrn]);

        if ($num > 0) {
            return redirect()->to('/admin/order/');
        }

        return redirect()->to('/admin/orderb2c/');
    }
}
