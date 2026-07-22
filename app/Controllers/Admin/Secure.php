<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use App\Models\SupportModel;
use CodeIgniter\HTTP\Exceptions\RedirectException;

class Secure extends BaseController
{
    protected $supportModel;
    protected $loginModel;
    protected $permission = [];
    protected $notify = [];
    protected $notify_cod = [];
    protected $notify1 = [];
    protected $gatiurl;
    protected $wconfig;

    public function __construct()
    {
        helper(['url', 'form']);

        $this->supportModel = new SupportModel();
        $this->loginModel = new LoginModel();

        $this->initializeAdminAccess();
    }

    protected function initializeAdminAccess(): void
    {
        $adminUser = session()->get('admin_user');
       
        if (! session()->get('admin_login_id')) {
            redirect()->to('/admin/login')->send();
            return;
        }
       // $this->wconfig = $this->supportModel->find('config',1); 

        $adminUser = session()->get('admin_user');
       
        $adminUserName = $adminUser->userName ?? null;

        // Permissions in `control.type` are assigned against the logged-in
        // admin's userName. Load that role on every request so all pages render
        // the same sidebar as the dashboard.
        $permissions = ! empty($adminUserName)
            ? $this->loginModel->permission($adminUserName)
            : [];

        // Retain the legacy Admin role as a fallback for installations where a
        // user-specific control record has not been created.
        if (empty($permissions)) {
            $permissions = $this->loginModel->permission('admin');
        }
        if (empty($permissions)) {
            $permissions = $this->loginModel->permission('Admin');
        }

        $this->permission = $this->normalizePermissions($permissions);
        session()->set('admin_permission', $this->permission);
        $GLOBALS['permission'] = $this->permission;

        $adminId = session()->get('admin_login_id');
        if (! empty($adminId)) {
            $user = $this->loginModel->profile($adminId);

            if ($user && $user->login_token != session()->get('admin_login_token')) {
                session()->remove([
                    'admin_id', 'admin_login_id', 'admin_login_token',
                    'admin_user', 'admin_permission', 'admin_login_activity_id',
                ]);
                throw new RedirectException(redirect()->to('/admin/login'));
            }

            $this->supportModel->update('users', ['last_activity' => date('Y-m-d H:i:s')], $adminId);
        }

        $date = date('Y-m-d');
        $notify = $this->supportModel->getRows('ticket', 'status="Open" and date like "%' . $date . '%"');
        $notifyCod = $this->supportModel->getRows('cod_request');

        if (in_array('Update Training', $this->permission, true)) {
            $training = $this->supportModel->getRows('training', 'status!="Completed" and emp IS Null');
        } else {
            $training = $this->supportModel->getRows('training', 'status!="Completed" and emp = "' . ($adminUserName ?? '') . '"');
        }

        session()->set('notify1', $training);
        session()->set('notify', $notify);
        session()->set('notify_cod', $notifyCod);

        $this->notify1 = $training;
        $this->notify = $notify;
        $this->notify_cod = $notifyCod;
        $this->gatiurl = 'https://justi.gati.com/webservices/';
        $this->wconfig = $this->supportModel->find('config', 1);
    }

    public function token($username, $password)
    {
        $data = ['username' => $username, 'password' => $password];
        $tokenKey = $this->supportModel->search_col('b2b-partner', 'jwt', $data);

        return $tokenKey && ! empty($tokenKey->jwt) ? $tokenKey->jwt : '';
    }

    public function lrnum()
    {
        $lr = $this->request->getPost('lr');
        echo $this->supportModel->getRows('lrnumber', ['lr' => $lr, 'status' => 1]);
    }

    public function state()
    {
        $zip = $this->request->getPost('pincode');
        $url = base_url("api/apic/state/{$zip}");
        $response = $this->curlGet($url);

        echo json_encode($response['data'] ?? []);
    }

