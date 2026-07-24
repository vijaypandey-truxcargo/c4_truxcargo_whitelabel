<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class CustomerServiceCharge extends Secure
{
    private function guard(string $permission)
    {
        if (! in_array($permission, $this->permission, true)) return redirect()->to('/admin/dashboard')->with('error', 'You do not have permission to perform this action.')->with('error_class', 'alert-danger');
        return null;
    }

    private function render(string $view, array $data): string
    {
        $data += ['wconfig' => $this->wconfig, 'permission' => $this->permission];
        $GLOBALS['permission'] = $this->permission;
        return view('admin/header', $data) . view($view, $data) . view('admin/footer');
    }

    public function index(int $page = 1)
    {
        if ($response = $this->guard('View Customer Service')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page); $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set(['csc_customer' => (string) $this->request->getPost('customer'), 'csc_service' => (string) $this->request->getPost('service'), 'csc_vendor' => (string) $this->request->getPost('vendor'), 'csc_status' => (string) $this->request->getPost('status')]);
            $page = 1;
        }
        $customer = (string) $session->get('csc_customer'); $service = (string) $session->get('csc_service');
        $vendor = (string) $session->get('csc_vendor'); $status = (string) $session->get('csc_status');
        $filter = static function ($builder) use ($customer, $service, $vendor, $status): void {
            if ($customer !== '' && $customer !== 'All') $builder->where('FIND_IN_SET(' . db_connect()->escape($customer) . ', customer_id) >', 0, false);
            if ($service !== '' && $service !== 'All') $builder->where('service_id', $service);
            if ($vendor !== '' && $vendor !== 'All') $builder->where('vendor_id', $vendor);
            if ($status !== '' && $status !== 'All') $builder->where('status', $status);
        };
        $db = db_connect(); $counter = $db->table('customer_service_charge'); $filter($counter); $total = $counter->countAllResults();
        $page = max(1, $page); $perPage = 10; $builder = $db->table('customer_service_charge'); $filter($builder);
        $rows = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        $customers = $this->supportModel->show('registration', 'ASC'); $services = $this->supportModel->show('service', 'ASC'); $vendors = $this->supportModel->show('vendor', 'ASC');
        return $this->render('admin/customer_service_charge', ['page_title' => 'Customer Service Charge', 'code' => $rows, 'customers' => $customers, 'services' => $services, 'vendors' => $vendors, 'customer_map' => $this->customerMap($customers), 'service_map' => $this->map($services), 'vendor_map' => $this->map($vendors), 'selected_customer' => $customer, 'selected_service' => $service, 'selected_vendor' => $vendor, 'selected_status' => $status, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full')]);
    }

    public function add()
    {
        if ($response = $this->guard('Add Customer Service')) return $response;
        return $this->render('admin/add_customer_service_charge', ['page_title' => 'Add Customer Service Charge'] + $this->lists());
    }

    public function insert()
    {
        if ($response = $this->guard('Add Customer Service')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['created_at'] = date('Y-m-d H:i:s'); $data['updated_at'] = date('Y-m-d H:i:s');
        if ($this->duplicate($data)) return redirect()->back()->withInput()->with('error', 'Duplicate record found.')->with('error_class', 'alert-warning');
        if ($this->supportModel->insert('customer_service_charge', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Record added successfully.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to add record.')->with('error_class', 'alert-danger');
    }

    public function edit(int $id)
    {
        if ($response = $this->guard('Edit Customer Service')) return $response;
        $row = $this->supportModel->find('customer_service_charge', $id);
        if (! $row) throw PageNotFoundException::forPageNotFound('Customer Service Charge was not found.');
        return $this->render('admin/edit_customer_service_charge', ['page_title' => 'Edit Customer Service Charge', 'customer_charge' => $row] + $this->lists());
    }

    public function update(int $id)
    {
        if ($response = $this->guard('Edit Customer Service')) return $response;
        $old = $this->supportModel->find('customer_service_charge', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Customer Service Charge was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['updated_at'] = date('Y-m-d H:i:s');
        if ($this->duplicate($data, $id)) return redirect()->back()->withInput()->with('error', 'Duplicate record already exists.')->with('error_class', 'alert-warning');
        if ($this->supportModel->update('customer_service_charge', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete()
    {
        if ($response = $this->guard('Delete Customer Service')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('customer_service_charge', $id);
        if ($old && $this->supportModel->delete('customer_service_charge', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_sample()
    {
        if ($response = $this->guard('Import Customer Service')) return $response;
        $path = ROOTPATH . 'assets/sample-customer-service-charge.csv';
        if (! is_file($path)) return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function export_all()
    {
        if ($response = $this->guard('Export Customer Service')) return $response;
        $customers = $this->customerMap($this->supportModel->show('registration')); $services = $this->map($this->supportModel->show('service')); $vendors = $this->map($this->supportModel->show('vendor'));
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['SNO','CUSTOMER_NAME','SERVICE_NAME','VENDOR_NAME','MIN_CHARGE','DIVISOR','MIN_WEIGHT','FUEL_PERCENT','STATUS']);
        foreach ($this->supportModel->show('customer_service_charge', 'DESC') as $i => $row) {
            $names = []; foreach ($this->ids($row->customer_id) as $id) if (isset($customers[$id])) $names[] = $customers[$id];
            fputcsv($stream, [$i + 1, implode(', ', $names), $services[$row->service_id] ?? '', $vendors[$row->vendor_id] ?? '', $row->min_charge, $row->divisor, $row->min_weight, $row->fuel_percent, $row->status ? 'Active' : 'Inactive']);
        }
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="customer_service_charge_' . date('Y-m-d') . '.csv"')->setBody($csv);
    }

    public function import()
    {
        if ($response = $this->guard('Import Customer Service')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/CustomerServiceCharge')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(fn ($v) => strtoupper(trim((string) $v, " \t\n\r\0\x0B\xEF\xBB\xBF")), $header);
        $inserted = $updated = $skipped = 0; $reasons = []; $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++; if (! array_filter($values, fn ($v) => trim((string) $v) !== '')) continue;
            if (count($values) !== count($header)) { $skipped++; $reasons[] = "Row {$line}: column count mismatch"; continue; }
            $row = array_combine($header, array_map('trim', $values)); $customerIds = []; $missing = [];
            foreach (array_filter(array_map('trim', explode(',', $row['CUSTOMER_CODE'] ?? ''))) as $code) { $item = $this->supportModel->search('registration', ['code' => $code]); $item ? $customerIds[] = $item->id : $missing[] = $code; }
            $service = $this->supportModel->search('service', ['code' => $row['SERVICE_CODE'] ?? '']); $vendor = null;
            if (($row['VENDOR_CODE'] ?? '') !== '') $vendor = $this->supportModel->search('vendor', ['code' => $row['VENDOR_CODE']]);
            if ($missing) { $skipped++; $reasons[] = "Row {$line}: Customer Code(s) not found: " . implode(', ', $missing); continue; }
            if (! $service) { $skipped++; $reasons[] = "Row {$line}: Service Code '{$row['SERVICE_CODE']}' not found"; continue; }
            if (($row['VENDOR_CODE'] ?? '') !== '' && ! $vendor) { $skipped++; $reasons[] = "Row {$line}: Vendor Code '{$row['VENDOR_CODE']}' not found"; continue; }
            sort($customerIds); $data = ['customer_id' => implode(',', $customerIds), 'service_id' => $service->id, 'vendor_id' => $vendor->id ?? null, 'min_charge' => (float) ($row['MIN_CHARGE'] ?? 0), 'divisor' => (float) ($row['DIVISOR'] ?? 0), 'min_weight' => (float) ($row['MIN_WEIGHT'] ?? 0), 'fuel_percent' => (float) ($row['FUEL_PERCENTAGE'] ?? $row['FUEL_PERCENT'] ?? 0), 'status' => strcasecmp($row['STATUS'] ?? '', 'Active') === 0 || ($row['STATUS'] ?? '') === '1' ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')];
            $existing = db_connect()->table('customer_service_charge')->where(['customer_id' => $data['customer_id'], 'service_id' => $data['service_id'], 'vendor_id' => $data['vendor_id']])->get()->getRow();
            if ($existing) { if ($this->supportModel->update('customer_service_charge', $data, $existing->id)) { $updated++; $this->log('UPDATE WHILE IMPORT', (array)$existing, $data); } else { $skipped++; $reasons[] = "Row {$line}: update failed"; } }
            else { $data['created_at'] = date('Y-m-d H:i:s'); if ($this->supportModel->insert('customer_service_charge', $data)) { $inserted++; $this->log('INSERT WHILE IMPORT', null, $data); } else { $skipped++; $reasons[] = "Row {$line}: insert failed"; } }
        }
        fclose($handle); $message = "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.";
        if ($reasons) $message .= '<br><strong>Skipped reasons:</strong><br>' . implode('<br>', array_map('esc', $reasons));
        return redirect()->to('/admin/CustomerServiceCharge')->with('error', $message)->with('error_class', $skipped ? 'alert-warning' : 'alert-success');
    }

    private function lists(): array { return ['customers' => $this->supportModel->show_condition('registration', 'ASC', ['status' => 1]), 'services' => $this->supportModel->show_condition('service', 'ASC', ['status' => 1]), 'vendors' => $this->supportModel->show_condition('vendor', 'ASC', ['status' => 1])]; }
    private function rules(): array { return ['customer_id' => 'required', 'service_id' => 'required|integer', 'vendor_id' => 'permit_empty|integer', 'min_charge' => 'required|decimal', 'divisor' => 'required|decimal', 'min_weight' => 'required|decimal', 'fuel_percent' => 'required|decimal', 'status' => 'required|in_list[0,1]']; }
    private function formData(): array { $ids = (array)$this->request->getPost('customer_id'); if (in_array('all', $ids, true)) $ids = array_map(fn($r) => $r->id, $this->supportModel->show('registration')); $ids = array_filter(array_map('intval', $ids)); sort($ids); return ['customer_id' => implode(',', $ids), 'service_id' => (int)$this->request->getPost('service_id'), 'vendor_id' => $this->request->getPost('vendor_id') !== '' ? (int)$this->request->getPost('vendor_id') : null, 'min_charge' => (float)$this->request->getPost('min_charge'), 'divisor' => (float)$this->request->getPost('divisor'), 'min_weight' => (float)$this->request->getPost('min_weight'), 'fuel_percent' => (float)$this->request->getPost('fuel_percent'), 'status' => (int)$this->request->getPost('status')]; }
    private function duplicate(array $data, ?int $except = null): bool { $builder = db_connect()->table('customer_service_charge')->where(['customer_id' => $data['customer_id'], 'service_id' => $data['service_id'], 'vendor_id' => $data['vendor_id']]); if ($except) $builder->where('id !=', $except); return $builder->countAllResults() > 0; }
    private function map(array $rows): array { $map = []; foreach ($rows as $row) $map[$row->id] = $row->name; return $map; }
    private function customerMap(array $rows): array { $map = []; foreach ($rows as $row) $map[$row->id] = trim(($row->code ? $row->code . ' - ' : '') . ($row->first ?? '') . ' ' . ($row->last ?? '')); return $map; }
    private function ids(string $value): array { return array_filter(array_map('trim', explode(',', $value)), 'strlen'); }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Customer Service Charge', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
