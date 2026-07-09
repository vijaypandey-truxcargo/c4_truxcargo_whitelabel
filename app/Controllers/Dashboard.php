<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SupportModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $supportModel;
    protected $user;
    protected $wallet;
    protected $plan;
    protected $wconfig;
    protected $current_url;
    protected string $loginRedirect = '/login';

    public function __construct()
    {
        helper(['url', 'form']);

        $this->supportModel = new SupportModel();

        // Login Check
        if (!session()->get('login_id')) {
            redirect()->to($this->loginRedirect)->send();
            exit;
        }

        // Logged in User
        $this->user = $this->supportModel->find(
            'registration',
            session()->get('login_id')
        );

        // Wallet Balance
        $this->wallet = $this->user->wallet ?? 0;

        // Shared layout data used by header/menu/footer views
        $this->plan = $this->user->plan ?? '';
        $this->wconfig = $this->supportModel->find_col('config', 'logo,company,website', 1);

        if (!$this->wconfig || !is_object($this->wconfig)) {
            $this->wconfig = (object) [
                'logo'    => '',
                'company' => '',
                'website' => ''
            ];
        }

        $this->current_url = current_url();
    }

    public function index()
    {
        $session = session();

        // Login Check
        if (! $session->has('login_id')) {
            return redirect()->to($this->loginRedirect);
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Popup Logic
        |--------------------------------------------------------------------------
        */
        if ($session->get('new')) {

            $ss = '22';

        } else {

            $session->remove('new');
            $session->set('new', 'new');

            $date = date('Y-m-d', strtotime($this->user->date));
            $due  = (strtotime(date('Y-m-d')) - strtotime($date)) / 86400;

            $num = $this->supportModel->getRows(
                'buy_plan',
                [
                    'login_id' => $session->get('login_id'),
                    'status'   => 'Active'
                ]
            );

            if ($this->wallet < 0) {

                $ss = 'wallet';

            } elseif ($due < 5) {

                $ss = 'new';

            } elseif (empty($this->user->gst)) {

                $ss = 'gst3';

            } elseif ($num == 0) {

                $ss = 'plan';

            } else {

                $ss = 'ddddd';

            }
        }

        /*
        |--------------------------------------------------------------------------
        | Common Functions
        |--------------------------------------------------------------------------
        */

        $this->upper('Dashboard');
        // $this->job_process();

        if ($this->user->status == 0) {
            return redirect()->to('/dashboard/profile');
        }

        $data = [];

        $data['ss'] = $ss;

        /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

        $data['customer'] = function ($id) {

            $p = $this->supportModel->find_col(
                'address',
                'name,city,state',
                $id
            );

            if (!$p) {
                return '';
            }

            return $p->name . '<br>' . $p->city . ', ' . $p->state;
        };

        /*
        |--------------------------------------------------------------------------
        | Pickup Point
        |--------------------------------------------------------------------------
        */

        $data['point'] = function ($id) {

            $k = $this->supportModel->find_col(
                'pickup',
                'address,city',
                $id
            );

            if (!$k) {
                return '';
            }

            return $k->address . ', ' . $k->city;
        };

        /*
        |--------------------------------------------------------------------------
        | Zone
        |--------------------------------------------------------------------------
        */

        $data['zone'] = function ($id) {
            return 0;
        };

        /*
        |--------------------------------------------------------------------------
        | Divisor
        |--------------------------------------------------------------------------
        */

        $data['divisor'] = function ($panel) use ($session) {

            return $this->divisor(
                $panel,
                $session->get('login_id')
            );

        };

        /*
        |--------------------------------------------------------------------------
        | Latest Orders
        |--------------------------------------------------------------------------
        */

        $data['orders'] = $this->supportModel->show_limit_col(

            'order_waybills',

            'id,lrnum,customer_id,date,awb_status',

            4,

            0,

            'DESC',

            [
                'login_id'   => $session->get('login_id'),
                'status'     => 'Complete',
                'awb_status !=' => 'Not Picked'
            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Latest B2C Orders
        |--------------------------------------------------------------------------
        */

        $data['b2corders'] = $this->supportModel->show_limit_col(

            'b2c_waybills',

            'id,waybill,name,city,state,status,date',

            4,

            0,

            'DESC',

            [
                'login_id' => $session->get('login_id'),
                'status !=' => 'Not Picked'
            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Pickup Request
        |--------------------------------------------------------------------------
        */

        $data['pickup'] = $this->supportModel->select_rows(

            'pickup_request',

            'pickup_id,package_count,pickup_location,pickup_point',

            'DESC',

            [
                'login_id'    => $session->get('login_id'),
                'pickup_date' => date('Y-m-d')
            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Current Month Packages
        |--------------------------------------------------------------------------
        */

        $m = date('m');

        $order_date1 = date('Y') . '-' . $m . '-01';
        $order_date2 = date('Y') . '-' . $m . '-31';

        $condition = "login_id='" . $session->get('login_id') . "'
            AND awb_status!='Not Picked'
            AND date BETWEEN '{$order_date1}' AND '{$order_date2}'
            AND status='Complete'";

        $data['package'] = $this->supportModel->select_rows(

            'order_waybills',

            'id,cweight,panel,weight,customer_id',

            'DESC',

            $condition

        );
            /*
        |--------------------------------------------------------------------------
        | B2B & B2C Count
        |--------------------------------------------------------------------------
        */

        $data['b2b'] = function ($con) use ($session) {

            $condition = "login_id='" . $session->get('login_id') .
                "' AND awb_status!='Not Picked'
                AND status='Complete'
                AND " . $con;

            return $this->supportModel->getRows(
                'order_waybills',
                $condition
            );
        };

        $data['b2c'] = function ($con) use ($session) {

            $condition = "login_id='" . $session->get('login_id') .
                "' AND status!='Not Picked'
                AND " . $con;

            return $this->supportModel->getRows(
                'b2c_waybills',
                $condition
            );
        };

        /*
        |--------------------------------------------------------------------------
        | Not Picked
        |--------------------------------------------------------------------------
        */

        $data['not_picked'] =
            $this->supportModel->getRows(
                'order_waybills',
                [
                    'login_id' => $session->get('login_id'),
                    'awb_status' => 'Not Picked'
                ]
            )
            +
            $this->supportModel->getRows(
                'b2c_waybills',
                [
                    'login_id' => $session->get('login_id'),
                    'status' => 'Not Picked'
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Last Remittance
        |--------------------------------------------------------------------------
        */

        $ldate = date('Y-m-d', strtotime('previous Thursday'));

        $data['last_remittance'] = 0;

        $l1 = $this->supportModel->search_col(
            'remittance',
            'amount',
            [
                'login_id' => $session->get('login_id'),
                'due_date' => $ldate
            ]
        );

        if ($l1) {
            $data['last_remittance'] = $l1->amount;
        }

        /*
        |--------------------------------------------------------------------------
        | Pending COD
        |--------------------------------------------------------------------------
        */

        $start = date('Y-m-d', strtotime('-30 days'));
        $end   = date('Y-m-d', strtotime('-1 days'));

        $start_date = date('d-m-Y', strtotime($start));
        $end_date   = date('d-m-Y', strtotime($end));

        $upcod = 0;

        $condition =
            "login_id=" . $session->get('login_id') . "
            AND status='Delivered'
            AND payment_mode='COD'
            AND topay!='Yes'
            AND STR_TO_DATE(statusDate,'%d-%m-%Y')
            BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y')
            AND STR_TO_DATE('$end_date','%d-%m-%Y')";

        $b2c = $this->supportModel->select_rows(
            'b2c_waybills',
            'waybill,profit,cod_amount,ftopay',
            'ASC',
            $condition
        );

        foreach ($b2c as $row) {

            $num = $this->supportModel->getRows(
                'remittance',
                "waybill LIKE '%{$row->waybill}%'"
            );

            if ($num == 0) {
                $upcod += $row->cod_amount;
            }
        }

        $condition =
            "login_id=" . $session->get('login_id') . "
            AND awb_status='Delivered'
            AND d_mode='CoD'
            AND topay!='Yes'
            AND STR_TO_DATE(statusDate,'%d-%m-%Y')
            BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y')
            AND STR_TO_DATE('$end_date','%d-%m-%Y')";

        $b2b = $this->supportModel->select_rows(
            'order_waybills',
            'lrnum,profit,amount,ftopay',
            'ASC',
            $condition
        );

        foreach ($b2b as $row) {

            $num = $this->supportModel->getRows(
                'remittance',
                "waybill LIKE '%{$row->lrnum}%'"
            );

            if ($num == 0) {
                $upcod += $row->amount;
            }
        }

        $data['upcod'] = $upcod;

        /*
        |--------------------------------------------------------------------------
        | Partner Shipment
        |--------------------------------------------------------------------------
        */

        $data['partner'] = function ($panel) use ($session) {

            $m = date('m');

            $from = date('Y') . "-{$m}-01";
            $to   = date('Y') . "-{$m}-31";

            $con =
                "login_id='" . $session->get('login_id') . "'
                AND date BETWEEN '$from' AND '$to'
                AND status='Complete'
                AND awb_status!='Not Picked'";

            if ($panel == 'delhivery') {

                $con .= " AND panel IN ('Delhivery Lite','Delhivery Cargo','Delhivery Dense')";

            } else {

                $con .= " AND panel LIKE '%{$panel}%'";
            }

            return $this->supportModel->invoice(
                'order_waybills',
                'cweight',
                $con
            );
        };

        /*
        |--------------------------------------------------------------------------
        | Ship Weight
        |--------------------------------------------------------------------------
        */

        $data['ship'] = function ($panel) use ($session) {

            $m = date('m');

            $from = date('Y') . "-{$m}-01";
            $to   = date('Y') . "-{$m}-31";

            $con =
                "login_id='" . $session->get('login_id') . "'
                AND status!='Not Picked'
                AND date BETWEEN '$from' AND '$to'
                AND ship_with IN ($panel)";

            return $this->supportModel->invoice(
                'b2c_waybills',
                'charged_weight',
                $con
            );
        };

        /*
        |--------------------------------------------------------------------------
        | Revenue
        |--------------------------------------------------------------------------
        */

        $data['revenue'] = function ($table, $col, $con) use ($session) {

            $condition =
                "login_id='" . $session->get('login_id') .
                "' AND " . $con;

            return $this->supportModel->invoice(
                $table,
                $col,
                $condition
            );
        };

        /*
        |--------------------------------------------------------------------------
        | NDR
        |--------------------------------------------------------------------------
        */

        $data['ndr'] = function ($con) use ($session) {

            $condition =
                "login_id='" . $session->get('login_id') .
                "' AND " . $con;

            return $this->supportModel->getRows(
                'ndr',
                $condition
            );
        };

        /*
        |--------------------------------------------------------------------------
        | Plan
        |--------------------------------------------------------------------------
        */

        $data['pplan'] = function ($plan) {

            $row = $this->supportModel->search_col(
                'plan',
                'monthly',
                [
                    'title' => $plan
                ]
            );

            return $row ? $row->monthly : 0;
        };
        $data['user'] = $this->user;
        //$data['wconfig'] = $this->wconfig;
        $data['plan'] = $this->plan;
        // $data['current_url']  = trim(str_replace('index.php/','',current_url())); 

         return view('dashboard', $data)
            . view('footer', $data);
       
    }

    public function upper($title)
    {
        $bank = $this->supportModel->getRows(
            'bank',
            [
                'login_id' => session()->get('login_id')
            ]
        );

        $data = [
            'page_title'   => $title,
            'body'         => '',
            'user'         => $this->user,
            'wallet'       => $this->wallet,
            'bank'         => $bank,
            'wconfig'      => $this->wconfig,
            'plan'         => $this->plan,
            'current_url'  => trim(str_replace('index.php/','',current_url()))
        ];

        echo view('head', $data);
        echo view('menu', $data);
        echo view('header', $data);
    }
}
