<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Partnerb2b extends Secure
{
    private const PER_PAGE = 10;
    private const TABLE = 'b2b-partner';

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
        $data += [
            'wconfig'    => $this->wconfig,
            'permission' => $this->permission,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view($view, $data)
            . view('admin/footer');
    }

    public function index(int $page = 1)
    {
        if ($response = $this->guard('View Partner')) {
            return $response;
        }

        $page = (int) ($this->request->getGet('page') ?? $page);
        $page = max(1, $page);

        $db = db_connect();
        $total = $db->table(self::TABLE)->countAllResults();
        $rows = $db->table(self::TABLE)
            ->orderBy('id', 'ASC')
            ->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)
            ->get()
            ->getResult();

        return $this->render('admin/view_partnerb2b', [
            'page_title' => 'B2B Partner',
            'code'       => $rows,
            'count'      => ($page - 1) * self::PER_PAGE,
            'links'      => service('pager')->makeLinks($page, self::PER_PAGE, $total, 'admin_full'),
        ]);
    }

    public function add()
    {
        if ($response = $this->guard('Add Partner')) {
            return $response;
        }

        return $this->render('admin/add_partnerb2b', [
            'page_title' => 'Add B2B Partner',
        ]);
    }

    public function insert()
    {
        if ($response = $this->guard('Add Partner')) {
            return $response;
        }

        if (! $this->validate($this->rules())) {
            return $this->validationRedirect();
        }

        $data = $this->partnerData();

        if ($this->supportModel->insert(self::TABLE, $data)) {
            return redirect()->to('/admin/partnerb2b')
                ->with('error', 'Successfully Added.')
                ->with('error_class', 'alert-success');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed To Add.')
            ->with('error_class', 'alert-danger');
    }

    public function edit(int $id)
    {
        if ($response = $this->guard('Add Partner')) {
            return $response;
        }

        $data = $this->supportModel->find(self::TABLE, $id);
        if (! $data) {
            throw PageNotFoundException::forPageNotFound('B2B partner was not found.');
        }

        return $this->render('admin/edit_partnerb2b', [
            'page_title' => 'Edit B2B Partner',
            'data'       => $data,
        ]);
    }

    public function update(int $id)
    {
        if ($response = $this->guard('Add Partner')) {
            return $response;
        }

        if (! $this->supportModel->find(self::TABLE, $id)) {
            throw PageNotFoundException::forPageNotFound('B2B partner was not found.');
        }

        if (! $this->validate($this->rules())) {
            return $this->validationRedirect();
        }

        if ($this->supportModel->update(self::TABLE, $this->partnerData(), $id)) {
            return redirect()->to('/admin/partnerb2b')
                ->with('error', 'Successfully Updated.')
                ->with('error_class', 'alert-success');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed To Update.')
            ->with('error_class', 'alert-danger');
    }

    public function delete()
    {
        if ($response = $this->guard('Add Partner')) {
            return $response;
        }

        $id = (int) $this->request->getPost('id');

        if ($id > 0 && $this->supportModel->delete(self::TABLE, $id)) {
            return redirect()->to('/admin/partnerb2b')
                ->with('error', 'Successfully Deleted.')
                ->with('error_class', 'alert-success');
        }

        return redirect()->to('/admin/partnerb2b')
            ->with('error', 'Failed To Delete.')
            ->with('error_class', 'alert-danger');
    }

    private function rules(): array
    {
        return [
            'title'    => 'required',
            'username' => 'required',
            'password' => 'required',
            'apikey'   => 'required',
            'overall'  => 'required',
            'jwt'      => 'permit_empty',
        ];
    }

    private function partnerData(): array
    {
        $data = [];

        foreach (['title', 'username', 'password', 'apikey', 'overall', 'jwt'] as $field) {
            $data[$field] = trim((string) $this->request->getPost($field));
        }

        return $data;
    }

    private function validationRedirect()
    {
        return redirect()->back()
            ->withInput()
            ->with('error', implode(' ', $this->validator->getErrors()))
            ->with('error_class', 'alert-danger');
    }
}
