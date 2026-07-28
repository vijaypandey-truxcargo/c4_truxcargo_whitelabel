<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Support extends Secure
{
    private const PER_PAGE = 10;

    public function __construct()
    {
        parent::__construct();
    }

    ///------------- Delhivery Pincode ------------------------///
    public function pincodes($count = 0)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $condition = $this->pincodeCondition();
        $data = $this->paginatedRows('pincodes', 'ASC', $condition, $count);
        $data['page_title'] = 'Couriers Pincode';

        return $this->render('admin/view_pincode', $data);
    }

    public function add_pincode()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->render('admin/add_pincode');
    }

    public function insert_pincode()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->insertRow('pincodes', '/admin/support/pincodes', '/admin/support/add_pincode');
    }

    public function delete_pincode()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('pincodes', $id);

        if ($this->supportModel->delete('pincodes', $id)) {
            $this->logActivity('DELETE', $this->logPayload('pincodes', $old), null);
        }

        return $this->successRedirect('/admin/support/pincodes', 'Successfully Delete.');
    }

    public function edit_pincode($id)
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->render('admin/edit_pincode', [
            'data' => $this->findOr404('pincodes', (int) $id, 'Pincode was not found.'),
        ]);
    }

    public function update_pincode($id)
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->updateRow('pincodes', (int) $id, '/admin/support/pincodes');
    }

    public function import_delivery()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->render('admin/import_delivery');
    }

    public function import_oda()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $panel = (int) $this->request->getPost('panel');

        if ($panel === 10) {
            $this->supportModel->update_condition('pincodes', ['status10' => '']);
        } elseif ($panel === 8) {
            $this->supportModel->update_condition('pincodes', ['status8' => '']);
        } elseif ($panel === 6) {
            $this->supportModel->update_condition('pincodes', ['status6' => '']);
        } else {
            $this->supportModel->update_condition('pincodes', ['status' => '']);
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/import_delivery');
        }

        return $this->importCsv('pincodes', '/admin/support/import_delivery', function (array $row) use ($panel): array {
            if ($panel === 10) {
                return [
                    'pincode' => $row['Pincode'] ?? '',
                    'oda10' => $row['ODA'] ?? '',
                    'center' => $row['Center'] ?? '',
                    'status10' => 'Yes',
                    'city' => $row['City'] ?? '',
                ];
            }

            if ($panel === 8) {
                return [
                    'pincode' => $row['Pincode'] ?? '',
                    'oda8' => $row['ODA'] ?? '',
                    'center' => $row['Center'] ?? '',
                    'status8' => 'Yes',
                    'city' => $row['City'] ?? '',
                ];
            }

            if ($panel === 6) {
                return [
                    'pincode' => $row['Pincode'] ?? '',
                    'oda6' => $row['ODA'] ?? '',
                    'center' => $row['Center'] ?? '',
                    'status6' => 'Yes',
                    'city' => $row['City'] ?? '',
                ];
            }

            return ['pincode' => $row['Pincode'] ?? ''];
        });
    }

    public function import_city()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/import_delivery');
        }

        return $this->importCsv('pincodes', '/admin/support/import_delivery', static fn (array $row): array => [
            'pincode' => $row['Pincode'] ?? '',
            'state' => $row['State'] ?? '',
            'city' => $row['City'] ?? '',
        ], false);
    }

    ///---------------- Terms ----------------------/////
    public function terms()
    {
        if ($response = $this->guard('View Additional Section')) {
            return $response;
        }

        return $this->render('admin/view_terms', [
            'data' => $this->supportModel->show('terms'),
        ]);
    }

    public function add_terms()
    {
        if ($response = $this->guard('Add Additional Section')) {
            return $response;
        }

        return $this->render('admin/add_terms');
    }

    public function insert_terms()
    {
        if ($response = $this->guard('Add Additional Section')) {
            return $response;
        }

        return $this->insertRow('terms', '/admin/support/terms', '/admin/support/add_terms');
    }

    public function delete_terms()
    {
        if ($response = $this->guard('Add Additional Section')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('terms', $id);

        if ($this->supportModel->delete('terms', $id)) {
            $this->logActivity('DELETE', $this->logPayload('terms', $old), null);
        }

        return $this->successRedirect('/admin/support/terms', 'Successfully Delete.');
    }

    public function edit_terms($id)
    {
        if ($response = $this->guard('Add Additional Section')) {
            return $response;
        }

        return $this->render('admin/edit_terms', [
            'data' => $this->findOr404('terms', (int) $id, 'Terms record was not found.'),
        ]);
    }

    public function update_terms($id)
    {
        if ($response = $this->guard('Add Additional Section')) {
            return $response;
        }

        return $this->updateRow('terms', (int) $id, '/admin/support/terms');
    }

    /////------------------- Support Category --------------------////
    public function category($count = 0)
    {
        if ($response = $this->guard('View Support')) {
            return $response;
        }

        $data = $this->paginatedRows('category', 'ASC', [], $count);
        $data['page_title'] = 'Issue Category';
        $data['key'] = $data['code'];
        unset($data['code']);

        return $this->render('admin/view_category', $data);
    }

    public function add_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        return $this->render('admin/add_category', ['page_title' => 'Add Issue Category']);
    }

    public function insert_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $post = $this->postData();

        if ($this->titleExists('category', trim((string) ($post['title'] ?? '')))) {
            return redirect()->back()->withInput()
                ->with('error', 'Category already exists.')
                ->with('error_class', 'alert-danger');
        }

        return $this->insertRow('category', '/admin/support/category', '/admin/support/add_category', $post);
    }

    public function delete_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $category = $this->supportModel->find('category', $id);

        if ($category) {
            $old = $this->logPayload('category', $category);
            $this->supportModel->delete_condition('subcategory', ['category' => $category->title]);

            if ($this->supportModel->delete('category', $id)) {
                $this->logActivity('DELETE', $old, null);
            }
        }

        return $this->successRedirect('/admin/support/category', 'Successfully Delete.');
    }

    public function edit_category($id)
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        return $this->render('admin/edit_category', [
            'data' => $this->findOr404('category', (int) $id, 'Issue category was not found.'),
        ]);
    }

    public function update_category($id)
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $id;
        $old = $this->findOr404('category', $id, 'Issue category was not found.');
        $post = $this->postData();
        $title = trim((string) ($post['title'] ?? ''));

        if ($this->titleExists('category', $title, $id)) {
            return redirect()->back()->withInput()
                ->with('error', 'Category already exists.')
                ->with('error_class', 'alert-danger');
        }

        if ($this->supportModel->update('category', $post, $id)) {
            if (($old->title ?? '') !== $title) {
                $this->supportModel->update_condition('subcategory', ['category' => $title], ['category' => $old->title]);
            }

            $this->logActivity('UPDATE', $this->logPayload('category', $old), $this->logPayload('category', $post, $id));

            return $this->successRedirect('/admin/support/category', 'Successfully Updated.');
        }

        return redirect()->back()->withInput()
            ->with('error', 'Failed To Update.')
            ->with('error_class', 'alert-danger');
    }

    ///-------------------- Sub Category ----------------------
    public function sub_category($count = 0)
    {
        if ($response = $this->guard('View Support')) {
            return $response;
        }

        $data = $this->paginatedRows('subcategory', 'ASC', [], $count);
        $data['page_title'] = 'Issue Sub Category';
        $data['key'] = $data['code'];
        unset($data['code']);

        return $this->render('admin/view_sub_category', $data);
    }

    public function add_sub_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        return $this->render('admin/add_sub_category', [
            'data' => $this->supportModel->show('category', 'ASC'),
        ]);
    }

    public function insert_sub_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $post = $this->postData();

        if ($this->titleExists('subcategory', trim((string) ($post['title'] ?? '')))) {
            return redirect()->back()->withInput()
                ->with('error', 'Sub category already exists.')
                ->with('error_class', 'alert-danger');
        }

        return $this->insertRow('subcategory', '/admin/support/sub_category', '/admin/support/add_sub_category', $post);
    }

    public function delete_sub_category()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('subcategory', $id);

        if ($this->supportModel->delete('subcategory', $id)) {
            $this->logActivity('DELETE', $this->logPayload('subcategory', $old), null);
        }

        return $this->successRedirect('/admin/support/sub_category', 'Successfully Delete.');
    }

    public function edit_sub_category($id)
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        return $this->render('admin/edit_sub_category', [
            'data' => $this->findOr404('subcategory', (int) $id, 'Issue sub category was not found.'),
            'cat' => $this->supportModel->show('category', 'ASC'),
        ]);
    }

    public function update_sub_category($id)
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $id;
        $this->findOr404('subcategory', $id, 'Issue sub category was not found.');
        $post = $this->postData();

        if ($this->titleExists('subcategory', trim((string) ($post['title'] ?? '')), $id)) {
            return redirect()->back()->withInput()
                ->with('error', 'Sub category already exists.')
                ->with('error_class', 'alert-danger');
        }

        return $this->updateRow('subcategory', $id, '/admin/support/sub_category', $post);
    }

    ////----------------------------- Ticket ----------------------------///
    public function ticket($count = 0)
    {
        return $this->ticketList('Open', 'admin/all_ticket', 'open', $count);
    }

    public function cancel($count = 0)
    {
        if ($response = $this->guard('View Support')) {
            return $response;
        }

        $search = $this->searchTerm();
        $condition = $search !== ''
            ? ['status' => 'Open', 'tno' => $search]
            : "status!='Closed' and (subject like '%Cancel%' or detail like '%Cancel%')";

        $data = $this->paginatedRows('ticket', 'DESC', $condition, $count);
        $data['open'] = $data['code'];
        unset($data['code']);
        $data += $this->ticketCommonData($search);

        return $this->render('admin/ticket_cancel', $data);
    }

    public function pending($count = 0)
    {
        return $this->ticketList('Pending', 'admin/ticket_pending', 'pending', $count);
    }

    public function closed($count = 0)
    {
        return $this->ticketList('Closed', 'admin/ticket_closed', 'closed', $count);
    }

    public function reply()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $ticket = $this->supportModel->search('ticket', ['id' => (int) $this->request->getPost('id')]);

        if (! $ticket) {
            return $this->response->setBody('Ticket was not found.');
        }

        $options = $ticket->status === 'Open'
            ? '<option value="Open">Open</option><option value="Pending">Pending</option><option value="Closed">Closed</option>'
            : '<option value="Pending">Pending</option><option value="Closed">Closed</option>';

        return $this->response->setBody(
            '<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><strong id="show1"> Request ID : #' . esc($ticket->tno) . '</strong></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class=" mt-20">Status</label>
                        <select class="form-control form-white" name="status" id="status" required="required">' . $options . '</select>
                        <span class="alert-danger mt-20" id="error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" onclick="submit(' . (int) $ticket->id . ')" value="Submit" class="btn btn-primary">
            </div>'
        );
    }

    public function submit()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('ticket', $id);
        $post = [
            'status' => (string) $this->request->getPost('status'),
        ];

        if ($this->supportModel->update('ticket', $post, $id)) {
            $this->logActivity('UPDATE', $this->logPayload('ticket', $old), $this->logPayload('ticket', $post, $id));
        }

        return $this->response->setBody('Successfully Updated.');
    }

    public function reopen()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('ticket', $id);
        $post = ['status' => 'Open'];

        if ($this->supportModel->update('ticket', $post, $id)) {
            $this->logActivity('UPDATE', $this->logPayload('ticket', $old), $this->logPayload('ticket', $post, $id));
        }

        return $this->successRedirect('/admin/support/closed', 'Successfully Updated.');
    }

    public function submit_reply()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $post = [
            'ticket_id' => (int) $this->request->getPost('ticket_id'),
            'user' => (string) $this->request->getPost('user'),
            'answer' => trim((string) $this->request->getPost('answer')),
            'date' => date('Y-m-d H:i:s'),
        ];

        if ($post['answer'] === '') {
            return $this->response->setBody('Please Enter Reply !');
        }

        $upload = $this->supportUpload('image', ['jpg', 'jpeg', 'png', 'gif', 'docx', 'pdf', 'csv', 'xlsx']);

        if (! $upload['success']) {
            return $this->response->setBody($upload['error']);
        }

        if (! empty($upload['name'])) {
            $post['image'] = $upload['name'];
        }

        $insertId = $this->supportModel->insert('reply', $post);

        if ($insertId) {
            $this->logActivity('INSERT', null, $this->logPayload('reply', $post, (int) $insertId));
        }

        return $this->response->setBody('Successfully Submit Reply !');
    }

    public function reply_ticket($id)
    {
        if ($response = $this->guard('View Support')) {
            return $response;
        }

        $id = (int) $id;
        $ticket = $this->supportModel->search('ticket', ['id' => $id]);

        if (! $ticket) {
            throw PageNotFoundException::forPageNotFound('Ticket was not found.');
        }

        return $this->render('admin/ticket_reply', [
            'data' => $ticket,
            'reply' => $this->supportModel->show_condition('reply', 'ASC', ['ticket_id' => $id]),
            'getName' => fn ($loginId) => '<b>' . esc($this->customerName($loginId, 'username')) . '</b>',
        ]);
    }

    public function markall()
    {
        if ($response = $this->guard('Add Support')) {
            return $response;
        }

        $ids = (array) $this->request->getPost('id');
        $post = ['status' => (string) $this->request->getPost('status')];

        foreach ($ids as $id) {
            $id = (int) $id;
            $old = $this->supportModel->find('ticket', $id);

            if ($this->supportModel->update('ticket', $post, $id)) {
                $this->logActivity('UPDATE', $this->logPayload('ticket', $old), $this->logPayload('ticket', $post, $id));
            }
        }

        return $this->response->setBody('Successfully Updated.');
    }

    public function pincode_check()
    {
        $condition = ['pincode' => $this->request->getPost('pincode')];

        return $this->response->setBody((string) $this->supportModel->getRows('pincodes', $condition));
    }

    ////---------------------- Rivigo Pincode ----------------///
    public function pincodes_rivigo($count = 0)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $data = $this->paginatedRows('rivigopin', 'ASC', $this->pincodeCondition(), $count);
        $data['page_title'] = 'Rivigo Pincode';

        return $this->render('admin/view_fedexpin', $data);
    }

    public function add_rivigopin()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->render('admin/add_fedexpin', ['page_title' => 'Add Rivigo Pincode']);
    }

    public function insert_rivigopin()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->insertRow('rivigopin', '/admin/support/pincodes_rivigo', '/admin/support/add_rivigopin');
    }

    public function delete_rivigopin()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('rivigopin', $id);

        if ($this->supportModel->delete('rivigopin', $id)) {
            $this->logActivity('DELETE', $this->logPayload('rivigopin', $old), null);
        }

        return $this->successRedirect('/admin/support/pincodes_rivigo', 'Successfully Delete.');
    }

    public function edit_rivigopin($id)
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->render('admin/edit_fedexpin', [
            'page_title' => 'Edit Rivigo Pincode',
            'data' => $this->findOr404('rivigopin', (int) $id, 'Rivigo pincode was not found.'),
        ]);
    }

    public function update_rivigopin($id)
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        return $this->updateRow('rivigopin', (int) $id, '/admin/support/pincodes_rivigo');
    }

    public function import_rivigopin()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/pincodes_rivigo');
        }

        return $this->importCsv('rivigopin', '/admin/support/pincodes_rivigo', static fn (array $row): array => [
            'pincode' => $row['Pincode'] ?? '',
            'pickup' => $row['Pickup'] ?? '',
            'delivery' => $row['Delivery'] ?? '',
        ]);
    }

    ////---------------------- Oxygen Pincode ----------------///
    public function oxygen($count = 0)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $data = $this->paginatedRows('oxygen', 'ASC', $this->pincodeCondition(), $count);
        $data['page_title'] = 'Oxygen Pincodes';

        return $this->render('admin/view_oxygen', $data);
    }

    public function import_oxygen()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/oxygen');
        }

        return $this->importCsv('oxygen', '/admin/support/oxygen', static fn (array $row): array => [
            'pincode' => $row['Pincode'] ?? '',
            'state' => $row['State'] ?? '',
            'zone' => $row['Zone'] ?? '',
            'oda' => $row['ODA'] ?? '',
        ]);
    }

    public function delete_oxygen()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('oxygen', $id);

        if ($this->supportModel->delete('oxygen', $id)) {
            $this->logActivity('DELETE', $this->logPayload('oxygen', $old), null);
        }

        return $this->successRedirect('/admin/support/oxygen', 'Successfully Delete.');
    }

    ////---------------------- Ekart Pincode ----------------///
    public function ekart($count = 0)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $data = $this->paginatedRows('ekart', 'ASC', $this->pincodeCondition(), $count);
        $data['page_title'] = 'Ekart Pincodes';

        return $this->render('admin/view_ekart', $data);
    }

    public function import_ekart()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/ekart');
        }

        return $this->importCsv('ekart', '/admin/support/ekart', static fn (array $row): array => [
            'pincode' => $row['Pincode'] ?? '',
            'state' => $row['State'] ?? '',
            'city' => $row['City'] ?? '',
            'oda' => $row['ODA'] ?? '',
        ]);
    }

    public function delete_ekart()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('ekart', $id);

        if ($this->supportModel->delete('ekart', $id)) {
            $this->logActivity('DELETE', $this->logPayload('ekart', $old), null);
        }

        return $this->successRedirect('/admin/support/ekart', 'Successfully Delete.');
    }

    ////---------------------- Amazon Pincode ----------------///
    public function amazon_pins($count = 0)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $data = $this->paginatedRows('amazon', 'ASC', $this->pincodeCondition(), $count);
        $data['page_title'] = 'Amazon Pincodes';

        return $this->render('admin/amazon', $data);
    }

    public function import_amazon()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        if (! $this->request->getPost('importSubmit')) {
            return redirect()->to('/admin/support/amazon_pins');
        }

        return $this->importCsv('amazon', '/admin/support/amazon_pins', static fn (array $row): array => [
            'pincode' => $row['Pincode'] ?? '',
            'city' => $row['City'] ?? '',
            'state' => $row['State'] ?? '',
            'center' => strtoupper((string) ($row['Center'] ?? '')),
        ]);
    }

    public function delete_amazon()
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('amazon', $id);

        if ($this->supportModel->delete('amazon', $id)) {
            $this->logActivity('DELETE', $this->logPayload('amazon', $old), null);
        }

        return $this->successRedirect('/admin/support/amazon_pins', 'Successfully Delete.');
    }

    ////--------------------- Pincode Report -------------------///
    public function report()
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $table = (string) $this->request->getPost('table');
        $rows = $this->supportModel->show($table, 'ASC');
        $this->logActivity('REPORT', null, ['_table' => $table, 'rows' => count($rows)]);

        ob_start();
        echo $this->reportTable($table, $rows);
        $this->csv();

        return $this->response->setBody((string) ob_get_clean());
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

    private function postData(?array $data = null): array
    {
        $post = $data ?? $this->request->getPost();
        $csrf = csrf_token();

        unset($post['submit'], $post['importSubmit'], $post[$csrf]);

        return $post;
    }

    private function currentPage(): int
    {
        return max(1, (int) ($this->request->getGet('page') ?? 1));
    }

    private function paginatedRows(string $table, string $order = 'DESC', $condition = [], int $count = 0, string $key = 'code'): array
    {
        $page = $this->currentPage();
        $offset = ($page - 1) * self::PER_PAGE;
        $total = $this->supportModel->getRows($table, $condition);

        return [
            'count' => $offset ?: $count,
            'links' => service('pager')->makeLinks($page, self::PER_PAGE, $total, 'admin_full'),
            $key => $this->supportModel->show_limit($table, self::PER_PAGE, $offset, $order, $condition),
        ];
    }

    private function pincodeCondition()
    {
        $pincode = trim((string) $this->request->getGetPost('pincode'));

        return $pincode !== '' ? ['pincode' => $pincode] : '1=1';
    }

    private function searchTerm(): string
    {
        return trim((string) $this->request->getGetPost('search'));
    }

    private function insertRow(string $table, string $successUrl, string $failureUrl, ?array $data = null)
    {
        $post = $this->postData($data);
        $insertId = $this->supportModel->insert($table, $post);

        if ($insertId) {
            $this->logActivity('INSERT', null, $this->logPayload($table, $post, (int) $insertId));
            return $this->successRedirect($successUrl, 'Successfully Added.');
        }

        return redirect()->to($failureUrl)
            ->withInput()
            ->with('error', 'Failed To Add.')
            ->with('error_class', 'alert-danger');
    }

    private function updateRow(string $table, int $id, string $successUrl, ?array $data = null)
    {
        $post = $this->postData($data);
        $old = $this->supportModel->find($table, $id);

        if ($this->supportModel->update($table, $post, $id)) {
            $this->logActivity('UPDATE', $this->logPayload($table, $old), $this->logPayload($table, $post, $id));
            return $this->successRedirect($successUrl, 'Successfully Updated.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed To Update.')
            ->with('error_class', 'alert-danger');
    }

    private function successRedirect(string $url, string $message)
    {
        return redirect()->to($url)
            ->with('error', $message)
            ->with('error_class', 'alert-success');
    }

    private function findOr404(string $table, int $id, string $message)
    {
        $row = $this->supportModel->find($table, $id);

        if (! $row) {
            throw PageNotFoundException::forPageNotFound($message);
        }

        return $row;
    }

    private function ticketList(string $status, string $view, string $dataKey, int $count = 0)
    {
        if ($response = $this->guard('View Support')) {
            return $response;
        }

        $search = $this->searchTerm();
        $condition = $search !== '' ? ['status' => $status, 'tno' => $search] : ['status' => $status];
        $data = $this->paginatedRows('ticket', 'DESC', $condition, $count);
        $data[$dataKey] = $data['code'];
        unset($data['code']);
        $data += $this->ticketCommonData($search);

        return $this->render($view, $data);
    }

    private function ticketCommonData(string $search): array
    {
        return [
            'page_title' => 'Support Ticket',
            'search' => $search,
            'getName' => fn ($loginId) => $this->customerName($loginId),
        ];
    }

    private function customerName($loginId, string $columns = 'first,last,username'): string
    {
        $customer = $this->supportModel->find_col('registration', $columns, $loginId);

        if (! $customer) {
            return '';
        }

        if ($columns === 'username') {
            return (string) ($customer->username ?? '');
        }

        return esc(trim(($customer->first ?? '') . ' ' . ($customer->last ?? '')))
            . '<br><b>' . esc($customer->username ?? '') . '</b>';
    }

    private function titleExists(string $table, string $title, ?int $exceptId = null): bool
    {
        if ($title === '') {
            return false;
        }

        $row = $this->supportModel->search($table, ['title' => $title]);

        if (! $row) {
            return false;
        }

        return $exceptId === null || (int) $row->id !== $exceptId;
    }

    private function logActivity(string $action, ?array $old, ?array $new): void
    {
        $admin = session()->get('admin_user');

        db_connect()->table('activity_logs')->insert([
            'user_id' => session()->get('admin_login_id'),
            'user_name' => $admin->userName ?? '',
            'module_name' => 'Support',
            'action_type' => $action,
            'old_data' => $old === null ? null : json_encode($old),
            'new_data' => $new === null ? null : json_encode($new),
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function logPayload(string $table, $data, ?int $id = null): ?array
    {
        if (! $data) {
            return null;
        }

        $payload = is_array($data) ? $data : (array) $data;

        if ($id !== null && ! isset($payload['id'])) {
            $payload['id'] = $id;
        }

        return ['_table' => $table] + $payload;
    }

    private function importCsv(string $table, string $redirectUrl, callable $rowMapper, bool $allowInsert = true)
    {
        $rows = $this->parseUploadedCsv();

        if ($rows === null) {
            return redirect()->to($redirectUrl)
                ->with('error', 'Invalid file, please select only CSV file.')
                ->with('error_class', 'alert-danger');
        }

        $insertCount = 0;
        $updateCount = 0;
        $rowCount = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $rowCount++;
            $data = $rowMapper($row);
            $pincode = trim((string) ($data['pincode'] ?? ''));

            if ($pincode === '') {
                $skipped++;
                continue;
            }

            $condition = ['pincode' => $pincode];

            if ($this->supportModel->getRows($table, $condition) > 0) {
                if ($this->supportModel->update_condition($table, $data, $condition)) {
                    $updateCount++;
                } else {
                    $skipped++;
                }
            } elseif ($allowInsert && $this->supportModel->insert($table, $data)) {
                $insertCount++;
            } else {
                $skipped++;
            }
        }

        $notAddCount = $rowCount - ($insertCount + $updateCount);
        $message = 'Pincodes imported successfully. Total Rows (' . $rowCount . ') | Inserted (' . $insertCount . ') | Updated (' . $updateCount . ') | Not Inserted (' . max($notAddCount, $skipped) . ')';
        $this->logActivity('IMPORT', null, [
            '_table' => $table,
            'total_rows' => $rowCount,
            'inserted' => $insertCount,
            'updated' => $updateCount,
            'not_inserted' => max($notAddCount, $skipped),
        ]);

        return redirect()->to($redirectUrl)
            ->with('error', $message)
            ->with('error_class', 'alert-success');
    }

    private function parseUploadedCsv(): ?array
    {
        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid() || strtolower($file->getClientExtension()) !== 'csv') {
            return null;
        }

        $handle = fopen($file->getTempName(), 'r');

        if (! $handle) {
            return null;
        }

        $headers = fgetcsv($handle);
        $rows = [];

        if (! is_array($headers)) {
            fclose($handle);
            return [];
        }

        $headers = array_map(static fn ($header) => trim((string) $header), $headers);

        while (($line = fgetcsv($handle)) !== false) {
            $line = array_pad($line, count($headers), '');
            $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
        }

        fclose($handle);

        return $rows;
    }

    private function supportUpload(string $field, array $allowedExtensions): array
    {
        $file = $this->request->getFile($field);

        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'name' => null, 'error' => null];
        }

        if (! $file->isValid()) {
            return ['success' => false, 'name' => null, 'error' => $file->getErrorString()];
        }

        if ($file->getSize() > 102400) {
            return ['success' => false, 'name' => null, 'error' => 'The file size cannot exceed 100KB.'];
        }

        $extension = strtolower($file->getClientExtension());

        if (! in_array($extension, $allowedExtensions, true)) {
            return ['success' => false, 'name' => null, 'error' => 'Invalid file type.'];
        }

        $uploadPath = FCPATH . 'uploads/support';

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $name = $file->getRandomName();
        $file->move($uploadPath, $name);

        return ['success' => true, 'name' => $name, 'error' => null];
    }

    private function reportTable(string $table, array $rows): string
    {
        if ($table === 'pincodes') {
            $html = '<table class="table table-bordered" id="report"><tr>
                <th>Pincode</th><th>City</th><th>State</th><th>Dispatched Center</th>
                <th>ODA (Delhivery Economy)</th><th>Serviceablity (Delhivery Economy)</th>
                <th>ODA (Delhivery Cargo)</th><th>Serviceablity (Delhivery Cargo)</th>
                <th>ODA (Delhivery Lite)</th><th>Serviceablity (Delhivery Lite)</th></tr>';

            foreach ($rows as $row) {
                $html .= '<tr><td>' . esc($row->pincode ?? '') . '</td><td>' . esc(str_replace(',', ' ', $row->city ?? '')) . '</td><td>' . esc(str_replace(',', ' ', $row->state ?? '')) . '</td><td>' . esc(str_replace(',', ' ', $row->center ?? '')) . '</td><td>' . esc($row->oda10 ?? '') . '</td><td>' . esc($row->status10 ?? '') . '</td><td>' . esc($row->oda8 ?? '') . '</td><td>' . esc($row->status8 ?? '') . '</td><td>' . esc($row->oda6 ?? '') . '</td><td>' . esc($row->status6 ?? '') . '</td></tr>';
            }

            return $html . '</table>';
        }

        if ($table === 'oxygen') {
            $html = '<table class="table table-bordered" id="report"><tr><th>Pincode</th><th>Zone</th><th>State</th><th>ODA</th></tr>';

            foreach ($rows as $row) {
                $html .= '<tr><td>' . esc($row->pincode ?? '') . '</td><td>' . esc(str_replace(',', ' ', $row->zone ?? '')) . '</td><td>' . esc(str_replace(',', ' ', $row->state ?? '')) . '</td><td>' . esc($row->oda ?? '') . '</td></tr>';
            }

            return $html . '</table>';
        }

        if ($table === 'ekart') {
            $html = '<table class="table table-bordered" id="report"><tr><th>Pincode</th><th>City</th><th>State</th><th>ODA</th></tr>';

            foreach ($rows as $row) {
                $html .= '<tr><td>' . esc($row->pincode ?? '') . '</td><td>' . esc(str_replace(',', ' ', $row->city ?? '')) . '</td><td>' . esc(str_replace(',', ' ', $row->state ?? '')) . '</td><td>' . esc($row->oda ?? '') . '</td></tr>';
            }

            return $html . '</table>';
        }

        $html = '<table class="table table-bordered" id="report"><tr><th>Pincode</th><th>Pickup</th><th>Delhivery</th></tr>';

        foreach ($rows as $row) {
            $html .= '<tr><td>' . esc($row->pincode ?? '') . '</td><td>' . esc($row->pickup ?? '') . '</td><td>' . esc($row->delivery ?? $row->delhivery ?? '') . '</td></tr>';
        }

        return $html . '</table>';
    }
}
