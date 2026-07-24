<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class RateModifier extends Secure
{
    private const RATE_MODES = [1 => 'RATE PER PCS', 2 => 'RATE PER KG', 3 => 'RATE PER HALF KG', 4 => 'RATE MIN PER BOX WEIGHT', 5 => 'RATE PER UNIT TYPE', 6 => 'RATE PER TOTAL AWB', 7 => 'RATE PER TOTAL BAGS', 8 => 'RATE MONTHLY TOTAL BAGS'];

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
        if ($response = $this->guard('View Rate Modifier')) return $response;
        $page = (int) ($this->request->getGet('page') ?? $page);
        $session = session();
        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set(['filter_rm_charge' => (string) $this->request->getPost('charge_id'), 'filter_rm_billing' => (string) $this->request->getPost('billing_type'), 'filter_rm_status' => (string) $this->request->getPost('status'), 'filter_rm_service' => (string) $this->request->getPost('service')]);
            $page = 1;
        }
        $charge = (string) $session->get('filter_rm_charge'); $billing = (string) $session->get('filter_rm_billing');
        $status = (string) $session->get('filter_rm_status'); $service = (string) $session->get('filter_rm_service');
        $filter = static function ($builder) use ($charge, $billing, $status, $service): void {
            if ($charge !== '' && $charge !== 'All') $builder->where('charge_id', $charge);
            if ($billing !== '' && $billing !== 'All') $builder->where('billing_type', $billing);
            if ($status !== '' && $status !== 'All') $builder->where('status', $status);
            if ($service !== '' && $service !== 'All') $builder->where('FIND_IN_SET(' . db_connect()->escape($service) . ', service_id) >', 0, false);
        };
        $db = db_connect(); $countBuilder = $db->table('rate_modifier'); $filter($countBuilder); $total = $countBuilder->countAllResults();
        $page = max(1, $page); $perPage = 10; $builder = $db->table('rate_modifier'); $filter($builder);
        $rows = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        $charges = $this->supportModel->show('charge', 'ASC'); $services = $this->supportModel->show('service', 'ASC');
        return $this->render('admin/rate_modifier', ['page_title' => 'Rate Modifier', 'code' => $rows, 'charge_list' => $charges, 'service_list' => $services, 'charge_map' => $this->map($charges, 'name'), 'service_map' => $this->map($services, 'name'), 'selected_charge' => $charge, 'selected_billing' => $billing, 'selected_status' => $status, 'selected_service' => $service, 'count' => ($page - 1) * $perPage, 'links' => service('pager')->makeLinks($page, $perPage, $total, 'admin_full')]);
    }

    public function add()
    {
        if ($response = $this->guard('Add Rate Modifier')) return $response;
        return $this->render('admin/rate_modifier_edit', ['page_title' => 'Add Rate Modifier', 'row' => null] + $this->formLists());
    }

    public function insert()
    {
        if ($response = $this->guard('Add Rate Modifier')) return $response;
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['created_date'] = date('Y-m-d H:i:s');
        if ($this->supportModel->insert('rate_modifier', $data)) {
            $this->log('INSERT', null, $data);
            return redirect()->to('/admin/RateModifier')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit(int $id)
    {
        if ($response = $this->guard('Edit Rate Modifier')) return $response;
        $row = $this->supportModel->find('rate_modifier', $id);
        if (! $row) throw PageNotFoundException::forPageNotFound('Rate Modifier was not found.');
        return $this->render('admin/rate_modifier_edit', ['page_title' => 'Edit Rate Modifier', 'row' => $row] + $this->formLists());
    }

    public function update(int $id)
    {
        if ($response = $this->guard('Edit Rate Modifier')) return $response;
        $old = $this->supportModel->find('rate_modifier', $id);
        if (! $old) throw PageNotFoundException::forPageNotFound('Rate Modifier was not found.');
        if (! $this->validate($this->rules())) return $this->validationRedirect();
        $data = $this->formData(); $data['updated_date'] = date('Y-m-d H:i:s');
        if ($this->supportModel->update('rate_modifier', $data, $id)) {
            $this->log('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/RateModifier')->with('error', 'Rate Modifier updated successfully.')->with('error_class', 'alert-success');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update Rate Modifier.')->with('error_class', 'alert-danger');
    }

    public function delete()
    {
        if ($response = $this->guard('Delete Rate Modifier')) return $response;
        $id = (int) $this->request->getPost('id'); $old = $this->supportModel->find('rate_modifier', $id);
        if ($old && $this->supportModel->delete('rate_modifier', $id)) {
            $this->log('DELETE', (array) $old, null);
            return redirect()->to('/admin/RateModifier')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }
        return redirect()->to('/admin/RateModifier')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function view_ajax(int $id)
    {
        if ($response = $this->guard('View Rate Modifier')) return $response;
        $row = $this->supportModel->find('rate_modifier', $id);
        if (! $row) return $this->response->setStatusCode(404)->setBody('Rate Modifier was not found.');
        $charges = $this->map($this->supportModel->show('charge'), 'name'); $services = $this->map($this->supportModel->show('service'), 'name');
        $serviceNames = []; foreach ($this->ids($row->service_id ?? '') as $sid) $serviceNames[] = $services[$sid] ?? $sid;
        $fields = ['Charge' => $charges[$row->charge_id] ?? 'N/A', 'Billing Type' => $row->billing_type, 'Fixed Amount' => $row->fixed_amount, 'Minimum Amount' => $row->min_amount, 'Rate Mode' => self::RATE_MODES[$row->rate_mod] ?? 'N/A', 'Services' => implode(', ', $serviceNames), 'Effective From' => $row->effective_from, 'Effective To' => $row->effective_to, 'Status' => $row->status ? 'Active' : 'Inactive'];
        $html = '<table class="table table-bordered">'; foreach ($fields as $label => $value) $html .= '<tr><th>' . esc($label) . '</th><td>' . esc((string) $value) . '</td></tr>';
        return $this->response->setBody($html . '</table>');
    }

    public function export_sample_rate()
    {
        if ($response = $this->guard('Import Rate Modifier')) return $response;
        $path = ROOTPATH . 'assets/sample-rate-modifier.csv';
        if (! is_file($path)) return redirect()->to('/admin/RateModifier')->with('error', 'Sample file was not found.')->with('error_class', 'alert-danger');
        return $this->response->download($path, null);
    }

    public function export_all()
    {
        if ($response = $this->guard('Export Rate Modifier')) return $response;
        $charges = $this->map($this->supportModel->show('charge'), 'name'); $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['Charge Name','Billing Type','Fixed Amount','Min Amount','Rate Mode','Service IDs','Status','Effective From','Effective To']);
        foreach ($this->supportModel->show('rate_modifier', 'DESC') as $row) fputcsv($stream, [$charges[$row->charge_id] ?? '', $row->billing_type, $row->fixed_amount, $row->min_amount, self::RATE_MODES[$row->rate_mod] ?? '', $row->service_id, $row->status ? 'Active' : 'Inactive', $row->effective_from, $row->effective_to]);
        rewind($stream); $csv = stream_get_contents($stream); fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv')->setHeader('Content-Disposition', 'attachment; filename="Rate_Modifier_' . date('Ymd_His') . '.csv"')->setBody($csv);
    }

    public function import()
    {
        if ($response = $this->guard('Import Rate Modifier')) return $response;
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getExtension()) !== 'csv') return redirect()->to('/admin/RateModifier')->with('error', 'Please upload a valid CSV file.')->with('error_class', 'alert-danger');
        $handle = fopen($file->getTempName(), 'r'); $rawHeader = fgetcsv($handle);
        if (! $rawHeader) return redirect()->to('/admin/RateModifier')->with('error', 'CSV file is empty.')->with('error_class', 'alert-danger');
        $header = array_map(fn ($v) => strtolower(preg_replace('/\s+/', '_', trim((string) $v, " \t\n\r\0\x0B\xEF\xBB\xBF"))), $rawHeader);
        $inserted = $skipped = 0; $reasons = []; $line = 1; $modeMap = array_flip(self::RATE_MODES);
        while (($values = fgetcsv($handle)) !== false) {
            $line++; if (! array_filter($values, fn ($v) => trim((string) $v) !== '')) continue;
            if (count($values) !== count($header)) { $skipped++; $reasons[] = "Row {$line}: column count mismatch"; continue; }
            $row = array_combine($header, array_map('trim', $values)); $charge = $this->supportModel->search('charge', ['name' => $row['charge_name'] ?? '']); $modeText = strtoupper((string) ($row['rate_mode'] ?? ''));
            if (! $charge) { $skipped++; $reasons[] = "Row {$line}: Charge '{$row['charge_name']}' not found"; continue; }
            if (! isset($modeMap[$modeText])) { $skipped++; $reasons[] = "Row {$line}: invalid Rate Mode '{$modeText}'"; continue; }
            if (! $this->validDate($row['effective_from'] ?? '')) { $skipped++; $reasons[] = "Row {$line}: Effective From must be DD-MM-YYYY"; continue; }
            $from = \DateTime::createFromFormat('d-m-Y', $row['effective_from']);
            $to = $this->validDate($row['effective_to'] ?? '') ? \DateTime::createFromFormat('d-m-Y', $row['effective_to'])->format('Y-m-d') : null;
            $data = ['charge_id' => $charge->id, 'billing_type' => strtolower($row['billing_type'] ?? 'sale'), 'fixed_amount' => (float) ($row['fixed_amount'] ?? 0), 'min_amount' => (float) ($row['min_amount'] ?? 0), 'rate_mod' => $modeMap[$modeText], 'customer_id' => $this->getIdsByCodes('registration', 'code', $row['customer_code'] ?? ''), 'service_id' => $this->getIdsByCodes('service', 'code', $row['service_code'] ?? ''), 'vendor_id' => $this->getIdsByCodes('vendor', 'code', $row['vendor_code'] ?? ''), 'status' => strcasecmp($row['status'] ?? '', 'Active') === 0 || ($row['status'] ?? '') === '1' ? 1 : 0, 'effective_from' => $from->format('Y-m-d'), 'effective_to' => $to, 'created_date' => date('Y-m-d H:i:s')];
            foreach (['percentage_on_freight','percentage_on_shipment_value','min_chargeable_weight','max_chargeable_weight','min_actual_weight','max_actual_weight','min_per_box_actual_weight','max_per_box_actual_weight'] as $field) $data[$field] = (float) ($row[$field] ?? 0);
            foreach (['min_dimension_per_pcs','max_dimension_per_pcs','is_origin_oda','is_destination_oda'] as $field) $data[$field] = strtoupper($row[$field] ?? '') === 'TRUE' ? 1 : 0;
            if ($this->supportModel->insert('rate_modifier', $data)) { $inserted++; $this->log('INSERT WHILE IMPORT', null, $data); } else { $skipped++; $reasons[] = "Row {$line}: database insert failed"; }
        }
        fclose($handle); $message = "Import complete. Inserted: {$inserted}, Skipped: {$skipped}.";
        if ($reasons) $message .= '<br><strong>Skipped reasons:</strong><br>' . implode('<br>', array_map('esc', $reasons));
        return redirect()->to('/admin/RateModifier')->with('error', $message)->with('error_class', $skipped ? 'alert-warning' : 'alert-success');
    }

    private function formLists(): array { return ['charge_master' => $this->supportModel->show_condition('charge', 'ASC', ['status' => 1]), 'customers' => $this->supportModel->show_condition('registration', 'ASC', ['status' => 1]), 'services' => $this->supportModel->show_condition('service', 'ASC', ['status' => 1]), 'vendors' => $this->supportModel->show_condition('vendor', 'ASC', ['status' => 1]), 'rates' => self::RATE_MODES]; }
    private function rules(): array { return ['charge_id' => 'required|integer', 'billing_type' => 'required|in_list[sale,purchase,SALE,PURCHASE]', 'fixed_amount' => 'permit_empty|decimal', 'min_amount' => 'permit_empty|decimal', 'rate_mod' => 'required|integer', 'status' => 'required|in_list[0,1]', 'effective_from' => 'required|valid_date[Y-m-d]']; }
    private function formData(): array
    {
        $data = ['charge_id' => (int) $this->request->getPost('charge_id'), 'billing_type' => strtolower((string) $this->request->getPost('billing_type')), 'fixed_amount' => (float) $this->request->getPost('fixed_amount'), 'min_amount' => (float) $this->request->getPost('min_amount'), 'rate_mod' => (int) $this->request->getPost('rate_mod'), 'customer_id' => $this->selectedIds('customer_id', 'registration'), 'service_id' => $this->selectedIds('service_id', 'service'), 'vendor_id' => $this->selectedIds('vendor_id', 'vendor'), 'status' => (int) $this->request->getPost('status'), 'effective_from' => $this->request->getPost('effective_from'), 'effective_to' => $this->request->getPost('effective_to') ?: null];
        foreach (['percentage_on_freight','percentage_on_shipment_value','min_chargeable_weight','max_chargeable_weight','min_actual_weight','max_actual_weight','min_volume_weight','max_volume_weight','min_no_of_boxes','max_no_of_boxes','min_dimension','max_dimension','min_per_box_actual_weight','max_per_box_actual_weight'] as $field) $data[$field] = $this->request->getPost($field) ?: 0;
        foreach (['min_dimension_per_pcs','max_dimension_per_pcs','is_origin_oda','is_destination_oda'] as $field) $data[$field] = $this->request->getPost($field) ? 1 : 0;
        return $data;
    }
    private function selectedIds(string $field, string $table): string { $ids = (array) $this->request->getPost($field); if (in_array('all', $ids, true)) return implode(',', array_map(fn ($r) => $r->id, $this->supportModel->show($table))); return implode(',', array_filter(array_map('intval', $ids))); }
    private function ids(string $value): array { return array_filter(array_map('trim', explode(',', $value)), 'strlen'); }
    private function map(array $rows, string $field): array { $map = []; foreach ($rows as $row) $map[$row->id] = $row->{$field}; return $map; }
    private function validDate(string $date): bool { $d = \DateTime::createFromFormat('d-m-Y', $date); return $d && $d->format('d-m-Y') === $date; }
    private function getIdsByCodes(string $table, string $field, string $codes): string { $values = array_filter(array_map('trim', explode(',', $codes))); if (! $values) return ''; return implode(',', array_column(db_connect()->table($table)->select('id')->whereIn($field, $values)->get()->getResultArray(), 'id')); }
    private function validationRedirect() { return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger'); }
    private function log(string $action, ?array $old, ?array $new): void { $admin = session()->get('admin_user'); $this->supportModel->insert('activity_logs', ['user_id' => session()->get('admin_login_id'), 'user_name' => $admin->userName ?? '', 'module_name' => 'Rate Modifier', 'action_type' => $action, 'old_data' => $old ? json_encode($old) : null, 'new_data' => $new ? json_encode($new) : null, 'ip_address' => $this->request->getIPAddress(), 'created_at' => date('Y-m-d H:i:s')]); }
}
