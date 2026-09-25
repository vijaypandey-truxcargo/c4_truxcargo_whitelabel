<?php

namespace App\Controllers\Admin;

use CodeIgniter\Database\BaseBuilder;
use Config\Database;

class TrackingExceptionReport extends Secure
{
    private const PER_PAGE = 20;
    private const FILTER_SESSION_KEY = 'tracking_exception_filters';

    public function index($reset = null)
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $filters = $this->filters($reset === 'all');
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $total = $this->reportBuilder($filters)->countAllResults();
        $rows = $this->reportBuilder($filters)
            ->orderBy('id', 'DESC')
            ->limit(self::PER_PAGE, $offset)
            ->get()
            ->getResult();

        return $this->render('admin/tracking_exception_report', [
            'page_title' => 'Tracking Exception Report',
            'res' => $rows,
            'count' => $offset,
            'links' => service('pager')->makeLinks($page, self::PER_PAGE, $total, 'admin_full'),
            'filters' => $filters,
        ]);
    }

    public function view_awb()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $lrn = trim((string) $this->request->getPost('awb'));
        if ($lrn === '') {
            return $this->response->setBody('<div class="modal-body text-center">AWB Number Required</div>');
        }

        $trackUrl = base_url('api/truxapi/tracking/' . rawurlencode($lrn));
        $ch = curl_init($trackUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode((string) $response, true);
        $trackHistory = $data['data']['scaninfo'] ?? [];

        $html = '<div class="modal-header">
                <h4 class="modal-title">Tracking History - ' . esc($lrn) . '</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>';

        if ($trackHistory !== []) {
            foreach ($trackHistory as $track) {
                $html .= '<tr>
                    <td>' . esc($track['date'] ?? '') . '</td>
                    <td>' . esc($track['status'] ?? '') . '</td>
                    <td>' . esc($track['location'] ?? '') . '</td>
                    <td>' . esc($track['remark'] ?? '') . '</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="4" class="text-center">No History Found</td></tr>';
        }

        $html .= '</tbody></table></div>';

        return $this->response->setBody($html);
    }

    private function reportBuilder(array $filters): BaseBuilder
    {
        $builder = Database::connect()
            ->table('tracking_exception_report')
            ->where('entry_type', 'NDR');

        $this->applyUserScope($builder);
        $this->applyFilters($builder, $filters);

        return $builder;
    }

    private function applyUserScope(BaseBuilder $builder): void
    {
        $admin = session()->get('admin_user');
        $role = (int) ($admin->role ?? 0);

        if (in_array($role, [1, 2], true)) {
            return;
        }

        $userId = (int) (session()->get('user_id') ?: session()->get('admin_login_id'));
        $customers = $this->supportModel->select_rows('registration', 'id', 'ASC', ['cs_person' => $userId]);
        $customerIds = array_map(static fn ($row): int => (int) $row->id, $customers);

        if ($customerIds === []) {
            $builder->where('1=0', null, false);
            return;
        }

        $builder->whereIn('login_id', $customerIds);
    }

    private function applyFilters(BaseBuilder $builder, array $filters): void
    {
        $awb = trim((string) ($filters['awb'] ?? ''));
        if ($awb !== '') {
            $builder->like('awb_no', $awb);
        }

        $eventCode = trim((string) ($filters['event_code'] ?? ''));
        if ($eventCode !== '') {
            $builder->like('event_code', $eventCode);
        }

        $eventDesc = trim((string) ($filters['event_desc'] ?? ''));
        if ($eventDesc !== '') {
            $builder->like('event_desc', $eventDesc);
        }

        $this->applyDateFilter(
            $builder,
            'created_date',
            (string) ($filters['date'] ?? 'All'),
            (string) ($filters['date1'] ?? ''),
            (string) ($filters['date2'] ?? '')
        );
    }

    private function applyDateFilter(BaseBuilder $builder, string $column, string $date, string $date1, string $date2): void
    {
        if ($date === 'All' || $date === '') {
            return;
        }

        if ($date === 'Today') {
            $builder->where("DATE({$column})", date('Y-m-d'));
            return;
        }

        if ($date === 'Yesterday') {
            $builder->where("DATE({$column})", date('Y-m-d', strtotime('-1 day')));
            return;
        }

        if ($date === '7 Days') {
            $builder->where("DATE({$column}) >=", date('Y-m-d', strtotime('-6 days')));
            $builder->where("DATE({$column}) <=", date('Y-m-d'));
            return;
        }

        if ($date === '30 Days') {
            $builder->where("DATE({$column}) >=", date('Y-m-d', strtotime('-29 days')));
            $builder->where("DATE({$column}) <=", date('Y-m-d'));
            return;
        }

        if ($date === 'This Month') {
            $builder->where("DATE_FORMAT({$column}, '%Y-%m')", date('Y-m'));
            return;
        }

        if ($date === 'Last Month') {
            $builder->where("DATE_FORMAT({$column}, '%Y-%m')", date('Y-m', strtotime('last month')));
            return;
        }

        if ($date === 'Custom Range' && $date1 !== '' && $date2 !== '') {
            $builder->where("DATE({$column}) >=", $date1);
            $builder->where("DATE({$column}) <=", $date2);
        }
    }

    private function filters(bool $reset): array
    {
        $defaults = [
            'awb' => '',
            'event_code' => '',
            'event_desc' => '',
            'date' => 'All',
            'date1' => '',
            'date2' => '',
        ];

        $session = session();

        if ($reset) {
            $session->remove(self::FILTER_SESSION_KEY);
            return $defaults;
        }

        if ($this->request->is('post') && $this->request->getPost('apply') !== null) {
            $filters = [
                'awb' => trim((string) $this->request->getPost('awb')),
                'event_code' => trim((string) $this->request->getPost('event_code')),
                'event_desc' => trim((string) $this->request->getPost('event_desc')),
                'date' => (string) ($this->request->getPost('date') ?: 'All'),
                'date1' => (string) $this->request->getPost('date1'),
                'date2' => (string) $this->request->getPost('date2'),
            ];

            $session->set(self::FILTER_SESSION_KEY, $filters);
            return array_merge($defaults, $filters);
        }

        return array_merge($defaults, (array) $session->get(self::FILTER_SESSION_KEY));
    }

    private function render(string $view, array $data = []): string
    {
        $data += [
            'wconfig' => $this->wconfig,
            'permission' => $this->permission,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view($view, $data)
            . view('admin/footer');
    }

    private function guard(string $permission)
    {
        if (! in_array($permission, $this->permission, true)) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'You do not have permission to perform this action.')
                ->with('error_class', 'alert-danger');
        }

        return null;
    }
}
