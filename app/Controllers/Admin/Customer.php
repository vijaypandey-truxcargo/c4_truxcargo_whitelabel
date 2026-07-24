<?php

namespace App\Controllers\Admin;

use App\Models\SupportModel;
use App\Models\UserModel;
use Config\Database;

class Customer extends Secure
{
    protected UserModel $userModel;
    protected $db;
    protected string $accesstoken = '';
    protected array $registrationColumns = [];

    public function __construct()
    {
        parent::__construct();

        helper(['url', 'form', 'file', 'download']);

        $this->supportModel = new SupportModel();
        $this->userModel = new UserModel();
        $this->db = Database::connect();
        $this->wconfig = $this->supportModel->find('config', 1);
        $this->accesstoken = $this->wconfig->kyc ?? '';
    }

    public function all($count = 0)
    {
        if (! $this->can('View Customer')) {
            return redirect()->to('/admin/dashboard');
        }

        $session = session();
        if ($count === 'all') {
            $count = 0;
            $session->remove('apply');
        }

        $condition = '1=1';

        if ($this->request->getPost('apply') || $session->get('apply')) {
            $session->set('apply', 'apply');

            $search = trim((string) $this->request->getPost('search'));
            if ($search !== '') {
                $searchArr = preg_split('/\s+/', $search);
                if (is_numeric($search)) {
                    $condition .= " AND (phone LIKE '%" . $search . "%' OR aadhar = '" . $search . "' OR code = '" . $search . "')";
                } elseif (count($searchArr) >= 2) {
                    $firstName = trim($searchArr[0]);
                    $lastName = trim($searchArr[1]);
                    $condition .= " AND ((first LIKE '%" . $firstName . "%' AND last LIKE '%" . $lastName . "%') OR username LIKE '%" . $search . "%' OR code = '" . $search . "' OR gst = '" . $search . "')";
                } else {
                    $condition .= " AND (first LIKE '%" . $search . "%' OR last LIKE '%" . $search . "%' OR username LIKE '%" . $search . "%' OR code = '" . $search . "' OR gst = '" . $search . "')";
                }
            }

            $wallet = $this->postedOrSession('wallet');
            if (! empty($wallet) && $wallet !== 'All') {
                $condition .= $wallet === 'Capping' ? ' and capping>0' : " and wallet like '%" . $wallet . "%' ";
            }

            $codPlan = $this->postedOrSession('cod_plan');
            if (! empty($codPlan) && $codPlan !== 'All') {
                $condition .= " and cod_plan like '%" . $codPlan . "%' ";
            }

            $hasBtype = $this->registrationHasColumn('btype');
            $btype = $this->postedOrSession('btype');
            if ($hasBtype) {
                if (! empty($btype) && $btype !== 'All') {
                    $condition .= " and btype like '%" . $btype . "%' ";
                }
            } else {
                $session->remove('btype');
            }

            $hasMorder = $this->registrationHasColumn('morder');
            $morder = $this->postedOrSession('morder');
            if ($hasMorder) {
                if (! empty($morder) && $morder !== 'All') {
                    $condition .= " and morder like '%" . $morder . "%' ";
                }
            } else {
                $session->remove('morder');
            }

            $panelCount = $this->postedOrSession('panel_count');
            if (! empty($panelCount) && $panelCount !== 'All') {
                $condition .= " and panel_count like '%" . $panelCount . "%' ";
            }

            $type = $this->postedOrSession('type');
            if (! empty($type) && $type !== 'All') {
                $condition .= " and type='" . $type . "' ";
            }

            $gstpre = $this->postedOrSession('gstpre');
            if (! empty($gstpre) && $gstpre !== 'All') {
                $condition .= " and gstpre='" . $gstpre . "' ";
            }

            $hub = $this->postedOrSession('hub');
            if (! empty($hub) && $hub !== 'All') {
                $condition .= " and hub like '%" . $hub . "%' ";
            }

            $status = $this->request->getPost('status') !== null
                ? $this->request->getPost('status')
                : $session->get('status');
            if ($this->request->getPost('status') !== null) {
                $session->set('status', $status);
            }
            if ($status !== null && $status !== 'All') {
                $condition .= " AND status = '" . $status . "' ";
            }

            $date = $this->postedOrSession('date');
            if (! empty($date)) {
                if ($date === 'Custom Range') {
                    if ($this->request->getPost('date1')) {
                        $orderDate1 = $this->request->getPost('date1');
                        $orderDate2 = $this->request->getPost('date2');
                        $session->set('date1', $orderDate1);
                        $session->set('date2', $orderDate2);
                    } else {
                        $orderDate1 = $session->get('date1');
                        $orderDate2 = $session->get('date2');
                    }

                    if ($orderDate1 && $orderDate2) {
                        $condition .= " and date between  '" . $orderDate1 . "' and '" . $orderDate2 . "'";
                    }
                } else {
                    $condition .= $this->condition($date, 'date');
                    $session->remove(['date1', 'date2']);
                }
            }
        } else {
            $session->remove(['date', 'type', 'gstpre', 'wallet', 'cod_plan', 'btype', 'morder', 'panel_count', 'hub', 'status', 'date1', 'date2']);
        }

        $perPage = 10;
        [$currentPage, $offset] = $this->pageState($count, $perPage);
        $total = $this->supportModel->getRows('registration', $condition);

        $data = [
            'page_title' => 'All Customer',
            'condition' => $condition,
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $total, 'admin_full'),
            'code' => $this->supportModel->show_limit_col('registration', 'id,username,code,first,last,wallet,gst,date,company,panel,phone,gstpre,cod_plan,status,type', $perPage, $offset, 'ASC', $condition),
            'plan' => $this->supportModel->select_rows('cod_plan', 'plan', 'ASC'),
            'hubList' => $this->supportModel->show('hub'),
            'company' => $this->supportModel->show('company'),
            'hasBtype' => $this->registrationHasColumn('btype'),
            'hasMorder' => $this->registrationHasColumn('morder'),
            'poc' => function ($id) {
                $row = $this->supportModel->search_col('sales_client', 'userid', ['login_id' => $id]);
                if (! $row) {
                    return 'NA';
                }

                $user = $this->supportModel->find_col('users', 'userName', $row->userid);
                return $user ? $user->userName : 'NA';
            },
        ];

