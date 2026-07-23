<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Mode extends Secure
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
        if ($response = $this->guard('View Mode')) return $response;
        $page = max(1, $page); $perPage = 10; $db = db_connect(); $total = $db->table('mode')->countAllResults();
        $rows = $db->table('mode')->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        return $this->render('admin/master/mode', ['page_title' => 'Mode', 'code' => $rows, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total)]);
    }

    public function add_mode()
    {
        if ($response = $this->guard('Add Mode')) return $response;
        return $this->render('admin/master/add_mode', ['page_title' => 'Add Mode']);
    }

    public function insert_mode()
    {
        if ($response = $this->guard('Add Mode')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->modeData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('mode', $data)) { $this->log('INSERT', null, $data); return redirect()->to('/admin/mode')->with('error', 'Successfully Added.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_mode(int $id)
    {
        if ($response = $this->guard('Edit Mode')) return $response;
        $data = $this->supportModel->find('mode', $id); if (! $data) throw PageNotFoundException::forPageNotFound('Mode was not found.');
        return $this->render('admin/master/edit_mode', ['page_title' => 'Edit Mode', 'data' => $data]);
    }

    public function update_mode(int $id)
    {
        if ($response = $this->guard('Edit Mode')) return $response;
        $old = $this->supportModel->find('mode', $id); if (! $old) throw PageNotFoundException::forPageNotFound('Mode was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->modeData(); $data['updated_by'] = (int) session()->get('admin_login_id');
        if ($this->supportModel->update('mode', $data, $id)) { $this->log('UPDATE', (array) $old, $data); return redirect()->to('/admin/mode')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_mode()
    {
        if ($response = $this->guard('Delete Mode')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('mode', $id);
        if ($old && $this->supportModel->delete('mode', $id)) { $this->log('DELETE', (array) $old, null); return redirect()->to('/admin/mode')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success'); }
        return redirect()->to('/admin/mode')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_mode()
    {
        if ($response = $this->guard('Export Mode')) return $response;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','Name','Created At','Status']);
        foreach ($this->supportModel->show('mode', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->name ?? '', $row->created_at ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="mode_list_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_mode()
    {
        if ($response = $this->guard('Import Mode')) return $response;
        $path = ROOTPATH . 'assets/sample-import-mode.csv';
        if (! is_file($path)) return redirect()->to('/admin/mode')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_mode()
    {
        if ($response = $this->guard('Import Mode')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/mode')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/mode')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header); $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values); $name = trim((string) ($row['Name'] ?? '')); if ($name === '') { $skipped++; continue; }
            $statusValue = trim((string) ($row['Status'] ?? '')); $data = ['name' => $name, 'status' => $statusValue === '1' || strcasecmp($statusValue, 'Active') === 0 ? 1 : 0];
            $existing = $this->supportModel->search('mode', ['name' => $name]);
            if ($existing) { if ($this->supportModel->update('mode', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++; }
            else { $data['created_at'] = date('Y-m-d H:i:s'); if ($this->supportModel->insert('mode', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++; }
        }
        fclose($handle);
        return redirect()->to('/admin/mode')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', $skipped > 0 ? 'alert-warning' : 'alert-success');
    }

    private function rules(): array { return ['name' => 'required', 'status' => 'required|in_list[0,1]']; }
    private function modeData(): array { return ['name' => trim((string) $this->request->getPost('name')), 'status' => (int) $this->request->getPost('status')]; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Mode', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
