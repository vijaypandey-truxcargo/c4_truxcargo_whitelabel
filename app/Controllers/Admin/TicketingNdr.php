<?php

namespace App\Controllers\Admin;

use CodeIgniter\Database\BaseBuilder;
use Config\Database;

class TicketingNdr extends Secure
{
    private const PER_PAGE = 10;
    private const FILTER_SESSION_KEY = 'ticketing_ndr_filters';
    private const STATUS_OPTIONS = ['Open', 'Pending', 'Close', 'Closed'];

    public function index($reset = null)
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $filters = $this->filters($reset === 'all');
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $total = $this->ticketingBuilder($filters)->countAllResults();
        $rows = $this->ticketingBuilder($filters)
            ->select('ter.*, ow.panel, ow.d_mode AS mode, ow.awb_status')
            ->orderBy('ter.id', 'DESC')
            ->limit(self::PER_PAGE, $offset)
            ->get()
            ->getResult();

        return $this->render('admin/ticketing_ndr', [
            'page_title' => 'All Ticket EDD',
            'res' => $rows,
            'count' => $offset,
            'links' => service('pager')->makeLinks($page, self::PER_PAGE, $total, 'admin_full'),
            'filters' => $filters,
            'key' => $this->supportModel->select_rows('registration', 'id,first,last,username', 'ASC'),
            'user' => fn ($id): string => $this->customerName((int) $id),
        ]);
    }

    public function reply()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $row = $this->supportModel->find('tracking_exception_report', $id);

        if (! $row) {
            return $this->response->setBody('<div class="modal-body text-center text-danger"><strong>No record found.</strong></div>');
        }

        $currentStatus = (string) ($row->status ?? 'Open');
        $options = '';

        foreach (self::STATUS_OPTIONS as $status) {
            $selected = $status === $currentStatus ? ' selected' : '';
            $options .= '<option value="' . esc($status, 'attr') . '"' . $selected . '>' . esc($status) . '</option>';
        }

        return $this->response->setBody(
            '<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><strong>Tracking Number : ' . esc($row->awb_no ?? '') . '</strong></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status">' . $options . '</select>
                    </div>
                    <div class="col-md-12 mt-20">
                        <label>Comment</label>
                        <textarea class="form-control" id="comment"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" onclick="submitComment(' . $id . ')" value="Submit" class="btn btn-primary">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>'
        );
    }

    public function submit()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $row = $this->supportModel->find('tracking_exception_report', $id);

        if (! $row) {
            return $this->response->setStatusCode(404)->setBody('No record found.');
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, self::STATUS_OPTIONS, true)) {
            $status = 'Open';
        }

        $admin = session()->get('admin_user');
        $updatedBy = (string) ($admin->userName ?? 'Admin');
        $comment = trim((string) $this->request->getPost('comment'));
        $update = [
            'updated_by' => $updatedBy,
            'status' => $status,
        ];

        if ($comment !== '') {
            $comments = json_decode((string) ($row->comments ?? ''), true);
            if (! is_array($comments)) {
                $comments = [];
            }

            array_unshift($comments, [
                'time' => date('Y-m-d H:i:s'),
                'updated_by' => $updatedBy,
                'comment' => $comment,
            ]);

            $update['comments'] = json_encode($comments);
        }

        if ($this->supportModel->update('tracking_exception_report', $update, $id)) {
            return $this->response->setBody('Successfully Updated.');
        }

        return $this->response->setBody('Something went wrong.');
    }

    public function view_comment()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $row = $this->supportModel->find('tracking_exception_report', $id);

        if (! $row) {
            return $this->response->setBody('<div class="modal-body text-center text-danger"><strong>No record found.</strong></div>');
        }

        $comments = json_decode((string) ($row->comments ?? ''), true);
        if (! is_array($comments)) {
            $comments = [];
        }

        $html = '<div class="modal-header" style="background:#f8f9fa;border-bottom:1px solid #ddd;">
            <button type="button" class="close" data-dismiss="modal" style="color:red;font-size:28px;">&times;</button>
            <h4 class="modal-title">Tracking : <strong>' . esc($row->awb_no ?? '') . '</strong></h4>
        </div>
        <div class="modal-body" style="max-height:400px;overflow-y:auto;">';

        if ($comments === []) {
            $html .= '<p>No comments available.</p>';
        } else {
            $html .= '<table class="table table-hover table-striped">
                <thead><tr><th>#</th><th>Updated By</th><th>Time</th><th>Comment</th></tr></thead>
                <tbody>';

            $i = 1;
            foreach ($comments as $comment) {
                $text = trim((string) ($comment['comment'] ?? ''));
                if ($text === '') {
                    continue;
                }

                $time = (string) ($comment['time'] ?? '');
                $displayTime = $time !== '' ? date('d M Y h:i A', strtotime($time)) : '';
                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . esc($comment['updated_by'] ?? '') . '</td>
                    <td>' . esc($displayTime) . '</td>
                    <td>' . esc($text) . '</td>
                </tr>';
            }

            $html .= '</tbody></table>';
        }

        $html .= '</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>';

        return $this->response->setBody($html);
    }

    public function send_ticket_mail()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $row = $this->supportModel->find('tracking_exception_report', $id);

        if (! $row) {
            return $this->response->setJSON(['status' => false, 'message' => 'No record found.']);
        }

        $customer = $this->supportModel->find_col('registration', 'email,company,first,last', $row->login_id);
        if (! $customer || empty($customer->email)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Customer email not found.']);
        }

        $smtpUser = env('email.SMTPUser') ?: (string) ($this->wconfig->email ?? '');
        $smtpPass = env('email.SMTPPass') ?: (string) ($this->wconfig->password ?? '');
        $fromEmail = env('email.fromEmail') ?: $smtpUser;

        if ($smtpUser === '' || $smtpPass === '' || $fromEmail === '') {
            return $this->response->setJSON(['status' => false, 'message' => 'Email configuration is missing.']);
        }

        $email = service('email');
        $email->initialize([
            'protocol' => 'smtp',
            'SMTPHost' => env('email.SMTPHost') ?: 'mail.mypafex.in',
            'SMTPPort' => (int) (env('email.SMTPPort') ?: 465),
            'SMTPUser' => $smtpUser,
            'SMTPPass' => $smtpPass,
            'SMTPCrypto' => env('email.SMTPCrypto') ?: 'ssl',
            'SMTPTimeout' => (int) (env('email.SMTPTimeout') ?: 30),
            'mailType' => 'html',
            'charset' => 'UTF-8',
            'wordWrap' => true,
            'newline' => "\r\n",
            'CRLF' => "\r\n",
            'validate' => true,
        ]);

        $awbNo = esc((string) ($row->awb_no ?? ''));
        $reason = esc((string) ($row->event_desc ?? 'N/A'));
        $message = "
            <p>Dear Customer,</p>
            <p>Your shipment was undelivered due to the following reason: <strong>{$reason}</strong>, resulting in a delivery delay.</p>
            <p>Please provide an alternate address and contact number for the delivery at the earliest.</p>
            <p><strong>Delayed Delivery Notification</strong></p>
            <br>
            <p>Regards,<br><strong>PRAKASH AIR FREIGHT INDIA PVT LTD</strong></p>
        ";

        $email->setFrom($fromEmail, env('email.fromName') ?: 'PRAKASH AIR FREIGHT INDIA PVT LTD');
        $email->setTo((string) $customer->email);
        $email->setSubject('Delayed Delivery Notification - ' . $awbNo);
        $email->setMessage($message);

        if ($email->send()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Mail sent successfully.']);
        }

        log_message('error', (string) $email->printDebugger(['headers']));

        return $this->response->setJSON(['status' => false, 'message' => 'Mail could not be sent.']);
    }

    public function export_ticket_ndr()
    {
        if ($response = $this->guard('B2B Order')) {
            return $response;
        }

        $filters = $this->filters(false);
        $rows = $this->ticketingBuilder($filters)
            ->select('ter.*, ow.panel, ow.d_mode AS mode, ow.awb_status')
            ->orderBy('ter.id', 'DESC')
            ->get()
            ->getResult();

        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['AWB', 'Customer Name', 'Vendor', 'Event Code', 'Event Desc', 'Expected Date', 'Delivery Date', 'Created Date', 'Status']);

        foreach ($rows as $row) {
            fputcsv($stream, [
                $row->awb_no ?? '',
                $row->customer_name ?? '',
                $row->vendor_name ?? '',
                $row->event_code ?? '',
                $row->event_desc ?? '',
                $row->expected_date ?? '',
                $row->delivery_date ?? '',
                $row->created_date ?? '',
                $row->status ?? '',
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="edd_report_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    private function ticketingBuilder(array $filters): BaseBuilder
    {
        $builder = Database::connect()
            ->table('tracking_exception_report ter')
            ->join('order_waybills ow', 'ter.awb_no = ow.lrnum', 'inner')
            ->where('ter.entry_type', 'EDD')
            ->where('ow.awb_status !=', 'Delivered');

        $this->applyFilters($builder, $filters);

        return $builder;
    }

    private function applyFilters(BaseBuilder $builder, array $filters): void
    {
        if (($filters['login_id'] ?? 'All') !== 'All' && $filters['login_id'] !== '') {
            $builder->where('ow.login_id', (int) $filters['login_id']);
        }

        $lrn = trim((string) ($filters['lrn'] ?? ''));
        if ($lrn !== '') {
            $lrns = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $lrn))));
            if ($lrns !== []) {
                $builder->whereIn('ter.awb_no', $lrns);
            }
        }

        $status = (string) ($filters['status'] ?? 'All');
        if ($status !== 'All' && in_array($status, self::STATUS_OPTIONS, true)) {
            $builder->where('ter.status', $status);
        }

        $this->applyDateFilter(
            $builder,
            'ter.created_date',
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
            'login_id' => 'All',
            'lrn' => '',
            'status' => 'All',
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
                'login_id' => (string) ($this->request->getPost('login_id') ?: 'All'),
                'lrn' => trim((string) $this->request->getPost('lrn')),
                'status' => (string) ($this->request->getPost('status') ?: 'All'),
                'date' => (string) ($this->request->getPost('date') ?: 'All'),
                'date1' => (string) $this->request->getPost('date1'),
                'date2' => (string) $this->request->getPost('date2'),
            ];

            $session->set(self::FILTER_SESSION_KEY, $filters);
            return array_merge($defaults, $filters);
        }

        return array_merge($defaults, (array) $session->get(self::FILTER_SESSION_KEY));
    }

    private function customerName(int $loginId): string
    {
        $customer = $this->supportModel->find_col('registration', 'first,last,username', $loginId);

        if (! $customer) {
            return 'NA';
        }

        return '<b>' . esc($customer->username ?? '') . '</b><br>' . esc(trim(($customer->first ?? '') . ' ' . ($customer->last ?? '')));
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
