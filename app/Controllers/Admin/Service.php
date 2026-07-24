<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Service extends Secure
{
    private function guard(string $permission)
    {
        if (! in_array($permission, $this->permission, true)) {
            return redirect()->to('/admin/dashboard')->with('error', 'You do not have permission to perform this action.')->with('error_class', 'alert-danger');
        }
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
        if ($response = $this->guard('View Service')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set([
                'filter_service_name' => trim((string) $this->request->getPost('name')),
                'filter_service_code' => trim((string) $this->request->getPost('code')),
                'filter_service_status' => (string) $this->request->getPost('status'),
            ]);
            $page = 1;
        }
        $name = (string) $session->get('filter_service_name');
        $code = (string) $session->get('filter_service_code');
        $status = (string) $session->get('filter_service_status');
        $filter = static function ($builder) use ($name, $code, $status): void {
            if ($name !== '' && $name !== 'All') $builder->where('name', $name);
            if ($code !== '' && $code !== 'All') $builder->where('code', $code);
            if ($status !== '' && $status !== 'All') $builder->where('status', $status);
        };
        $db = db_connect();
        $countBuilder = $db->table('service'); $filter($countBuilder); $total = $countBuilder->countAllResults();
        $page = max(1, $page); $perPage = 10;
        $builder = $db->table('service'); $filter($builder);
        $rows = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/service', [
            'page_title' => 'Service', 'code' => $rows, 'key' => $this->supportModel->show('service', 'ASC'),
            'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full'),
            'selected_name' => $name, 'selected_code' => $code, 'selected_status' => $status,
        ]);
    }

    public function add_service()
    {
        if ($response = $this->guard('Add Service')) return $response;
        return $this->render('admin/master/add_service', ['page_title' => 'Add Service'] + $this->formLists());
    }

    public function insert_service()
    {
        if ($response = $this->guard('Add Service')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->serviceData();
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('service', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/service')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_service(int $id)
    {
        if ($response = $this->guard('Edit Service')) return $response;
        $service = $this->supportModel->find('service', $id);
        if (! $service) throw PageNotFoundException::forPageNotFound('Service was not found.');
        return $this->render('admin/master/edit_service', ['page_title' => 'Edit Service', 'service' => $service] + $this->formLists(false));
    }

    public function update_service(int $id)
    {
        if ($response = $this->guard('Edit Service')) return $response;
        $old = $this->supportModel->find('service', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Service was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->serviceData();
        if ($this->supportModel->update('service', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/service')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_service()
    {
        if ($response = $this->guard('Delete Service')) return $response;
        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('service', $id);
        if ($old && $this->supportModel->delete('service', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/service')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/service')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_sample()
    {
        if ($response = $this->guard('Export Service')) return $response;
        $companies = []; foreach ($this->supportModel->show('company', 'ASC') as $item) $companies[$item->id] = $item->code;
        $vendors = []; foreach ($this->supportModel->show('vendor', 'ASC') as $item) $vendors[$item->id] = $item->code;
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['#','Service Name','Service Code','Company Code','Vendor Code','Address','Type','Service Type','Mode','Status','Created At']);
        foreach ($this->supportModel->show('service', 'DESC') as $i => $row) {
            $vendorCodes = [];
            foreach (array_filter(array_map('trim', explode(',', (string) ($row->vendor ?? '')))) as $vendorId) {
                if (isset($vendors[$vendorId])) $vendorCodes[] = $vendors[$vendorId];
            }
            fputcsv($stream, [$i + 1, $row->name ?? '', $row->code ?? '', $companies[$row->company ?? 0] ?? '', implode(',', $vendorCodes), $row->address ?? '', $row->type ?? '', $row->service_type ?? '', $row->mode ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive', $row->created_at ?? '']);
        }
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="Service_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_service()
    {
        if ($response = $this->guard('Add Service')) return $response;
        $path = ROOTPATH . 'assets/sample-import-service.csv';
        if (! is_file($path)) return redirect()->to('/admin/service')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_service()
    {
        if ($response = $this->guard('Add Service')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/service')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/service')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header);
        $inserted = $updated = $skipped = 0; $rowNumber = 1; $skipReasons = [];
        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($values) !== count($header)) { $skipped++; $skipReasons[] = "Row {$rowNumber}: column count mismatch"; continue; }
            $row = array_combine($header, $values);
            $code = trim((string) ($row['Service Code'] ?? ''));
            $companyCode = trim((string) ($row['Company Code'] ?? ''));
            $modeName = trim((string) ($row['Mode'] ?? ''));
            $company = $this->supportModel->search('company', ['code' => $companyCode]);
            $mode = $this->supportModel->search('mode', ['name' => $modeName]);
            if ($code === '') { $skipped++; $skipReasons[] = "Row {$rowNumber}: Service Code is empty"; continue; }
            if (! $company) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): Company Code '{$companyCode}' was not found"; continue; }
            if (! $mode) { $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): Mode '{$modeName}' was not found"; continue; }
            $vendorIds = []; $missingVendors = [];
            foreach (array_filter(array_map('trim', explode(',', (string) ($row['Vendor Code'] ?? '')))) as $vendorCode) {
                $vendor = $this->supportModel->search('vendor', ['code' => $vendorCode]);
                if ($vendor) $vendorIds[] = $vendor->id; else $missingVendors[] = $vendorCode;
            }
            if ($vendorIds === [] || $missingVendors !== []) {
                $skipped++; $skipReasons[] = "Row {$rowNumber} ({$code}): Vendor Code(s) not found: " . implode(', ', $missingVendors ?: ['none supplied']); continue;
            }
            $statusValue = trim((string) ($row['Status'] ?? ''));
            $data = [
                'name' => trim((string) ($row['Service Name'] ?? '')), 'code' => $code, 'company' => $company->id,
                'vendor' => implode(',', $vendorIds), 'address' => trim((string) ($row['Address'] ?? '')),
                'type' => trim((string) ($row['Type'] ?? '')), 'service_type' => trim((string) ($row['Service Type'] ?? '')),
                'mode' => $mode->name, 'status' => $statusValue === '1' || strcasecmp($statusValue, 'Active') === 0 ? 1 : 0,
            ];
            $existing = $this->supportModel->search('service', ['code' => $code]);
            if ($existing) {
                if ($this->supportModel->update('service', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++;
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                if ($this->supportModel->insert('service', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++;
            }
        }
        fclose($handle);
        $message = "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.";
        if ($skipReasons !== []) $message .= '<br><strong>Skipped reasons:</strong><br>' . implode('<br>', array_map('esc', $skipReasons));
        return redirect()->to('/admin/service')->with('error', $message)->with('error_class', $skipped > 0 ? 'alert-warning' : 'alert-success');
    }

    private function formLists(bool $activeVendorsOnly = true): array
    {
        return [
            'vendors' => $activeVendorsOnly ? $this->supportModel->show_condition('vendor', 'ASC', ['status' => 1]) : $this->supportModel->show('vendor', 'ASC'),
            'companies' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1]),
            'mode' => $this->supportModel->show_condition('mode', 'ASC', ['status' => 1]),
        ];
    }

    private function rules(): array
    {
        return ['name' => 'required', 'code' => 'required', 'company' => 'required|integer', 'type' => 'required', 'service_type' => 'required', 'mode' => 'required', 'vendor' => 'required', 'address' => 'required', 'status' => 'required|in_list[0,1]'];
    }

    private function serviceData(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')), 'code' => trim((string) $this->request->getPost('code')),
            'company' => (int) $this->request->getPost('company'), 'type' => trim((string) $this->request->getPost('type')),
            'service_type' => trim((string) $this->request->getPost('service_type')), 'mode' => trim((string) $this->request->getPost('mode')),
            'vendor' => implode(',', array_map('intval', (array) $this->request->getPost('vendor'))),
            'address' => trim((string) $this->request->getPost('address')), 'status' => (int) $this->request->getPost('status'),
        ];
    }

    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Service', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