    public function pincode_check()
    {
        $pincode = $this->request->getPost('pincode');
        $condition = ['pincode' => $pincode];
        $table = 'pincodes';
        $num = $this->supportModel->getRows($table, $condition);

        if ($num > 0) {
            $data = $this->supportModel->search_col($table, 'status', $condition);
            echo $data && $data->status === 'No' ? 0 : 1;
            return;
        }

        echo $num;
    }

    public function delhivery_price($state1, $state2, $city1, $city2, $panel, $matrix, $login_id)
    {
        if ($panel === 'Delhivery Dense' || $panel === 'Delhivery Cargo' || $panel === 'Delhivery Lite') {
            $matrix = 16;
        }

        if ($panel === 'Delhivery Lite') {
            $tableZone = 'zone_nine';
            $table = 'air_price';
        } elseif ($matrix == 16 && $panel !== 'Delhivery Lite') {
            $tableZone = 'air_zone';
            $table = 'air_price';
        } else {
            $tableZone = 'matrix_zone';
            $table = 'matrix';
        }

        $data1 = $this->supportModel->search($tableZone, ['city' => $city1, 'state' => $state1]);
        $zone1 = $data1->zone ?? null;

        if (empty($zone1)) {
            $data1 = $this->supportModel->search($tableZone, ['state' => $state1]);
            $zone1 = $data1->zone ?? null;
        }

        $data2 = $this->supportModel->search($tableZone, ['city' => $city2, 'state' => $state2]);
        $zone2 = $data2->zone ?? null;

        if (empty($zone2)) {
            $data2 = $this->supportModel->search($tableZone, ['state' => $state2]);
            $zone2 = $data2->zone ?? null;
        }

        $num = $this->supportModel->getRows($table, ['login_id' => $login_id, 'panel' => $panel, 'zone' => $zone1]);
        if ($num > 0) {
            $price = $this->supportModel->search($table, ['zone' => $zone1, 'panel' => $panel, 'login_id' => $login_id])->{$zone2};
        } else {
            $price = $this->supportModel->search($table, ['zone' => $zone1, 'panel' => $panel, 'login_id' => ''])->{$zone2};
        }

        return ['price' => $price, 'zone1' => $zone1, 'zone2' => $zone2];
    }

    public function job_process()
    {
        $num = $this->supportModel->getRows('order_waybills', ['status' => 'Processing']);
        if ($num > 0) {
            $data = $this->supportModel->select_rows_limit('order_waybills', 'id,job_id,panel,login_id', 'ASC', '1', ['status' => 'Processing']);
            foreach ($data as $row) {
                $id = $row->id;
                $jobId = $row->job_id;
                $api = $this->supportModel->search_col('b2b-partner', 'jwt', ['title' => $row->panel]);
                $token = $api->jwt ?? '';
                $result = $this->jobStatus($jobId, $token);

                if (($result['status'] ?? '') === 'Complete') {
                    $post['lrnum'] = $result['lrnum'] ?? '';
                    $post['status'] = $result['status'] ?? '';
                    $post['waybills'] = $result['waybills'] ?? '';
                    $this->supportModel->update('order_waybills', $post, $id);

                    $reason = 'Debit For Order Creation LR No : ' . ($post['lrnum'] ?? '');
                    $this->supportModel->update_condition('wallet', ['reason' => $reason], ['order_id' => $jobId]);
                }
            }
        }
    }

