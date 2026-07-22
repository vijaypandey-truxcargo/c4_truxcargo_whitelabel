<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Company extends Secure
{
    private function guard(string $permission)
    {
        if (! in_array($permission, $this->permission, true)) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'You do not have permission to perform this action.')
                ->with('error_class', 'alert-danger');
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
        if ($response = $this->guard('View Company')) return $response;

        $page = max(1, $page);
        $perPage = 10;
        $db = db_connect();
        $total = $db->table('company')->countAllResults();
        $companies = $db->table('company')->orderBy('id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)->get()->getResult();

        return $this->render('admin/master/company', [
            'page_title' => 'Company',
            'code' => $companies,
            'count' => ($page - 1) * $perPage,
            'links' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function add_company()
    {
        if ($response = $this->guard('Add Company')) return $response;
        return $this->render('admin/master/add_company', ['page_title' => 'Add Company']);
    }

    public function insert_company()
    {
        if ($response = $this->guard('Add Company')) return $response;
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger');
        }

        $data = $this->companyData();
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('company', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/company')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_company(int $id)
    {
        if ($response = $this->guard('Edit Company')) return $response;
        $company = $this->supportModel->find('company', $id);
        if (! $company) throw PageNotFoundException::forPageNotFound('Company was not found.');
        return $this->render('admin/master/edit_company', ['page_title' => 'Edit Company', 'data' => $company]);
    }

    public function update_company(int $id)
    {
        if ($response = $this->guard('Edit Company')) return $response;
        $old = $this->supportModel->find('company', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Company was not found.');
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger');
        }

        $data = $this->companyData();
        if ($this->supportModel->update('company', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/company')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_company()
    {
        if ($response = $this->guard('Delete Company')) return $response;
        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('company', $id);
        if ($old && $this->supportModel->delete('company', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/company')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/company')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_company()
    {
        if ($response = $this->guard('Export Company')) return $response;
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['#','Code','Name','Phone','Email','Address','Website','City','State','Pincode','Pan Number','Cin Number','Gst Number','SAC Code','Created At','Status']);
        foreach ($this->supportModel->show('company', 'DESC') as $i => $row) {
            fputcsv($stream, [$i + 1, $row->code ?? '', $row->name ?? '', $row->phone ?? '', $row->email ?? '', $row->address ?? '', $row->website ?? '', $row->city ?? '', $row->state ?? '', $row->pincode ?? '', $row->pan_number ?? '', $row->cin_number ?? '', $row->gst_number ?? '', $row->sac_code ?? '', $row->created_at ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        }
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="Company_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_company()
    {
        if ($response = $this->guard('Import Company')) return $response;
        $path = ROOTPATH . 'assets/sample-import-company.csv';
        if (! is_file($path)) return redirect()->to('/admin/company')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_company()
    {
        if ($response = $this->guard('Import Company')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') {
            return redirect()->to('/admin/company')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        }

        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/company')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($v) => trim((string) $v), $header);
        $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values);
            $code = trim((string) ($row['Code'] ?? ''));
            if ($code === '') { $skipped++; continue; }
            $data = [
                'code' => $code, 'name' => trim((string) ($row['Name'] ?? '')), 'website' => trim((string) ($row['Website'] ?? $row['website'] ?? '')),
                'email' => trim((string) ($row['Email'] ?? '')), 'phone' => trim((string) ($row['Phone'] ?? '')), 'address' => trim((string) ($row['Address'] ?? '')),
                'city' => trim((string) ($row['City'] ?? '')), 'state' => trim((string) ($row['State'] ?? $row['state'] ?? '')), 'pincode' => trim((string) ($row['Pincode'] ?? '')),
                'pan_number' => trim((string) ($row['Pan Number'] ?? '')), 'cin_number' => trim((string) ($row['Cin Number'] ?? '')), 'gst_number' => trim((string) ($row['Gst Number'] ?? '')),
                'sac_code' => trim((string) ($row['SAC Code'] ?? '')), 'status' => strcasecmp(trim((string) ($row['Status'] ?? '')), 'Active') === 0 ? 1 : 0,
            ];
            $existing = $this->supportModel->search('company', ['code' => $code]);
            if ($existing) { $this->supportModel->update('company', $data, $existing->id); $updated++; }
            else { $data['created_at'] = date('Y-m-d H:i:s'); $this->supportModel->insert('company', $data); $inserted++; }
        }
        fclose($handle);
        return redirect()->to('/admin/company')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', 'alert-success');
    }

    private function rules(): array
    {
        return ['code' => 'required', 'name' => 'required', 'website' => 'required', 'email' => 'required|valid_email', 'phone' => 'required', 'state' => 'required', 'pincode' => 'required|exact_length[6]|numeric', 'pan_number' => 'required', 'cin_number' => 'required', 'gst_number' => 'required', 'sac_code' => 'required', 'city' => 'required', 'status' => 'required|in_list[0,1]'];
    }

    private function companyData(): array
    {
        $fields = ['code','name','website','email','phone','state','pincode','pan_number','cin_number','gst_number','sac_code','city','address'];
        $data = [];
        foreach ($fields as $field) $data[$field] = trim((string) $this->request->getPost($field));
        $data['status'] = (int) $this->request->getPost('status');
        $data['updated_by'] = (int) session()->get('admin_login_id');
        return $data;
    }

    private function log(string $action, ?array $old, ?array $new): void
    {
        $admin = session()->get('admin_user');
        $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Company', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]);
    }
}
