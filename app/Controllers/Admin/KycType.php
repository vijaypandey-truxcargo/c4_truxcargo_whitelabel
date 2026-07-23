<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class KycType extends Secure
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
        if ($response = $this->guard('View KYC')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $page = max(1, $page); $perPage = 10; $db = db_connect();
        $total = $db->table('kyc_type')->countAllResults();
        $rows = $db->table('kyc_type')->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/kyc_type', ['page_title' => 'KYC Type', 'code' => $rows, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full')]);
    }

    public function add_kyc()
    {
        if ($response = $this->guard('Add KYC')) return $response;
        return $this->render('admin/master/add_kyc', ['page_title' => 'Add KYC Type']);
    }

    public function insert_kyc()
    {
        if ($response = $this->guard('Add KYC')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->kycData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('kyc_type', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/kycType')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_kyc(int $id)
    {
        if ($response = $this->guard('Edit KYC')) return $response;
        $data = $this->supportModel->find('kyc_type', $id);
        if (! $data) throw PageNotFoundException::forPageNotFound('KYC type was not found.');
        return $this->render('admin/master/edit_kyc', ['page_title' => 'Edit KYC Type', 'data' => $data]);
    }

    public function update_kyc(int $id)
    {
        if ($response = $this->guard('Edit KYC')) return $response;
        $old = $this->supportModel->find('kyc_type', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('KYC type was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->kycData(); $data['updated_by'] = (int) session()->get('admin_login_id');
        if ($this->supportModel->update('kyc_type', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/kycType')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_kyc()
    {
        if ($response = $this->guard('Delete KYC')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('kyc_type', $id);
        if (! $old) return redirect()->to('/admin/kycType')->with('error', 'KYC type was not found.')->with('error_class', 'alert-danger');
        if ($this->supportModel->getRows('users', ['kyc_type' => $id]) > 0) return redirect()->to('/admin/kycType')->with('error', 'KYC type is assigned to users and cannot be deleted.')->with('error_class', 'alert-danger');
        if ($this->supportModel->delete('kyc_type', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/kycType')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/kycType')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_kyc()
    {
        if ($response = $this->guard('Export KYC')) return $response;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','Kyc Name','Created At','Status']);
        foreach ($this->supportModel->show('kyc_type', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->name ?? '', $row->created_at ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="Kyc_list_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_kyc()
    {
        if ($response = $this->guard('Import KYC')) return $response;
        $path = ROOTPATH . 'assets/sample-import-kyc.csv';
        if (! is_file($path)) return redirect()->to('/admin/kycType')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_kyc()
    {
        if ($response = $this->guard('Import KYC')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/kycType')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/kycType')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header); $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values); $name = trim((string) ($row['Kyc Name'] ?? $row['Name'] ?? ''));
            if ($name === '') { $skipped++; continue; }
            $data = ['name' => $name, 'status' => strcasecmp(trim((string) ($row['Status'] ?? '')), 'Active') === 0 ? 1 : 0];
            $existing = $this->supportModel->search('kyc_type', ['name' => $name]);
            if ($existing) {
                if ($this->supportModel->update('kyc_type', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++;
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                if ($this->supportModel->insert('kyc_type', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++;
            }
        }
        fclose($handle);
        return redirect()->to('/admin/kycType')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', 'alert-success');
    }

    private function rules(): array { return ['name' => 'required', 'status' => 'required|in_list[0,1]']; }
    private function kycData(): array { return ['name' => trim((string) $this->request->getPost('name')), 'status' => (int) $this->request->getPost('status')]; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'KYC Type', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