    public function condition($date, $col)
    {
        $condition = '';
        if ($date === 'Today') {
            $day = date('Y-m-d');
            return " and {$col} like '%{$day}%'";
        }
        if ($date === 'Yesterday') {
            $day = date('Y-m-d', strtotime('-1 days'));
            return " and {$col} like '%{$day}%'";
        }
        if ($date === '7 Days') {
            $date1 = date('Y-m-d', strtotime('+1 days'));
            $date2 = date('Y-m-d', strtotime('-6 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === '30 Days') {
            $date1 = date('Y-m-d', strtotime('+1 days'));
            $date2 = date('Y-m-d', strtotime('-29 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === 'This Month') {
            $month = date('Y-m');
            return " and {$col} like '%{$month}%'";
        }
        if ($date === 'Last Month') {
            $month = date('Y-m', strtotime('last month'));
            return " and {$col} like '%{$month}%'";
        }

        return $condition;
    }

    public function condition_lastmonth($date, $col)
    {
        $condition = '';
        if ($date === 'Today') {
            $date1 = date('Y-m-d', strtotime('-1 month'));
            return " and {$col} like '%{$date1}%'";
        }
        if ($date === 'Yesterday') {
            $date1 = date('Y-m-d', strtotime('-1 month -1 day'));
            return " and {$col} like '%{$date1}%'";
        }
        if ($date === '7 Days') {
            $date1 = date('Y-m-d', strtotime('+1 days'));
            $date2 = date('Y-m-d', strtotime('-1 month -6 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === '30 Days') {
            $date1 = date('Y-m-d', strtotime('+1 days'));
            $date2 = date('Y-m-d', strtotime('-1 month -29 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === 'This Month') {
            $month = date('Y-m', strtotime('last month'));
            return " and {$col} like '%{$month}%'";
        }
        if ($date === 'Last Month') {
            $month = date('Y-m', strtotime('-2 month'));
            return " and {$col} like '%{$month}%'";
        }

        return $condition;
    }

    public function condition_rem($date, $col)
    {
        $condition = '';
        if ($date === 'Today') {
            $date1 = date('Y-m-d', strtotime('-1 days'));
            return " and {$col} like '%{$date1}%'";
        }
        if ($date === 'Yesterday') {
            $date1 = date('Y-m-d', strtotime('-2 days'));
            return " and {$col} like '%{$date1}%'";
        }
        if ($date === '7 Days') {
            $date1 = date('Y-m-d', strtotime('+0 days'));
            $date2 = date('Y-m-d', strtotime('-7 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === '30 Days') {
            $date1 = date('Y-m-d', strtotime('+0 days'));
            $date2 = date('Y-m-d', strtotime('-30 days'));
            return " and {$col} between '{$date2}' and '{$date1}'";
        }
        if ($date === 'This Month') {
            $month = date('Y-m');
            return " and {$col} like '%{$month}%'";
        }
        if ($date === 'Last Month') {
            $month = date('Y-m', strtotime('last month'));
            return " and {$col} like '%{$month}%'";
        }

        return $condition;
    }

    public function csv($title = '')
    {
        if (empty($title)) {
            $title = 'report';
        }

        echo '<script>
            function downloadCSV(csv, filename) {
                var csvFile; var downloadLink;
                csvFile = new Blob([csv], {type: "text/csv"});
                downloadLink = document.createElement("a");
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
            }
            function exportTableToCSV(filename) {
                var table_id = "report"; var csv = [];
                var rows = document.querySelectorAll("table#" + table_id + " tr");
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++) {
                        row.push(cols[j].innerText);
                    }
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }
            exportTableToCSV("' . $title . '.csv"); window.close();
        </script>';
    }

    public function csv_new($csvData, $title = '')
    {
        if (empty($title)) {
            $title = 'report';
        }

        $filename = $title . date('d-m-y') . '.csv';
        $this->response->setContentType('application/octet-stream');
        $this->response->setHeader('Content-Disposition', 'attachment;filename=' . $filename);

        $out = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
    }

    public function file_check($str)
    {
        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid()) {
            return false;
        }

        $allowedMimeTypes = [
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'text/plain',
        ];

        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientExtension());

        return ($ext === 'csv') && in_array($mime, $allowedMimeTypes, true);
    }

    protected function curlGet(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function jobStatus($jobId, $token)
    {
        return [];
    }

    protected function normalizePermissions($permissions): array
    {
        if (is_string($permissions)) {
            $permissions = array_map('trim', explode(',', $permissions));
            return array_values(array_filter($permissions, fn ($value) => $value !== ''));
        }

        if (! is_array($permissions)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', $permissions), fn ($value) => $value !== ''));
    }
}
