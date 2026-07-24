<?php

namespace App\Controllers\Admin;

use Config\Database;

class CustomerServiceCharge extends Secure
{
    protected $db;

    public function __construct()
    {
        parent::__construct();

        helper(['url', 'form']);
        $this->db = Database::connect();
    }

    public function index($count = 0)
    {
        if (! $this->can('Add Access Control')) {
            return redirect()->to('/admin/dashboard');
        }

        if ($this->request->getPost('apply')) {
            session()->set([
                'apply' => 'apply',
                'filter_status' => $this->request->getPost('status'),
                'filter_customer' => $this->request->getPost('customer'),
                'filter_service' => $this->request->getPost('service'),
                'filter_vendor' => $this->request->getPost('vendor'),
            ]);
        }

        $filterStatus = session()->get('filter_status');
        $filterCustomer = session()->get('filter_customer');
        $filterService = session()->get('filter_service');
        $filterVendor = session()->get('filter_vendor');

        $condition = '1=1';
        if (! empty($filterCustomer) && $filterCustomer !== 'All') {
            $condition .= " AND FIND_IN_SET('" . $filterCustomer . "', customer_id)";
        }
        if (! empty($filterService) && $filterService !== 'All') {
            $condition .= " AND service_id = '" . $filterService . "'";
        }
        if (! empty($filterVendor) && $filterVendor !== 'All') {
            $condition .= " AND vendor_id = '" . $filterVendor . "'";
        }
        if ($filterStatus !== '' && $filterStatus !== null && $filterStatus !== 'All') {
            $condition .= " AND status = '" . $filterStatus . "'";
        }

        $perPage = 10;
        [$currentPage, $offset] = $this->pageState($count, $perPage);
        $totalRows = $this->supportModel->getRows('customer_service_charge', $condition);
        $rows = $this->supportModel->getCustomerServiceCharge($perPage, $offset, $condition);
        $this->attachCustomerNames($rows);

        return $this->render('admin/customer_service_charge', [
            'page_title' => 'Customer Service Charge',
            'count' => $offset,
            'links' => service('pager')->makeLinks($currentPage, $perPage, $totalRows, 'admin_full'),
            'code' => $rows,
            'key' => $this->supportModel->distinct_rows('customer_service_charge', 'id', 'ASC'),
            'selected_status' => $filterStatus,
            'selected_customer' => $filterCustomer,
            'selected_service' => $filterService,
            'selected_vendor' => $filterVendor,
            'customers' => $this->db->table('registration')->get()->getResult(),
            'services' => $this->db->table('service')->get()->getResult(),
            'vendors' => $this->db->table('vendor')->get()->getResult(),
        ]);
    }

    public function add()
    {
        if (! $this->can('Add Access Control')) {
            return redirect()->to('/admin/dashboard');
        }

        return $this->render('admin/add_customer_service_charge', [
            'page_title' => 'Add Customer Service Charge',
            'customers' => $this->db->table('registration')->get()->getResult(),
            'services' => $this->db->table('service')->where('status', 1)->get()->getResult(),
            'vendors' => $this->db->table('vendor')->where('status', 1)->get()->getResult(),
        ]);
    }

    public function insert()
    {
        $post = $this->request->getPost();
        unset($post['submit']);

        $customerIds = $this->normalizeCustomerIds($post['customer_id'] ?? []);
        $post['customer_id'] = implode(',', $customerIds);

        $where = [
            'customer_id' => $post['customer_id'],
            'service_id' => $post['service_id'] ?? '',
            'vendor_id' => $post['vendor_id'] ?? '',
        ];

        $exists = $this->db->table('customer_service_charge')->where($where)->get()->getRow();
        if ($exists) {
            return $this->redirectWith('/admin/CustomerServiceCharge', 'Duplicate record found.', 'alert-warning');
        }

        if ($this->supportModel->insert('customer_service_charge', $post)) {
            $this->logActivity('INSERT', null, $post);
            return $this->redirectWith('/admin/CustomerServiceCharge', 'Record added successfully.', 'alert-success');
        }

        return $this->redirectWith('/admin/CustomerServiceCharge', 'Failed to add record.', 'alert-danger');
    }

    public function edit($id)
    {
        if (! $this->can('Add Access Control')) {
            return redirect()->to('/admin/dashboard');
        }

        $charge = $this->supportModel->find('customer_service_charge', $id);
        if (! $charge) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Customer service charge was not found.');
        }

