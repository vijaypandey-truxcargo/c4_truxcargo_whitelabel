<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Role extends Secure
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
        if ($response = $this->guard('View Role')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $page = max(1, $page); $perPage = 10; $db = db_connect();
        $total = $db->table('role')->countAllResults();
        $roles = $db->table('role')->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        $roleNames = []; foreach ($this->supportModel->show('role', 'ASC') as $role) $roleNames[$role->id] = $role->name;
        return $this->render('admin/master/role', ['page_title' => 'Role', 'code' => $roles, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full'), 'role_names' => $roleNames]);
    }

    public function add_role()
    {
        if ($response = $this->guard('Add Role')) return $response;
        return $this->render('admin/master/add_role', ['page_title' => 'Add Role', 'role' => $this->availableParents()]);
    }

    public function insert_role()
    {
        if ($response = $this->guard('Add Role')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->roleData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('role', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/role')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_role(int $id)
    {
        if ($response = $this->guard('Edit Role')) return $response;
        $data = $this->supportModel->find('role', $id);
        if (! $data) throw PageNotFoundException::forPageNotFound('Role was not found.');
        return $this->render('admin/master/edit_role', ['page_title' => 'Edit Role', 'data' => $data, 'role' => $this->availableParents($id)]);
    }

    public function update_role(int $id)
    {
        if ($response = $this->guard('Edit Role')) return $response;
        $old = $this->supportModel->find('role', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Role was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->roleData();
        if ((int) ($data['parent_id'] ?? 0) === $id || in_array((int) ($data['parent_id'] ?? 0), $this->descendantIds($id), true)) {
            return redirect()->back()->withInput()->with('error', 'A role cannot use itself or its child role as parent.')->with('error_class', 'alert-danger');
        }
        $data['updated_by'] = (int) session()->get('admin_login_id');
        if ($this->supportModel->update('role', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/role')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete_role()
    {
        if ($response = $this->guard('Delete Role')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('role', $id);
        if (! $old) return redirect()->to('/admin/role')->with('error', 'Role was not found.')->with('error_class', 'alert-danger');
        if ($this->supportModel->getRows('role', ['parent_id' => $id]) > 0 || $this->supportModel->getRows('users', ['role' => $id]) > 0) {
            return redirect()->to('/admin/role')->with('error', 'Role is assigned to users or child roles and cannot be deleted.')->with('error_class', 'alert-danger');
        }
        if ($this->supportModel->delete('role', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/role')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/role')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function export_ticket_role()
    {
        if ($response = $this->guard('Export Role')) return $response;
        $names = []; foreach ($this->supportModel->show('role', 'ASC') as $role) $names[$role->id] = $role->name;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['#','Name','Parent Role','Created At','Status']);
        foreach ($this->supportModel->show('role', 'DESC') as $i => $row) fputcsv($stream, [$i + 1, $row->name ?? '', $names[$row->parent_id ?? 0] ?? 'TOP LEVEL', $row->created_at ?? '', ($row->status ?? 0) == 1 ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="Role_list_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function export_sample_role()
    {
        if ($response = $this->guard('Import Role')) return $response;
        $path = ROOTPATH . 'assets/sample-import-role.csv';
        if (! is_file($path)) return redirect()->to('/admin/role')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function import_role()
    {
        if ($response = $this->guard('Import Role')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/role')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/role')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(static fn ($value) => trim((string) $value), $header); $inserted = $updated = $skipped = 0;
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($header)) { $skipped++; continue; }
            $row = array_combine($header, $values); $name = strtoupper(trim((string) ($row['Name'] ?? '')));
            if ($name === '') { $skipped++; continue; }
            $data = ['name' => $name, 'status' => strcasecmp(trim((string) ($row['Status'] ?? '')), 'Active') === 0 ? 1 : 0];
            $existing = $this->supportModel->search('role', ['name' => $name]);
            if ($existing) {
                if ($this->supportModel->update('role', $data, $existing->id)) { $this->log('UPDATE WHILE IMPORT', (array) $existing, $data); $updated++; } else $skipped++;
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                if ($this->supportModel->insert('role', $data)) { $this->log('INSERT WHILE IMPORT', null, $data); $inserted++; } else $skipped++;
            }
        }
        fclose($handle);
        return redirect()->to('/admin/role')->with('error', "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.")->with('error_class', 'alert-success');
    }

    private function availableParents(?int $editingId = null): array
    {
        $roles = $this->supportModel->show_condition('role', 'ASC', ['status' => 1]);
        if ($editingId === null) return $roles;
        $blocked = array_merge([$editingId], $this->descendantIds($editingId));
        return array_values(array_filter($roles, static fn ($role) => ! in_array((int) $role->id, $blocked, true)));
    }

    private function descendantIds(int $parentId): array
    {
        $ids = [];
        $children = db_connect()->table('role')->where('parent_id', $parentId)->orderBy('id', 'ASC')->get()->getResult();
        foreach ($children as $role) {
            $ids[] = (int) $role->id; $ids = array_merge($ids, $this->descendantIds((int) $role->id));
        }
        return array_values(array_unique($ids));
    }

    private function rules(): array { return ['name' => 'required', 'parent_id' => 'permit_empty|integer', 'status' => 'required|in_list[0,1]']; }
    private function roleData(): array { $parent = $this->request->getPost('parent_id'); return ['name' => strtoupper(trim((string) $this->request->getPost('name'))), 'parent_id' => $parent === null || $parent === '' ? null : (int) $parent, 'status' => (int) $this->request->getPost('status')]; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Role', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
