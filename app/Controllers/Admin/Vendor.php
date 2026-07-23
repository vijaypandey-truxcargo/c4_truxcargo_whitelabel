<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Vendor extends Secure
{
    private function guard(string $permission)
    {
        if (! in_array($permission, $this->permission, true)) return redirect()->to('/admin/dashboard')->with('error', 'You do not have permission to perform this action.')->with('error_class', 'alert-danger');
        return null;
    }

    private function render(string $view, array $data): string
    {
        $data += ['wconfig' => $this->wconfig, 'permission' => $this->permission]; $GLOBALS['permission'] = $this->permission;
        return view('admin/header', $data) . view($view, $data) . view('admin/footer');
    }

    public function index(int $page = 1)
    {
        if ($response = $this->guard('View Vendor')) return $response;
        $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set(['filter_vendor_name' => trim((string) $this->request->getPost('name')), 'filter_vendor_code' => trim((string) $this->request->getPost('code')), 'filter_vendor_status' => (string) $this->request->getPost('status')]); $page = 1;
        }
        $name = (string) $session->get('filter_vendor_name'); $code = (string) $session->get('filter_vendor_code'); $status = (string) $session->get('filter_vendor_status');
        $filter = static function ($builder) use ($name, $code, $status): void { if ($name !== '' && $name !== 'All') $builder->where('name', $name); if ($code !== '' && $code !== 'All') $builder->where('code', $code); if ($status !== '' && $status !== 'All') $builder->where('status', $status); };
        $db = db_connect(); $countBuilder = $db->table('vendor'); $filter($countBuilder); $total = $countBuilder->countAllResults();
        $page = max(1, $page); $perPage = 10; $builder = $db->table('vendor'); $filter($builder);
        $rows = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/vendor', ['page_title' => 'Vendor', 'code' => $rows, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total), 'vendor_list' => $this->supportModel->show('vendor', 'ASC'), 'selected_name' => $name, 'selected_code' => $code, 'selected_status' => $status]);
    }

    public function add_vendor()
    {
        if ($response = $this->guard('Add Vendor')) return $response;
        return $this->render('admin/master/add_vendor', ['page_title' => 'Add Vendor'] + $this->formLists());
    }

    public function insert_vendor()
    {
        if ($response = $this->guard('Add Vendor')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->vendorData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('vendor', $data)) { $this->log('INSERT', null, $data); return redirect()->to('/admin/vendor')->with('error', 'Successfully Added.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_vendor(int $id)
    {
        if ($response = $this->guard('Edit Vendor')) return $response;
        $vendor = $this->supportModel->find('vendor', $id); if (! $vendor) throw PageNotFoundException::forPageNotFound('Vendor was not found.');
        return $this->render('admin/master/edit_vendor', ['page_title' => 'Edit Vendor', 'vendor' => $vendor] + $this->formLists());
    }

    public function update_vendor(int $id)
    {
        if ($response = $this->guard('Edit Vendor')) return $response;
        $old = $this->supportModel->find('vendor', $id); if (! $old) throw PageNotFoundException::forPageNotFound('Vendor was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->vendorData();
        if ($this->supportModel->update('vendor', $data, $id)) { $this->log('UPDATE', (array) $old, $data); return redirect()->to('/admin/vendor')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_vendor()
    {
        if ($response = $this->guard('Delete Vendor')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('vendor', $id);
        if ($old && $this->supportModel->delete('vendor', $id)) { $this->log('DELETE', (array) $old, null); return redirect()->to('/admin/vendor')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success'); }
        return redirect()->to('/admin/vendor')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_all()
    {
        if ($response = $this->guard('Export Vendor')) return $response;
        $companies = []; foreach ($this->supportModel->show('company', 'ASC') as $item) $companies[$item->id] = $item->name;
        $hubs = []; foreach ($this->supportModel->show('hub', 'ASC') as $item) $hubs[$item->id] = $item->name;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','Vendor Name','Vendor Code','Email','Phone','Address','Vendor Type','Company Name','Destination Hub','AIR Divisor','SFC Divisor','FREIGHT','Min Weight','Max Weight','Status','Created At']);
        foreach ($this->supportModel->show('vendor', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->name ?? '', $row->code ?? '', $row->email ?? '', $row->phone ?? '', $row->address ?? '', $row->vendor_type ?? '', $companies[$row->company ?? 0] ?? '', $hubs[$row->destination_hub ?? 0] ?? '', $row->air_divisor ?? '', $row->sfc_divisor ?? '', $row->min_freight ?? '', $row->min_weight ?? '', $row->max_weight ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive', $row->created_at ?? '']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="vendor_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_vendor()
    {
        if ($response = $this->guard('Add Vendor')) return $response;
        $path = ROOTPATH . 'assets/sample-import-vendor.csv';
        if (! is_file($path)) return redirect()->to('/admin/vendor')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_vendor()
    {
        if ($response = $this->guard('Add Vendor')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/vendor')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/vendor')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header); $inserted = $updated = $skipped = 0; $rowNumber = 1; $skipReasons = [];
        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($values) !== count($header)) { $skipped++; $skipReasons[] = "Row {$rowNumber}: column count does not match the header"; continue; }
            $row = array_combine($header, $values); $code = trim((string) ($row['Vendor Code'] ?? '')); $air = trim((string) ($row['AIR-Divisor'] ?? '')); $sfc = trim((string) ($row['SFC-Divisor'] ?? ''));
            $companyCode = trim((string) ($row['Company Code'] ?? '')); $hubCode = trim((string) ($row['Hub Code'] ?? ''));
            $company = $this->supportModel->search('company', ['code' => $companyCode]); $hub = $this->supportModel->search('hub', ['code' => $hubCode]);
            if ($code === '') { $skipped++; $skipReasons[] = "Row {$rowNumber}: Vendor Code is empty"; continue; }
            if (! $company) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): Company Code '{$companyCode}' was not found"; continue; }
            if (! $hub) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): Hub Code '{$hubCode}' was not found"; continue; }
            if ($air !== '' && ! preg_match('/^\d{4}$/', $air)) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): AIR-Divisor must be exactly 4 digits"; continue; }
            if ($sfc !== '' && ! preg_match('/^\d{4}$/', $sfc)) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): SFC-Divisor must be exactly 4 digits"; continue; }
            $statusValue = trim((string) ($row['Status'] ?? '')); $data = ['name' => trim((string) ($row['Vendor Name'] ?? '')), 'code' => $code, 'email' => trim((string) ($row['Email'] ?? '')), 'phone' => trim((string) ($row['Phone'] ?? '')), 'address' => trim((string) ($row['Address'] ?? '')), 'vendor_type' => trim((string) ($row['Vendor Type'] ?? '')), 'destination_hub' => $hub->id, 'company' => $company->id, 'air_divisor' => $air, 'sfc_divisor' => $sfc, 'min_weight' => trim((string) ($row['Min-Weight'] ?? '')), 'max_weight' => trim((string) ($row['Max-Weight'] ?? '')), 'min_freight' => trim((string) ($row['Freight'] ?? '')), 'status' => $statusValue === '1' || strcasecmp($statusValue, 'Active') === 0 ? 1 : 0];
            $existing = $this->supportModel->search('vendor', ['code' => $code]);
            if ($existing) { if ($this->supportModel->update('vendor', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++; }
            else { $data['created_at'] = date('Y-m-d H:i:s'); if ($this->supportModel->insert('vendor', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++; }
        }
        fclose($handle);
        $message = "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.";
        if ($skipReasons !== []) $message .= '<br><strong>Skipped reasons:</strong><br>' . implode('<br>', array_map('esc', $skipReasons));
        return redirect()->to('/admin/vendor')->with('error', $message)->with('error_class', $skipped > 0 ? 'alert-warning' : 'alert-success');
    }

    private function formLists(): array { return ['companies' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1]), 'hub' => $this->supportModel->show_condition('hub', 'ASC', ['status' => 1]), 'mode' => $this->supportModel->show_condition('mode', 'ASC', ['status' => 1])]; }
    private function rules(): array { return ['name' => 'required', 'code' => 'required', 'email' => 'required|valid_email', 'phone' => 'required', 'address' => 'required', 'vendor_type' => 'required', 'destination_hub' => 'required|integer', 'company' => 'required|integer', 'air_divisor' => 'permit_empty|exact_length[4]|numeric', 'sfc_divisor' => 'permit_empty|exact_length[4]|numeric', 'min_weight' => 'required|decimal', 'max_weight' => 'required|decimal', 'status' => 'required|in_list[0,1]']; }
    private function vendorData(): array { $data = []; foreach (['name','code','email','phone','address','vendor_type','air_divisor','sfc_divisor','min_freight','min_weight','max_weight'] as $field) $data[$field] = trim((string) $this->request->getPost($field)); $data['destination_hub'] = (int) $this->request->getPost('destination_hub'); $data['company'] = (int) $this->request->getPost('company'); $data['status'] = (int) $this->request->getPost('status'); return $data; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Vendor', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