        return $this->render('admin/edit_customer_service_charge', [
            'page_title' => 'Edit Customer Service Charge',
            'customer_charge' => $charge,
            'customers' => $this->supportModel->show('registration'),
            'services' => $this->db->table('service')->where('status', 1)->get()->getResult(),
            'vendors' => $this->db->table('vendor')->where('status', 1)->get()->getResult(),
        ]);
    }

    public function update($id)
    {
        $post = $this->request->getPost();
        unset($post['submit'], $post['Update']);

        $customerIds = $this->normalizeCustomerIds($post['customer_id'] ?? []);
        $customerIdsString = implode(',', $customerIds);

        $duplicate = $this->db->table('customer_service_charge')
            ->where('service_id', $post['service'] ?? '')
            ->where('customer_id', $customerIdsString)
            ->where('vendor_id', $post['vendor'] ?? '')
            ->where('id !=', $id)
            ->get()
            ->getRow();

        if ($duplicate) {
            return $this->redirectWith("/admin/CustomerServiceCharge/edit/{$id}", 'Duplicate record already exists.', 'alert-warning');
        }

        $data = [
            'customer_id' => $customerIdsString,
            'service_id' => $post['service'] ?? '',
            'vendor_id' => $post['vendor'] ?? null,
            'min_charge' => $post['min_charge'] ?? '',
            'divisor' => $post['divisor'] ?? '',
            'min_weight' => $post['min_weight'] ?? '',
            'fuel_percent' => $post['fuel_percent'] ?? '',
            'status' => $post['status'] ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->supportModel->update('customer_service_charge', $data, $id)) {
            return $this->redirectWith('/admin/CustomerServiceCharge', 'Successfully Updated.', 'alert-success');
        }

        return $this->redirectWith("/admin/CustomerServiceCharge/edit/{$id}", 'Failed To Update.', 'alert-danger');
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        $oldData = $this->supportModel->find('customer_service_charge', $id);

        if ($this->supportModel->delete('customer_service_charge', $id)) {
            $this->logActivity('DELETE', $oldData ? (array) $oldData : null, null);
            return $this->redirectWith('/admin/CustomerServiceCharge', 'Successfully Delete.', 'alert-success');
        }

        return $this->redirectWith('/admin/CustomerServiceCharge', 'Failed To Delete.', 'alert-danger');
    }

    public function import()
    {
        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/CustomerServiceCharge');
        }

        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return $this->redirectWith('/admin/CustomerServiceCharge', 'No file uploaded.', 'alert-danger');
        }

        $filePath = $file->getTempName();
        $raw = file_get_contents($filePath);
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        file_put_contents($filePath, $raw);

        $csv = array_map('str_getcsv', file($filePath));
        if (empty($csv) || count($csv) < 2) {
            return $this->redirectWith('/admin/CustomerServiceCharge', 'CSV file is empty.', 'alert-danger');
        }

        $header = array_map('trim', $csv[0]);
        $insertCount = 0;
        $updateCount = 0;
        $errorCount = 0;
        $rowCount = 0;
        $invalidRows = [];

        for ($i = 1; $i < count($csv); $i++) {
            if (count($csv[$i]) !== count($header)) {
                continue;
            }

            $row = array_combine($header, array_map('trim', $csv[$i]));
            $rowCount++;

            $customerIdsString = '';
            if (! empty($row['CUSTOMER_CODE'])) {
                $customerCodes = explode(',', preg_replace('/\s+/', '', $row['CUSTOMER_CODE']));
                $customerIds = [];

                foreach ($customerCodes as $code) {
                    if ($code === '') {
                        continue;
                    }

                    $customer = $this->db->table('registration')->where('code', $code)->get()->getRow();
                    if ($customer) {
                        $customerIds[] = $customer->id;
                    }
                }

                if (empty($customerIds)) {
                    $errorCount++;
                    $invalidRows[] = 'Row ' . ($i + 1) . ' -> Invalid Customer Code: ' . $row['CUSTOMER_CODE'];
                    continue;
                }

                sort($customerIds);
                $customerIdsString = implode(',', $customerIds);
            }

            $service = $this->db->table('service')->where('code', $row['SERVICE_CODE'] ?? '')->get()->getRow();
            if (! $service) {
                $errorCount++;
                $invalidRows[] = 'Row ' . ($i + 1) . ' -> Invalid Service Code: ' . ($row['SERVICE_CODE'] ?? '');
                continue;
            }

            $vendor = null;
            if (! empty($row['VENDOR_CODE'])) {
                $vendor = $this->db->table('vendor')->where('code', $row['VENDOR_CODE'])->get()->getRow();
            }

            $data = [
                'customer_id' => $customerIdsString,
                'service_id' => $service->id,
                'vendor_id' => $vendor ? $vendor->id : null,
                'min_charge' => $row['MIN_CHARGE'] ?? '',
                'divisor' => $row['DIVISOR'] ?? '',
                'min_weight' => $row['MIN_WEIGHT'] ?? '',
                'fuel_percent' => $row['FUEL_PERCENTAGE'] ?? '',
                'status' => strtolower($row['STATUS'] ?? '') === 'active' ? 1 : 0,
            ];

            $condition = [
                'customer_id' => $customerIdsString,
                'service_id' => $service->id,
            ];

            if ($this->supportModel->getRows('customer_service_charge', $condition) > 0) {
                if ($this->supportModel->update_condition('customer_service_charge', $data, $condition)) {
                    $updateCount++;
                }
            } elseif ($this->supportModel->insert('customer_service_charge', $data)) {
                $insertCount++;
            }
        }

        $msg = '<b>Customer Service Charge Import Summary:</b><br>';
        $msg .= "Total Rows: {$rowCount}<br>";
        $msg .= "Inserted: {$insertCount}<br>";
        $msg .= "Updated: {$updateCount}<br>";
        $msg .= "Skipped (Invalid Code): {$errorCount}<br>";
        if (! empty($invalidRows)) {
            $msg .= '<br><b>Invalid Rows:</b><br>' . implode('<br>', $invalidRows);
        }

        return $this->redirectWith('/admin/CustomerServiceCharge', $msg, 'alert-success');
    }

    public function export_sample()
    {
        $filePath = FCPATH . 'assets/sample-customer-service-charge.csv';
        if (is_file($filePath)) {
            return $this->response->download($filePath, null);
        }

        return $this->redirectWith('/admin/CustomerServiceCharge', 'Some thing went wrong. Please try again.', 'alert-danger');
    }

    public function export_all()
    {
        $rows = $this->db->table('customer_service_charge csc')
            ->select('csc.*, s.name as service_name, v.name as vendor_name')
            ->join('service s', 's.id = csc.service_id', 'left')
            ->join('vendor v', 'v.id = csc.vendor_id', 'left')
            ->get()
            ->getResult();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['SNO', 'CUSTOMER_NAME', 'SERVICE_NAME', 'VENDOR_NAME', 'MIN_CHARGE', 'DIVISOR', 'MIN_WEIGHT', 'FUEL_PERCENT', 'STATUS']);

        $i = 1;
        foreach ($rows as $row) {
            $customerNames = [];
            if (! empty($row->customer_id)) {
                foreach (explode(',', $row->customer_id) as $cid) {
                    $customer = $this->db->table('registration')
                        ->select('first,last')
                        ->where('id', trim($cid))
                        ->get()
                        ->getRow();
                    if ($customer) {
                        $customerNames[] = $customer->first . ' ' . $customer->last;
                    }
                }
            }

            fputcsv($handle, [
                $i++,
                implode(', ', $customerNames),
                $row->service_name,
                $row->vendor_name,
                $row->min_charge,
                $row->divisor,
                $row->min_weight,
                $row->fuel_percent,
                $row->status == 1 ? 'Active' : 'Inactive',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="customer_service_charge_' . date('Y-m-d') . '.csv"')
            ->setBody($csv);
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

    private function redirectWith(string $path, string $message, string $class)
    {
        return redirect()->to($path)
            ->with('error', $message)
            ->with('error_class', $class);
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

    private function normalizeCustomerIds($customerIds): array
    {
        if (! is_array($customerIds)) {
            $customerIds = $customerIds !== null && $customerIds !== '' ? [$customerIds] : [];
        }

        if (in_array('all', $customerIds, true)) {
            $customerIds = array_column(
                array_map(static fn ($row) => (array) $row, $this->supportModel->select_rows('registration', 'id', 'ASC')),
                'id'
            );
        }

        sort($customerIds);
        return $customerIds;
    }

    private function attachCustomerNames(array $rows): void
    {
        foreach ($rows as $row) {
            $names = [];
            $i = 1;
            if (! empty($row->customer_id)) {
                foreach (explode(',', $row->customer_id) as $cid) {
                    $customer = $this->db->table('registration')
                        ->select('first,last')
                        ->where('id', trim($cid))
                        ->get()
                        ->getRow();
                    if ($customer) {
                        $names[] = [
                            'no' => $i++,
                            'name' => $customer->first . ' ' . $customer->last,
                        ];
                    }
                }
            }

            if (empty($names)) {
                $names[] = [
                    'no' => 1,
                    'name' => '<b>Default Customer</b>',
                ];
            }

            $row->customer_names = $names;
        }
    }

    private function logActivity(string $action, ?array $oldData, ?array $newData): void
    {
        $adminUser = session()->get('admin_user');
        $this->supportModel->insert('activity_logs', [
            'user_id' => session()->get('user_id'),
            'user_name' => $adminUser->userName ?? '',
            'module_name' => 'Customer Service Charge',
            'action_type' => $action,
            'old_data' => $oldData === null ? null : json_encode($oldData),
            'new_data' => $newData === null ? null : json_encode($newData),
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