        return $this->render('admin/customers', $data);
    }

    public function active_user(string $type = 'active')
    {
        if (! $this->can('View Customer')) {
            return redirect()->to('/admin/dashboard');
        }

        $type = strtolower($type);
        if (! in_array($type, ['active', 'rare', 'dead'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Customer type was not found.');
        }

        $perPage = 10;
        $currentPage = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($currentPage - 1) * $perPage;
        $total = $this->customerActivityCount($type);

        if ($total > 0 && $offset >= $total) {
            $currentPage = (int) ceil($total / $perPage);
            $offset = ($currentPage - 1) * $perPage;
        }

        $users = $this->customerActivityRows($type, $perPage, $offset);
        $profiles = $this->customerProfilesById(array_column($users, 'login_id'));
        $activityByUser = [];

        foreach ($users as $index => $row) {
            $loginId = (int) $row['login_id'];
            $users[$index]['login_id'] = $loginId;
            $users[$index]['b2b'] = (int) ($row['b2b'] ?? 0);
            $users[$index]['b2c'] = (int) ($row['b2c'] ?? 0);
            $users[$index]['current_b2b'] = (int) ($row['current_b2b'] ?? 0);
            $users[$index]['current_b2c'] = (int) ($row['current_b2c'] ?? 0);
            $activityByUser[$loginId] = $users[$index];
        }

        $data = [
            'page_title' => ucfirst($type) . ' Customers',
            'type' => $type,
            'user' => $users,
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $total, 'admin_full'),
            'info' => static fn ($id) => $profiles[(int) $id] ?? (object) [
                'date' => '',
                'username' => '',
                'first' => '',
                'last' => '',
                'company' => '',
                'email' => '',
                'phone' => '',
            ],
            'cb2b' => static fn ($id) => $activityByUser[(int) $id]['current_b2b'] ?? 0,
            'cb2c' => static fn ($id) => $activityByUser[(int) $id]['current_b2c'] ?? 0,
        ];

        return $this->render('admin/active_user', $data);
    }

    public function edit_customer($id)
    {
        $data = [
            'page_title' => 'Edit Customer',
            'key' => $this->supportModel->show('b2b-partner', 'ASC'),
            'data' => $this->supportModel->find('registration', $id),
            'users' => $this->supportModel->show('users'),
            'state' => $this->supportModel->distinct_rows('state_code', 'state', 'Asc'),
            'hub' => $this->supportModel->show_condition('hub', 'ASC', ['status' => 1]),
            'company' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1]),
            'escalation' => $this->supportModel->show_condition('escalation_matrix', 'DESC', ['customer_id' => $id]),
        ];

        if (! $data['data']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Customer was not found.');
        }

        return $this->render('admin/edit_customer', $data);
    }

    public function update_customer($id)
    {
        $post = $this->request->getPost();
        unset($post['submit']);

        if (! empty($post['phone']) && ! preg_match('/^[0-9]{10}$/', $post['phone'])) {
            return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Invalid Phone Number, must be 10 digits.', 'alert-danger');
        }

        if (! empty($post['email']) && ! filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Invalid Email Format', 'alert-danger');
        }

        $oldData = $this->supportModel->find('registration', $id);
        if (! $oldData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Customer was not found.');
        }

        if (($post['phone'] ?? '') !== ($oldData->phone ?? '')) {
            $exists = $this->supportModel->getRows('registration', "phone = '" . ($post['phone'] ?? '') . "' AND id != '" . $id . "'");
            if ($exists) {
                return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Phone Number already exists!', 'alert-danger');
            }
        }

        if (($post['email'] ?? '') !== ($oldData->email ?? '')) {
            $exists = $this->supportModel->getRows('registration', "email = '" . ($post['email'] ?? '') . "' AND id != '" . $id . "'");
            if ($exists) {
                return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Email already exists!', 'alert-danger');
            }
        }

        if (($post['username'] ?? '') !== ($oldData->username ?? '')) {
            $exists = $this->supportModel->getRows('registration', "username = '" . ($post['username'] ?? '') . "' AND id != '" . $id . "'");
            if ($exists) {
                return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Username already exists!', 'alert-danger');
            }
        }

        $validationError = $this->validateEscalationRows($post, true);
        if ($validationError !== null) {
            return $this->redirectWith("/admin/customer/edit_customer/{$id}", $validationError, 'alert-danger');
        }

        [$image, $imageError] = $this->uploadFile('image', 'uploads/profile', ['jpg', 'jpeg', 'png', 'pdf'], 5450);
        if ($imageError !== null) {
            return $this->redirectWith("/admin/customer/edit_customer/{$id}", $imageError, 'alert-danger');
        }
        if ($image !== null) {
            $post['image'] = $image;
        }

        [$agreement, $agreementError] = $this->uploadFile('agreement', 'uploads/profile', ['jpg', 'jpeg', 'png', 'pdf'], 5450);
        if ($agreementError !== null) {
            return $this->redirectWith("/admin/customer/edit_customer/{$id}", $agreementError, 'alert-danger');
        }
        if ($agreement !== null) {
            $post['agreement'] = $agreement;
        }

        $data = [];
        foreach ($post as $key => $value) {
            if (! is_array($value)) {
                $data[$key] = $value !== '' && $value !== null ? $value : '';
            }
        }

        $this->supportModel->update('registration', $data, $id);
        $this->supportModel->delete_condition('escalation_matrix', ['customer_id' => $id]);
        $this->saveEscalationRows((int) $id, $post);

        return $this->redirectWith("/admin/customer/edit_customer/{$id}", 'Successfully Updated.', 'alert-success');
    }

    public function delete()
    {
        $this->supportModel->delete('registration', $this->request->getPost('id'));

        return $this->redirectWith('/admin/customer/all/', 'Successfully Delete.', 'alert-success');
    }

    public function bank($count = 0)
    {
        if (! $this->can('View Customer')) {
            return redirect()->to('/admin/dashboard');
        }

        if ($count === 'all') {
            $count = 0;
        }

        $loginId = $this->request->getPost('login_id');
        $condition = (! empty($loginId) && $loginId !== 'All') ? ['login_id' => $loginId] : '1=1';

        $perPage = 10;
        [$currentPage, $offset] = $this->pageState($count, $perPage);
        $total = $this->supportModel->getRows('bank', $condition);

        $data = [
            'page_title' => 'All Customer Bank Info',
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $total, 'admin_full'),
            'code' => $this->supportModel->show_limit('bank', $perPage, $offset, 'ASC', $condition),
            'customer' => $this->supportModel->select_rows('registration', 'id,first,last,username', 'ASC'),
            'user' => function ($id) {
                $rows = $this->supportModel->select_rows('registration', 'first,last,username', 'ASC', ['id' => $id]);
                $row = $rows[0] ?? null;
                return $row ? '<b>' . $row->username . '</b><br>' . $row->first . ' ' . $row->last : 'NA';
            },
        ];

        return $this->render('admin/bank', $data);
    }

    public function edit_bank($id)
    {
        $data = $this->supportModel->find('bank', $id);
        if (! $data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Bank record was not found.');
        }

        $user = $this->supportModel->find_col('registration', 'username', $data->login_id);
        $data->username = $user->username ?? '';

        return $this->render('admin/edit_bank', [
            'page_title' => 'Edit Bank',
            'data' => $data,
        ]);
    }

    public function update_bank($id)
    {
        $post = $this->request->getPost();
        unset($post['submit'], $post['username']);

        [$cheque, $uploadError] = $this->uploadFile('image', 'uploads/bank', ['jpg', 'jpeg', 'png'], 100);
        if ($uploadError !== null) {
            return $this->redirectWith("/admin/customer/edit_bank/{$id}", $uploadError, 'alert-danger');
        }
        if ($cheque !== null) {
            $post['cheque'] = $cheque;
        }

        if ($this->supportModel->update('bank', $post, $id)) {
            return $this->redirectWith('/admin/customer/bank/all', 'Successfully Updated.', 'alert-success');
        }

        return $this->redirectWith("/admin/customer/edit_bank/{$id}", 'Failed To Update.', 'alert-danger');
    }

    public function bank_verify()
    {
        $acc = $this->request->getPost('account');
        $ifsc = $this->request->getPost('ifsc');

        if (empty($acc) || empty($ifsc)) {
            return $this->response->setJSON(3);
        }

        $url = 'https://kyc-api.surepass.io/api/v1/bank-verification/';
        $payload = json_encode([
            'id_number' => $acc,
            'ifsc' => $ifsc,
            'ifsc_details' => true,
        ]);

        $output = $this->curl_post($url, $this->accesstoken, $payload ?: '');
        if (! empty($output['success'])) {
            return $this->response->setJSON([
                'bank' => $output['data']['ifsc_details']['bank_name'] ?? '',
                'holder' => $output['data']['full_name'] ?? '',
            ]);
        }

        return $this->response->setJSON(2);
    }

    public function wallet($count = 0)
    {
        if (! $this->can('View Customer')) {
            return redirect()->to('/admin/dashboard');
        }

        if ($count === 'all') {
            $count = 0;
            session()->remove('apply');
        }

        $loginId = $this->request->getPost('login_id');
        if ($loginId !== null) {
            session()->set('login_id', $loginId);
        }

        $condition = (! empty($loginId) && $loginId !== 'All') ? "id='" . $loginId . "'" : '1=1';

        $perPage = 10;
        [$currentPage, $offset] = $this->pageState($count, $perPage);
        $total = $this->supportModel->getRows('registration', $condition);

        $data = [
            'page_title' => 'All Customer Wallet',
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $total, 'admin_full'),
            'code' => $this->supportModel->show_limit_col('registration', 'first,last,company,username,panel,id', $perPage, $offset, 'ASC', $condition),
            'key' => $this->supportModel->select_rows('registration', 'id,first,last,username', 'ASC'),
            'recharge' => fn ($id) => $this->supportModel->wallet("login_id= '" . $id . "' and reason like '%Wallet Recharge%' and status!='Cancelled'"),
            'deduction' => fn ($id) => $this->supportModel->wallet("login_id= '" . $id . "' and amount<0 and status!='Cancelled'"),
            'negative' => fn ($id) => $this->supportModel->wallet("login_id= '" . $id . "' and amount<0 and status!='Cancelled' and date between '" . date('Y-m-d', strtotime('-10 days')) . "' and '" . date('Y-m-d', strtotime('+1 days')) . "'"),
            'refund' => fn ($id) => $this->supportModel->wallet("login_id= '" . $id . "' and amount>0 and reason not like '%Wallet Recharge%' and status!='Cancelled'"),
            'total' => fn ($id) => $this->supportModel->wallet("login_id= '" . $id . "' and status!='Cancelled'"),
        ];

        return $this->render('admin/customers_wallet', $data);
    }

    public function subscription($count = 0)
    {
        if ($count === 'all') {
            $count = 0;
            session()->remove('apply');
        }

        $condition = '1=1';
        $cond = '1=1';
        $con = '1=1';

        if ($this->request->getPost('apply') || session()->get('apply')) {
            session()->set('apply', 'apply');

            $loginId = $this->postedOrSession('login_id');
            if (! empty($loginId) && $loginId !== 'All') {
                $condition .= " and login_id='" . $loginId . "'";
                $cond .= " and id='" . $loginId . "'";
            }

            $plan = $this->postedOrSession('plan');
            if (! empty($plan) && $plan !== 'All') {
                $condition .= " and plan like '%" . $plan . "%' ";
                $cond .= " and plan like '%" . $plan . "%' ";
            }

            $status = $this->postedOrSession('status');
            if (! empty($status) && $status !== 'All') {
                $condition .= " and status='" . $status . "'";
            }

            $date = $this->postedOrSession('date');
            if (! empty($date)) {
                if ($date === 'Custom Range') {
                    if ($this->request->getPost('date1')) {
                        $orderDate1 = $this->request->getPost('date1');
                        $orderDate2 = $this->request->getPost('date2');
                        session()->set('date1', $orderDate1);
                        session()->set('date2', $orderDate2);
                    } else {
                        $orderDate1 = session()->get('date1');
                        $orderDate2 = session()->get('date2');
                    }

                    if ($orderDate1 && $orderDate2) {
                        $condition .= " and date between  '" . $orderDate1 . "' and '" . $orderDate2 . "'";
                        $con .= " and date between  '" . $orderDate1 . "' and '" . $orderDate2 . "'";
                    }
                } else {
                    $condition .= $this->condition($date, 'date');
                    $con .= $this->condition($date, 'date');
                    session()->remove(['date1', 'date2']);
                }
            }
        } else {
            session()->remove(['date', 'plan', 'login_id', 'status']);
            $date1 = date('Y-m-d', strtotime('+1 days'));
            $date2 = date('Y-m-d', strtotime('-29 days'));
            $cond = "date between '" . $date2 . "' and '" . $date1 . "'";
        }

        $perPage = 10;
        [$currentPage, $offset] = $this->pageState($count, $perPage);
        $totalRows = $this->supportModel->getRows('buy_plan', $condition);

        $data = [
            'page_title' => 'Plan subscription',
            'condition' => $condition,
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $totalRows, 'admin_full'),
            'code' => $this->supportModel->show_limit('buy_plan', $perPage, $offset, 'DESC', $condition),
            'key' => $this->supportModel->select_rows('registration', 'id,first,last,username', 'ASC'),
            'user' => function ($id) {
                $row = $this->supportModel->find_col('registration', 'first,last,username', $id);
                return $row ? '<b>' . $row->username . '</b><br>' . $row->first . ' ' . $row->last : 'NA';
            },
            'poc' => function ($id) {
                $row = $this->supportModel->search_col('sales_client', 'userid', ['login_id' => $id]);
                if (! $row) {
                    return 'NA';
                }

                $user = $this->supportModel->find_col('users', 'userName', $row->userid);
                return $user ? $user->userName : 'NA';
            },
            'price' => function ($plan, $day, $coupon, $id, $date = null) {
                $customer = $this->supportModel->find_col('registration', 'gstpercentage', $id);
                $table = date('Ymd', strtotime($date ?? date('Y-m-d'))) < 20250317 ? 'plan1' : 'plan';
                $row = $this->supportModel->search_col($table, $day, ['title' => $plan]);
                $amount = $row->{$day} ?? 0;
                $amount += $amount * (($customer->gstpercentage ?? 0) / 100);
                $percent = (int) filter_var((string) $coupon, FILTER_SANITIZE_NUMBER_INT);
                if (! empty($coupon) && str_contains($coupon, 'TRUX') && str_contains($coupon, '%') && is_numeric($percent)) {
                    $amount -= $amount * $percent / 100;
                }

                return round($amount, 2);
            },
        ];

        $amount = 0;
        $s1 = $s2 = $s3 = $sa1 = $sa2 = $sa3 = $rst = $rsme = $rent = 0;
        $plans = $this->supportModel->show_condition('buy_plan', 'DESC', $condition);
        foreach ($plans as $planRow) {
            $day = $this->durationColumn($planRow->date, $planRow->edate);
            $planAmount = $data['price']($planRow->plan, $day, $planRow->coupon, $planRow->login_id, $planRow->date);
            $amount += $planAmount;
            $revenue = 0;
            if ($planRow->status === 'Active') {
                $revenue = $this->supportModel->invoice('order_waybills', 'lastpay', "login_id='" . $planRow->login_id . "' and status='Complete' and awb_status!='Not Picked' and date between  '" . $planRow->date . "' and '" . $planRow->edate . "'");
            }

            if ($planRow->plan === 'Startup') {
                $s1 += $planAmount;
                if ($planRow->status === 'Active') {
                    $sa1 += $planAmount;
                    $rst += $revenue;
                }
            } elseif ($planRow->plan === 'Enterprise') {
                $s3 += $planAmount;
                if ($planRow->status === 'Active') {
                    $sa3 += $planAmount;
                    $rent += $revenue;
                }
            } else {
                $s2 += $planAmount;
                if ($planRow->status === 'Active') {
                    $sa2 += $planAmount;
                    $rsme += $revenue;
                }
            }
        }

        $activePlans = $this->supportModel->select_rows('registration', 'id,plan', 'DESC', $cond . " and plan!=''");
        foreach ($activePlans as $activePlan) {
            $revenue = $this->supportModel->invoice('order_waybills', 'lastpay', $con . " and login_id='" . $activePlan->id . "' and status='Complete' and awb_status!='Not Picked'");
            if ($activePlan->plan === 'Startup') {
                $rst += $revenue;
            } elseif ($activePlan->plan === 'Enterprise') {
                $rent += $revenue;
            } else {
                $rsme += $revenue;
            }
        }

        $data += [
            'total_user' => $this->supportModel->getRows('registration', $cond),
            'order_user' => $this->supportModel->distinct_getRows('order_waybills', 'login_id', $con),
            'startup' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Startup'"),
            'sme' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Small Business'"),
            'enterprise' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Enterprise'"),
            'revenue' => $this->supportModel->invoice('order_waybills', 'lastpay', $con . " and status='Complete' and awb_status!='Not Picked'"),
            'startup_rev' => $rst,
            'sme_rev' => $rsme,
            'enterprise_rev' => $rent,
            'startup_active' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Startup' and status='Active'") + $this->supportModel->getRows('registration', $cond . " and plan='Startup'"),
            'sme_active' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Small Business' and status='Active'") + $this->supportModel->getRows('registration', $cond . " and plan='Small Business'"),
            'enterprise_active' => $this->supportModel->getRows('buy_plan', $condition . " and plan='Enterprise' and status='Active'") + $this->supportModel->getRows('registration', $cond . " and plan='Enterprise'"),
            'total' => $amount,
            's1' => $s1,
            's2' => $s2,
            's3' => $s3,
            'sa1' => $sa1,
            'sa2' => $sa2,
            'sa3' => $sa3,
        ];

        return $this->render('admin/subscription', $data);
    }

    public function report()
    {
        $rows = $this->supportModel->show('registration', 'DESC');
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['#', 'Joining Date', 'Username', 'Name', 'Email', 'Phone', 'Customer Code', 'City', 'State', 'Address', 'Pincode', 'User Status']);

        $i = 1;
        foreach ($rows as $row) {
            fputcsv($handle, [
                $i++,
                $row->date ?? '',
                $row->username ?? '',
                str_replace(',', ' ', $row->first ?? '') . ' ' . str_replace(',', ' ', $row->last ?? ''),
                $row->email ?? '',
                $row->phone ?? '',
                $row->code ?? '',
                $row->city ?? '',
                $row->state ?? '',
                $row->address ?? '',
                $row->pincode ?? '',
                ($row->status == 1 ? 'Active' : 'Inactive'),
            ]);
        }

        return $this->csvResponse($handle, 'Customers_' . date('d-m-y') . '.csv');
    }

    public function report_wallet()
    {
        $table = $this->request->getPost('table') ?: 'registration';
        $rows = $this->supportModel->show_condition($table, 'ASC', '1=1');

        $html = '<table class="table table-bordered" id="report"><tr><th> Username</th><th> Name</th><th> Company Name</th><th>Recharge</th><th> Deduction</th><th> Refund</th><th>Balance</th></tr>';
        foreach ($rows as $row) {
            $recharge = $this->supportModel->wallet("login_id= '" . $row->id . "' and reason like '%Wallet Recharge%' and status!='Cancelled'");
            $deduction = $this->supportModel->wallet("login_id= '" . $row->id . "' and amount<0 and status!='Cancelled'");
            $refund = $this->supportModel->wallet("login_id= '" . $row->id . "' and amount>0 and reason not like '%Wallet Recharge%' and status!='Cancelled'");
            $balance = $this->supportModel->wallet("login_id= '" . $row->id . "' and status!='Cancelled'");
            $html .= '<tr><td>' . ($row->username ?? '') . '</td><td>' . ($row->first ?? '') . ' ' . ($row->last ?? '') . '</td><td>' . ($row->company ?? '') . '</td><td>' . round($recharge, 2) . '</td><td>' . round($deduction, 2) . '</td><td>' . round($refund, 2) . '</td><td>' . round($balance, 2) . '</td></tr>';
        }
        $html .= '</table>';

        ob_start();
        $this->csv('customer_wallet');
        $html .= ob_get_clean();

        return $this->response->setBody($html);
    }

    public function bank_report()
    {
        $rows = $this->supportModel->show_condition('bank', 'ASC', '1=1');
        $html = '<table class="table table-bordered" id="report"><tr><th> UserID</th><th> Company</th><th>Bank Holder Name</th><th> Bank Name</th><th> Account No</th><th>IFSC</th></tr>';
        foreach ($rows as $row) {
            $user = $this->supportModel->find_col('registration', 'username,company', $row->login_id);
            if (! $user) {
                continue;
            }

            $html .= '<tr><td>' . $user->username . '</td><td>' . str_replace(',', ' ', $user->company) . '</td><td>' . str_replace(',', ' ', $row->holder_name) . '</td><td>' . str_replace(',', ' ', $row->bname) . '</td><td> Acc/ ' . $row->account . '</td><td>' . $row->ifsc . '</td></tr>';
        }
        $html .= '</table>';

        ob_start();
        $this->csv('bank');
        $html .= ob_get_clean();

        return $this->response->setBody($html);
    }

    public function subscription_report()
    {
        $condition = $this->request->getPost('condition') ?: '1=1';
        $rows = $this->supportModel->show_condition('buy_plan', 'ASC', $condition);

        $html = '<table class="table table-bordered" id="report"><tr><th>USERID</th><th>Plan</th><th>Plan Duration</th><th>Plan Amt</th><th>Purchase Date</th><th>Expire Date</th><th>Coupon</th><th>Status</th><th>POC</th></tr>';
        foreach ($rows as $row) {
            $customer = $this->supportModel->find_col('registration', 'username,gstpercentage', $row->login_id);
            if (! $customer) {
                continue;
            }

            $duration = $this->durationLabel($row->date, $row->edate);
            $day = $this->durationColumn($row->date, $row->edate);
            $table = date('Ymd', strtotime($row->date)) < 20250317 ? 'plan1' : 'plan';
            $plan = $this->supportModel->search_col($table, $day, ['title' => $row->plan]);
            $amount = $plan->{$day} ?? 0;
            $amount += $amount * (($customer->gstpercentage ?? 0) / 100);
            $percent = (int) filter_var((string) $row->coupon, FILTER_SANITIZE_NUMBER_INT);
            $total = (! empty($row->coupon) && str_contains($row->coupon, 'TRUX') && str_contains($row->coupon, '%') && is_numeric($percent))
                ? $amount - ($amount * $percent / 100)
                : $amount;

            $sales = $this->supportModel->search_col('sales_client', 'userid', ['login_id' => $row->login_id]);
            $poc = 'NA';
            if ($sales) {
                $pocRow = $this->supportModel->find_col('users', 'userName', $sales->userid);
                $poc = $pocRow->userName ?? 'NA';
            }

            $html .= '<tr><td>' . $customer->username . '</td><td>' . $row->plan . '</td><td>' . $duration . '</td><td>' . $total . '</td><td>' . date('d-M-Y', strtotime($row->date)) . '</td><td>' . date('d-M-Y', strtotime($row->edate)) . '</td><td>' . $row->coupon . '</td><td>' . $row->status . '</td><td>' . $poc . '</td></tr>';
        }
        $html .= '</table>';

        ob_start();
        $this->csv('subscription');
        $html .= ob_get_clean();

        return $this->response->setBody($html);
    }

    public function bulk_client()
    {
        return $this->render('admin/bulk_client', ['page_title' => 'bulk_client']);
    }

    public function upload_client()
    {
        $file = $this->request->getFile('file');
        $msg = '';
        $msg1 = '';

        if (! $file || ! $file->isValid() || strtolower($file->getClientExtension()) !== 'csv') {
            return $this->redirectWith('/admin/customer/bulk_client', 'Invalid file, please select only CSV file.', 'alert-danger');
        }

        $insertCount = 0;
        $rowCount = 0;
        $missingUser = [];
        $handle = fopen($file->getTempName(), 'r');

        if ($handle) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($rowCount !== 0 && ! empty($row)) {
                    $csEmail = trim($row[11] ?? '');
                    $csPerson = $this->supportModel->search('users', ['userEmail' => $csEmail]);
                    if (! empty($csPerson)) {
                        $csPersonId = is_array($csPerson) ? ($csPerson[0]->id ?? '') : ($csPerson->id ?? '');
                    } else {
                        $missingUser[] = $csEmail;
                        $rowCount++;
                        continue;
                    }

                    if (empty($row[6])) {
                        $num = $this->supportModel->getRows('registration', "phone like '%" . ($row[2] ?? '') . "%' or email='" . ($row[3] ?? '') . "' or company='" . ($row[4] ?? '') . "'");
                    } else {
                        $num = $this->supportModel->getRows('registration', "phone like '%" . ($row[2] ?? '') . "%' or email='" . ($row[3] ?? '') . "' or company='" . ($row[4] ?? '') . "' or gst='" . ($row[6] ?? '') . "'");
                    }

                    if ((int) $num === 0) {
                        $post = [
                            'first' => $row[0] ?? '',
                            'last' => $row[1] ?? '',
                            'phone' => $row[2] ?? '',
                            'email' => $row[3] ?? '',
                            'company' => $row[4] ?? '',
                            'kyc' => $row[5] ?? '',
                            'gst' => $row[6] ?? '',
                            'address' => $row[7] ?? '',
                            'city' => $row[8] ?? '',
                            'state' => $row[9] ?? '',
                            'pincode' => $row[10] ?? '',
                            'password' => $this->rand_string(8),
                            'date' => date('Y-m-d H:i:s'),
                            'status' => 1,
                            'username' => ($this->wconfig->uprefix ?? '') . ((int) $this->userModel->username() + 1),
                            'cs_person' => $csPersonId,
                            'code' => $this->nextCustomerCode(),
                        ];

                        if ($this->userModel->insertUser($post)) {
                            $insertCount++;
                        } else {
                            $msg .= '<h6 class="text-center mb-3" style="color:red">' . ($row[0] ?? '') . ' - <b>Failed to Add</b></h6>';
                        }
                    } else {
                        $msg .= '<h6 class="text-center mb-3" style="color:red">' . ($row[0] ?? '') . ' - <b>Duplicate Entry Check phone/email/company/gst</b></h6>';
                    }

                    $notAddCount = max(0, $rowCount - $insertCount);
                    $msg1 = 'Imported successfully. Total Orders (' . $rowCount . ') | Inserted (' . $insertCount . ') | Not Inserted (' . $notAddCount . ')';
                }

                $rowCount++;
            }
            fclose($handle);

            if (! empty($missingUser)) {
                $msg .= '<br><b>CS Person Email Not Found:</b> ' . implode(', ', array_unique($missingUser));
            }
        } else {
            $msg1 = '<p class="red">Error on file upload, please try again.</p>';
        }

        return $this->redirectWith('/admin/customer/bulk_client', $msg . '<br>' . $msg1, 'alert-success');
    }

    public function rand_string($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function create()
    {
        return $this->render('admin/add_customer', [
            'page_title' => 'Add Customer',
            'key' => $this->supportModel->show('b2b-partner', 'ASC'),
            'state' => $this->supportModel->distinct_rows('state_code', 'state', 'Asc'),
            'hub' => $this->supportModel->show('hub'),
            'users' => $this->supportModel->show('users'),
            'company' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1]),
        ]);
    }

    public function insert()
    {
        $post = $this->request->getPost();
        unset($post['submit']);

        if (! empty($post['phone']) && ! preg_match('/^[0-9]{10}$/', $post['phone'])) {
            return $this->redirectWith('/admin/customer/create', 'Invalid Phone Number, it must be 10 digits.', 'alert-danger');
        }

        if (! empty($post['email']) && ! filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->redirectWith('/admin/customer/create', 'Invalid Email Format', 'alert-danger');
        }

        $duplicateCondition = "phone = '" . ($post['phone'] ?? '') . "' OR email = '" . ($post['email'] ?? '') . "' OR username = '" . ($post['username'] ?? '') . "'";
        if (! empty($post['code'])) {
            $duplicateCondition .= " OR code = '" . $post['code'] . "'";
        }

        if ($this->supportModel->getRows('registration', $duplicateCondition)) {
            return $this->redirectWith('/admin/customer/create', 'User Name, Phone Number, Customer Code or Email already exists!', 'alert-danger');
        }

        $validationError = $this->validateEscalationRows($post, false);
        if ($validationError !== null) {
            return $this->redirectWith('/admin/customer/create', $validationError, 'alert-danger');
        }

        [$image, $imageError] = $this->uploadFile('image', 'uploads/profile', ['jpg', 'jpeg', 'png', 'pdf'], 5450);
        if ($imageError !== null) {
            return $this->redirectWith('/admin/customer/create', $imageError, 'alert-danger');
        }
        if ($image !== null) {
            $post['image'] = $image;
        }

        [$agreement, $agreementError] = $this->uploadFile('agreement', 'uploads/profile', ['jpg', 'jpeg', 'png', 'pdf'], 5450);
        if ($agreementError !== null) {
            return $this->redirectWith('/admin/customer/create', $agreementError, 'alert-danger');
        }
        if ($agreement !== null) {
            $post['agreement'] = $agreement;
        }

        $data = [];
        foreach ($post as $key => $value) {
            if (! is_array($value)) {
                $data[$key] = ! empty($value) ? $value : '';
            }
        }

        $adminUser = session()->get('admin_user');
        $data['add_by'] = $adminUser->userName ?? '';
        $data['date'] = date('Y-m-d H:i:s');

        if (($data['divisor'] ?? '') === '' || strlen((string) $data['divisor']) !== 4) {
            $data['divisor'] = '5000';
        }

        $data['code'] = $this->nextCustomerCode();
        $customerId = $this->userModel->insertUser($data);
        $this->saveEscalationRows((int) $customerId, $post);

        return $this->redirectWith('/admin/customer/all/all', 'Customer Added Successfully', 'alert-success');
    }

    private function render(string $view, array $data = []): string
    {
        $data += [
            'wconfig' => $this->wconfig,
            'permission' => $this->permission,
            'admin_user' => session()->get('admin_user'),
        ];
        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view($view, $data)
            . view('admin/footer');
    }

    private function can(string $permission): bool
    {
        return in_array($permission, $this->permission, true)
            || in_array($permission, $GLOBALS['permission'] ?? [], true);
    }

    private function postedOrSession(string $key)
    {
        $value = $this->request->getPost($key);
        if ($value !== null && $value !== '') {
            session()->set($key, $value);
            return $value;
        }

        return session()->get($key);
    }

    private function registrationHasColumn(string $column): bool
    {
        if ($this->registrationColumns === []) {
            $this->registrationColumns = array_map(
                static fn ($field) => $field->name,
                $this->db->getFieldData('registration')
            );
        }

        return in_array($column, $this->registrationColumns, true);
    }

    private function customerActivityRows(string $type, int $limit, int $offset): array
    {
        [$sql, $binds] = $this->customerActivityQuery($type);
        $sql .= ' ORDER BY login_id DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset);

        return $this->db->query($sql, $binds)->getResultArray();
    }

    private function customerActivityCount(string $type): int
    {
        [$sql, $binds] = $this->customerActivityQuery($type);
        $row = $this->db->query("SELECT COUNT(*) AS total FROM ({$sql}) activity_rows", $binds)->getRow();

        return (int) ($row->total ?? 0);
    }

    private function customerActivityQuery(string $type): array
    {
        $currentStart = date('Y-m-d 00:00:00', strtotime('-30 days'));

        if ($type === 'dead') {
            return ["
                SELECT r.id AS login_id, 0 AS b2b, 0 AS b2c, 0 AS current_b2b, 0 AS current_b2c
                FROM registration r
                LEFT JOIN (
                    SELECT DISTINCT login_id
                    FROM order_waybills
                    WHERE login_id IS NOT NULL
                        AND login_id != ''
                        AND status = 'Complete'
                        AND awb_status != 'Not Picked'
                    UNION
                    SELECT DISTINCT login_id
                    FROM b2c_waybills
                    WHERE login_id IS NOT NULL
                        AND login_id != ''
                        AND status NOT IN ('Cancel', 'Not Picked')
                ) activity ON activity.login_id = r.id
                WHERE activity.login_id IS NULL
            ", []];
        }

        $comparison = $type === 'active' ? '>' : '=';

        $sql = "
            SELECT
                activity.login_id,
                SUM(activity.b2b) AS b2b,
                SUM(activity.b2c) AS b2c,
                SUM(activity.current_b2b) AS current_b2b,
                SUM(activity.current_b2c) AS current_b2c
            FROM (
                SELECT
                    login_id,
                    COUNT(*) AS b2b,
                    0 AS b2c,
                    SUM(CASE WHEN date >= ? THEN 1 ELSE 0 END) AS current_b2b,
                    0 AS current_b2c
                FROM order_waybills
                WHERE login_id IS NOT NULL
                    AND login_id != ''
                    AND status = 'Complete'
                    AND awb_status != 'Not Picked'
                GROUP BY login_id
                UNION ALL
                SELECT
                    login_id,
                    0 AS b2b,
                    COUNT(*) AS b2c,
                    0 AS current_b2b,
                    SUM(CASE WHEN date >= ? THEN 1 ELSE 0 END) AS current_b2c
                FROM b2c_waybills
                WHERE login_id IS NOT NULL
                    AND login_id != ''
                    AND status NOT IN ('Cancel', 'Not Picked')
                GROUP BY login_id
            ) activity
            INNER JOIN registration r ON r.id = activity.login_id
            GROUP BY activity.login_id
            HAVING (SUM(activity.current_b2b) + SUM(activity.current_b2c)) {$comparison} 0
        ";

        return [$sql, [$currentStart, $currentStart]];
    }

    private function customerProfilesById(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }

        $rows = $this->db->table('registration')
            ->select('id,date,username,first,last,company,email,phone')
            ->whereIn('id', $ids)
            ->get()
            ->getResult();

        $profiles = [];
        foreach ($rows as $row) {
            $profiles[(int) $row->id] = $row;
        }

        return $profiles;
    }

    private function pageState($count, int $perPage): array
    {
        $page = (int) ($this->request->getGet('page') ?? 0);
        if ($page > 0) {
            return [$page, ($page - 1) * $perPage];
        }

        $offset = is_numeric($count) ? max(0, (int) $count) : 0;
        return [(int) floor($offset / $perPage) + 1, $offset];
    }

    private function redirectWith(string $path, string $message, string $class)
    {
        return redirect()->to($path)
            ->with('error', $message)
            ->with('error_class', $class);
    }

    private function uploadFile(string $field, string $directory, array $extensions, int $maxKb): array
    {
        $file = $this->request->getFile($field);
        if (! $file || $file->getName() === '') {
            return [null, null];
        }

        if (! $file->isValid()) {
            return [null, $file->getErrorString()];
        }

        if ($file->getSizeByUnit('kb') > $maxKb) {
            return [null, ucfirst($field) . ' file size should be less than ' . $maxKb . 'KB.'];
        }

        if (! in_array(strtolower($file->getClientExtension()), $extensions, true)) {
            return [null, ucfirst($field) . ' file type is not allowed.'];
        }

        $path = ROOTPATH . trim($directory, '/');
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }

        $fileName = $file->getRandomName();
        $file->move($path, $fileName);

        return [$fileName, null];
    }

    private function validateEscalationRows(array $post, bool $requireComplete): ?string
    {
        if (empty($post['c_name']) || ! is_array($post['c_name'])) {
            return null;
        }

        $total = count($post['c_name']);
        for ($i = 0; $i < $total; $i++) {
            $fields = [
                $post['c_name'][$i] ?? '',
                $post['c_phone'][$i] ?? '',
                $post['c_email'][$i] ?? '',
                $post['c_designation'][$i] ?? '',
                $post['c_department'][$i] ?? '',
                $post['c_escalation'][$i] ?? '',
            ];

            $hasAny = count(array_filter($fields, fn ($value) => $value !== '')) > 0;
            if (! $hasAny) {
                continue;
            }

            if ($requireComplete && count(array_filter($fields, fn ($value) => $value !== '')) !== count($fields)) {
                return 'All fields are mandatory in Escalation Row #' . ($i + 1);
            }

            if (! empty($post['c_phone'][$i]) && ! preg_match('/^[0-9]{10}$/', $post['c_phone'][$i])) {
                return 'Invalid Phone in Escalation Row #' . ($i + 1);
            }

            if (! empty($post['c_email'][$i]) && ! filter_var($post['c_email'][$i], FILTER_VALIDATE_EMAIL)) {
                return 'Invalid Email Format in Escalation Row #' . ($i + 1);
            }
        }

        return null;
    }

    private function saveEscalationRows(int $customerId, array $post): void
    {
        if ($customerId <= 0 || empty($post['c_name']) || ! is_array($post['c_name'])) {
            return;
        }

        $total = count($post['c_name']);
        for ($i = 0; $i < $total; $i++) {
            if (
                empty($post['c_name'][$i]) && empty($post['c_phone'][$i]) &&
                empty($post['c_email'][$i]) && empty($post['c_designation'][$i]) &&
                empty($post['c_department'][$i]) && empty($post['c_escalation'][$i])
            ) {
                continue;
            }

            $this->supportModel->insert('escalation_matrix', [
                'customer_id' => $customerId,
                'name' => $post['c_name'][$i] ?? '',
                'phone' => $post['c_phone'][$i] ?? '',
                'email' => $post['c_email'][$i] ?? '',
                'designation' => $post['c_designation'][$i] ?? '',
                'department' => $post['c_department'][$i] ?? '',
                'escalation_level' => $post['c_escalation'][$i] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function nextCustomerCode(): int
    {
        $row = $this->db->table('registration')
            ->selectMax('code')
            ->get()
            ->getRow();

        return ! empty($row->code) ? ((int) $row->code + 1) : 32987001;
    }

    private function durationColumn($startDate, $endDate): string
    {
        $months = ((int) date('Y', strtotime($endDate)) - (int) date('Y', strtotime($startDate))) * 12
            + ((int) date('m', strtotime($endDate)) - (int) date('m', strtotime($startDate)));

        if ($months === 1) {
            return 'monthly';
        }
        if ($months >= 2 && $months <= 4) {
            return 'quarterly';
        }
        if ($months >= 4 && $months <= 6) {
            return 'half';
        }

        return 'yearly';
    }

    private function durationLabel($startDate, $endDate): string
    {
        $column = $this->durationColumn($startDate, $endDate);

        return match ($column) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'half' => 'Semi-Yearly',
            default => 'Yearly',
        };
    }

    private function csvResponse($handle, string $filename)
    {
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}
