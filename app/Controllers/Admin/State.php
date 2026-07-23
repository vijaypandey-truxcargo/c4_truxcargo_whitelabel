<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class State extends Secure
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
        if ($response = $this->guard('View State')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $page = max(1, $page); $perPage = 10; $db = db_connect(); $total = $db->table('state')->countAllResults();
        $rows = $db->table('state')->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/state', ['page_title' => 'State', 'code' => $rows, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full')]);
    }

    public function add_state()
    {
        if ($response = $this->guard('Add State')) return $response;
        return $this->render('admin/master/add_state', ['page_title' => 'Add State']);
    }

    public function insert_state()
    {
        if ($response = $this->guard('Add State')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->stateData();
        if ($this->supportModel->insert('state', $data)) { $this->log('INSERT', null, $data); return redirect()->to('/admin/state')->with('error', 'Successfully Added.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_state(int $id)
    {
        if ($response = $this->guard('Edit State')) return $response;
        $data = $this->supportModel->find('state', $id); if (! $data) throw PageNotFoundException::forPageNotFound('State was not found.');
        return $this->render('admin/master/edit_state', ['page_title' => 'Edit State', 'data' => $data]);
    }

    public function update_state(int $id)
    {
        if ($response = $this->guard('Edit State')) return $response;
        $old = $this->supportModel->find('state', $id); if (! $old) throw PageNotFoundException::forPageNotFound('State was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->stateData();
        if ($this->supportModel->update('state', $data, $id)) { $this->log('UPDATE', (array) $old, $data); return redirect()->to('/admin/state')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_state()
    {
        if ($response = $this->guard('Delete State')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('state', $id);
        if ($old && $this->supportModel->delete('state', $id)) { $this->log('DELETE', (array) $old, null); return redirect()->to('/admin/state')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success'); }
        return redirect()->to('/admin/state')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_state()
    {
        if ($response = $this->guard('Export State')) return $response;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','State Name','State Code','Status']);
        foreach ($this->supportModel->show('state', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->states ?? '', $row->state_code ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="State_list_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_state()
    {
        if ($response = $this->guard('Import State')) return $response;
        $path = ROOTPATH . 'assets/sample-import-state.csv';
        if (! is_file($path)) return redirect()->to('/admin/state')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_state()
    {
        if ($response = $this->guard('Import State')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/state')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/state')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header); $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values); $name = trim((string) ($row['State Name'] ?? ''));
            if ($name === '') { $skipped++; continue; }
            $data = ['states' => $name, 'state_code' => trim((string) ($row['State Code'] ?? '')), 'status' => strcasecmp(trim((string) ($row['Status'] ?? '')), 'Active') === 0 ? 1 : 0];
            $existing = $this->supportModel->search('state', ['states' => $name]);
            if ($existing) {
                if ($this->supportModel->update('state', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++;
            } else {
                if ($this->supportModel->insert('state', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++;
            }
        }
        fclose($handle);
        return redirect()->to('/admin/state')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', 'alert-success');
    }

    private function rules(): array { return ['name' => 'required', 'state_code' => 'permit_empty|max_length[50]', 'status' => 'required|in_list[0,1]']; }
    private function stateData(): array { return ['states' => trim((string) $this->request->getPost('name')), 'state_code' => trim((string) $this->request->getPost('state_code')), 'status' => (int) $this->request->getPost('status')]; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'State', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
