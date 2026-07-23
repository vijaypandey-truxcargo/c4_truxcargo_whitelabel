<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Master extends Secure
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

    public function hub_masters(int $page = 1)
    {
        if ($response = $this->guard('View Hub')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set(['filter_hub_code' => trim((string) $this->request->getPost('code')), 'filter_hub_status' => (string) $this->request->getPost('status')]);
            $page = 1;
        }
        $code = (string) $session->get('filter_hub_code');
        $status = (string) $session->get('filter_hub_status');
        $db = db_connect();
        $filter = static function ($builder) use ($code, $status): void {
            if ($code !== '' && $code !== 'All') $builder->where('code', $code);
            if ($status !== '' && $status !== 'All') $builder->where('status', $status);
        };
        $countBuilder = $db->table('hub'); $filter($countBuilder); $total = $countBuilder->countAllResults();
        $page = max(1, $page); $perPage = 10;
        $builder = $db->table('hub'); $filter($builder);
        $rows = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/hub', [
            'page_title' => 'Hub Master', 'code' => $rows, 'count' => ($page - 1) * $perPage,
            'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full'), 'hub_list' => $this->supportModel->show('hub', 'ASC'),
            'selected_code' => $code, 'selected_status' => $status,
        ]);
    }

    public function add_hub()
    {
        if ($response = $this->guard('Add Hub')) return $response;
        return $this->render('admin/master/add_hub', ['page_title' => 'Add Hub', 'data' => $this->supportModel->show('hub', 'ASC'), 'companies' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1])]);
    }

    public function insert_hub()
    {
        if ($response = $this->guard('Add Hub')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->hubData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('hub', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/master/hub_masters')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_hub(int $id)
    {
        if ($response = $this->guard('Edit Hub')) return $response;
        $hub = $this->supportModel->find('hub', $id);
        if (! $hub) throw PageNotFoundException::forPageNotFound('Hub was not found.');
        $hub->company_id = $hub->company;
        return $this->render('admin/master/edit_hub', ['page_title' => 'Edit Hub', 'data' => $hub, 'companies' => $this->supportModel->show_condition('company', 'ASC', ['status' => 1])]);
    }

    public function update_hub(int $id)
    {
        if ($response = $this->guard('Edit Hub')) return $response;
        $old = $this->supportModel->find('hub', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Hub was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->hubData();
        if ($this->supportModel->update('hub', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/master/hub_masters')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_hub()
    {
        if ($response = $this->guard('Delete Hub')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('hub', $id);
        if ($old && $this->supportModel->delete('hub', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/master/hub_masters')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/master/hub_masters')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_hub()
    {
        if ($response = $this->guard('Export Hub')) return $response;
        $companies = []; foreach ($this->supportModel->show('company', 'ASC') as $company) $companies[$company->id] = $company->code;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','Code','Type','Name','Phone','Email','Company Code','Address','Created At','Status']);
        foreach ($this->supportModel->show('hub', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->code ?? '', $row->type ?? '', $row->name ?? '', $row->phone ?? '', $row->email_id ?? '', $companies[$row->company ?? 0] ?? '', $row->address ?? '', $row->created_at ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="Hub_Master_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_hub()
    {
        if ($response = $this->guard('Import Hub')) return $response;
        $path = ROOTPATH . 'assets/sample-import-hub.csv';
        if (! is_file($path)) return redirect()->to('/admin/master/hub_masters')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_hub()
    {
        if ($response = $this->guard('Import Hub')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/master/hub_masters')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/master/hub_masters')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($v) => trim((string) $v), $header); $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values); $hubCode = trim((string) ($row['Code'] ?? '')); $companyCode = trim((string) ($row['Company Code'] ?? ''));
            $company = $this->supportModel->search('company', ['code' => $companyCode]);
            if ($hubCode === '' || ! $company || ($company->status ?? 0) != 1) { $skipped++; continue; }
            $data = ['code' => $hubCode, 'name' => trim((string) ($row['Name'] ?? '')), 'type' => trim((string) ($row['Type'] ?? '')), 'email_id' => trim((string) ($row['Email'] ?? '')), 'company' => $company->id, 'address' => trim((string) ($row['Address'] ?? '')), 'phone' => trim((string) ($row['Phone'] ?? '')), 'status' => strcasecmp(trim((string) ($row['Status'] ?? '')), 'Active') === 0 ? 1 : 0, 'updated_by' => (int) session()->get('admin_login_id')];
            $existing = $this->supportModel->search('hub', ['code' => $hubCode]);
            if ($existing) {
                if ($this->supportModel->update('hub', $data, $existing->id)) {
                    $this->log('UPDATE WHILE IMPORT', (array) $existing, $data);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                if ($this->supportModel->insert('hub', $data)) {
                    $this->log('INSERT WHILE IMPORT', null, $data);
                    $inserted++;
                } else {
                    $skipped++;
                }
            }
        }
        fclose($handle);
        return redirect()->to('/admin/master/hub_masters')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', 'alert-success');
    }

    private function rules(): array
    {
        return ['code' => 'required', 'name' => 'required', 'type' => 'required', 'phone' => 'required', 'email_id' => 'required|valid_email', 'company' => 'required|integer', 'status' => 'required|in_list[0,1]'];
    }

    private function hubData(): array
    {
        $data = []; foreach (['code','name','type','email_id','phone','address'] as $field) $data[$field] = trim((string) $this->request->getPost($field));
        $data['company'] = (int) $this->request->getPost('company'); $data['status'] = (int) $this->request->getPost('status'); $data['updated_by'] = (int) session()->get('admin_login_id'); return $data;
    }

    private function validationRedirect()
    {
        return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger');
    }

    private function log(string $action, ?array $old, ?array $new): void
    {
        $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Hub', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]);
    }
}
