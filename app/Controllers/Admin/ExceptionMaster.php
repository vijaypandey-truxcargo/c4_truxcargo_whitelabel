<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class ExceptionMaster extends Secure
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
        if ($response = $this->guard('View Exception Master')) return $response;
        $page = (int)($this->request->getGet('page') ?? $page); $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set(['exception_status_code' => trim((string)$this->request->getPost('status_code')), 'exception_desc' => trim((string)$this->request->getPost('desc')), 'exception_is_ndr' => (string)$this->request->getPost('is_ndr')]); $page = 1;
        }
        $statusCode = (string)$session->get('exception_status_code'); $desc = (string)$session->get('exception_desc'); $isNdr = (string)$session->get('exception_is_ndr');
        $filter = static function ($builder) use ($statusCode, $desc, $isNdr): void {
            if ($statusCode !== '') $builder->like('status_code', $statusCode);
            if ($desc !== '') $builder->like('desc', $desc);
            if ($isNdr !== '') $builder->where('is_ndr', $isNdr);
        };
        $db = db_connect(); $counter = $db->table('exception_master'); $filter($counter); $total = $counter->countAllResults();
        $page = max(1, $page); $perPage = 10; $builder = $db->table('exception_master'); $filter($builder);
        return $this->render('admin/exception_master', ['page_title' => 'Exception Master', 'code' => $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult(), 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full'), 'selected_status_code' => $statusCode, 'selected_desc' => $desc, 'selected_is_ndr' => $isNdr]);
    }

    public function add()
    {
        if ($response = $this->guard('Add Exception Master')) return $response;
        return $this->render('admin/add_exception_master', ['page_title' => 'Add Exception Master']);
    }

    public function insert()
    {
        if ($response = $this->guard('Add Exception Master')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->supportModel->search('exception_master', ['status_code' => $data['status_code']])) return redirect()->back()->withInput()->with('error', 'Status code already exists.')->with('error_class', 'alert-warning');
        if ($this->supportModel->insert('exception_master', $data)) { $this->log('INSERT', null, $data); return redirect()->to('/admin/exceptionMaster')->with('error', 'Successfully Added.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit(int $id)
    {
        if ($response = $this->guard('Edit Exception Master')) return $response;
        $row = $this->supportModel->find('exception_master', $id);
        if (! $row) throw PageNotFoundException::forPageNotFound('Exception Master was not found.');
        return $this->render('admin/edit_exception_master', ['page_title' => 'Edit Exception Master', 'exception' => $row]);
    }

    public function update(int $id)
    {
        if ($response = $this->guard('Edit Exception Master')) return $response;
        $old = $this->supportModel->find('exception_master', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Exception Master was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['updated_at'] = date('Y-m-d H:i:s'); $data['updated_by'] = (int)session()->get('admin_login_id');
        $duplicate = db_connect()->table('exception_master')->where('status_code', $data['status_code'])->where('id !=', $id)->countAllResults();
        if ($duplicate) return redirect()->back()->withInput()->with('error', 'Status code already exists.')->with('error_class', 'alert-warning');
        if ($this->supportModel->update('exception_master', $data, $id)) { $this->log('UPDATE', (array)$old, $data); return redirect()->to('/admin/exceptionMaster')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success'); }
        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete()
    {
        if ($response = $this->guard('Delete Exception Master')) return $response;
        $id = (int)$this->request->getPost('id'); $old = $this->supportModel->find('exception_master', $id);
        if ($old && $this->supportModel->delete('exception_master', $id)) { $this->log('DELETE', (array)$old, null); return redirect()->to('/admin/exceptionMaster')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success'); }
        return redirect()->to('/admin/exceptionMaster')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function import()
    {
        if ($response = $this->guard('Import Exception Master')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/exceptionMaster')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $header = fgetcsv($handle);
        if (! $header) return redirect()->to('/admin/exceptionMaster')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(fn($v) => trim((string)$v, " \t\n\r\0\x0B\xEF\xBB\xBF"), $header); $inserted = $updated = $skipped = 0; $reasons = []; $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++; if (! array_filter($values, fn($v) => trim((string)$v) !== '')) continue;
            if (count($values) !== count($header)) { $skipped++; $reasons[] = "Row {$line}: column count mismatch"; continue; }
            $row = array_combine($header, array_map('trim', $values)); $code = $row['Status Code'] ?? '';
            if ($code === '' || ($row['Description'] ?? '') === '') { $skipped++; $reasons[] = "Row {$line}: Status Code or Description is empty"; continue; }
            $data = ['status_code' => $code, 'status_type' => $row['Status Type'] ?? '', 'code_type' => $row['Code Type'] ?? '', 'desc' => $row['Description'], 'is_ndr' => strcasecmp($row['Add In NDR'] ?? '', 'Yes') === 0 ? 1 : 0, 'create_ticket' => strcasecmp($row['Create Ticket'] ?? '', 'Yes') === 0 ? 1 : 0, 'ticket_department' => $row['Ticket Department'] ?? '', 'ticket_priority' => $row['Ticket Priority'] ?? '', 'status' => strcasecmp($row['Status'] ?? '', 'Active') === 0 || ($row['Status'] ?? '') === '1' ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => (int)session()->get('admin_login_id')];
            if (! $data['create_ticket']) { $data['ticket_department'] = ''; $data['ticket_priority'] = ''; }
            $existing = $this->supportModel->search('exception_master', ['status_code' => $code]);
            if ($existing) { if ($this->supportModel->update('exception_master', $data, $existing->id)) { $updated++; $this->log('UPDATE WHILE IMPORT', (array)$existing, $data); } else { $skipped++; $reasons[] = "Row {$line}: update failed"; } }
            else { $data['created_at'] = date('Y-m-d H:i:s'); if ($this->supportModel->insert('exception_master', $data)) { $inserted++; $this->log('INSERT WHILE IMPORT', null, $data); } else { $skipped++; $reasons[] = "Row {$line}: insert failed"; } }
        }
        fclose($handle); $message = "Import complete. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}.";
        if ($reasons) $message .= '<br><strong>Skipped reasons:</strong><br>' . implode('<br>', array_map('esc', $reasons));
        return redirect()->to('/admin/exceptionMaster')->with('error', $message)->with('error_class', $skipped ? 'alert-warning' : 'alert-success');
    }

    public function export_sample_exception_master()
    {
        if ($response = $this->guard('Import Exception Master')) return $response;
        $path = ROOTPATH . 'assets/sample-import-exception-master.csv';
        if (! is_file($path)) return redirect()->to('/admin/exceptionMaster')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function export_all()
    {
        if ($response = $this->guard('Export Exception Master')) return $response;
        $stream = fopen('php://temp', 'w+'); fputcsv($stream, ['Status Code','Status Type','Code Type','Description','Add In NDR','Create Ticket','Ticket Department','Ticket Priority','Status']);
        foreach ($this->supportModel->show('exception_master', 'DESC') as $row) fputcsv($stream, [$row->status_code, $row->status_type, $row->code_type, $row->desc, $row->is_ndr ? 'Yes' : 'No', $row->create_ticket ? 'Yes' : 'No', $row->ticket_department, $row->ticket_priority, $row->status ? 'Active' : 'Inactive']);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="ExceptionMaster_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    private function rules(): array { return ['status_code' => 'required|max_length[50]', 'status_type' => 'required|max_length[50]', 'code_type' => 'required|max_length[50]', 'desc' => 'required', 'status' => 'required|in_list[0,1]']; }
    private function formData(): array { $ticket = $this->request->getPost('create_ticket') ? 1 : 0; return ['status_code' => trim((string)$this->request->getPost('status_code')), 'status_type' => trim((string)$this->request->getPost('status_type')), 'code_type' => trim((string)$this->request->getPost('code_type')), 'desc' => trim((string)$this->request->getPost('desc')), 'is_ndr' => $this->request->getPost('is_ndr') ? 1 : 0, 'create_ticket' => $ticket, 'ticket_department' => $ticket ? trim((string)$this->request->getPost('ticket_department')) : '', 'ticket_priority' => $ticket ? trim((string)$this->request->getPost('ticket_priority')) : '', 'status' => (int)$this->request->getPost('status')]; }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Exception Master', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
