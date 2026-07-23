<?php

namespace App\Controllers\Admin;

use CodeIgniter\Exceptions\PageNotFoundException;

class Users extends Secure
{
    private function allowed()
    {
        if (! in_array('Add Access Control', $this->permission, true)) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'You do not have permission to manage admin users.')
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

    public function index(int $page = 1)
    {
        if ($response = $this->allowed()) {
            return $response;
        }

        $page = (int) ($this->request->getGet('page') ?? $page);

        $session = session();
        if ($this->request->getGetPost('clear') || $page === 0) {
            $session->remove(['user_filter_role', 'user_filter_hub', 'user_filter_email', 'user_filter_name']);
            $page = 1;
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $session->set([
                'user_filter_role' => trim((string) $this->request->getPost('role')),
                'user_filter_hub' => trim((string) $this->request->getPost('hub')),
                'user_filter_email' => trim((string) $this->request->getPost('email')),
                'user_filter_name' => trim((string) $this->request->getPost('username')),
            ]);
            $page = 1;
        }

        $filters = [
            'role' => (string) $session->get('user_filter_role'),
            'hub' => (string) $session->get('user_filter_hub'),
            'email' => (string) $session->get('user_filter_email'),
            'name' => (string) $session->get('user_filter_name'),
        ];

        $db = db_connect();
        $applyFilters = static function ($builder) use ($filters, $db): void {
            if ($filters['role'] !== '' && $filters['role'] !== 'All') {
                $builder->where('role', $filters['role']);
            }
            if ($filters['hub'] !== '') {
                $builder->where("FIND_IN_SET(" . $db->escape($filters['hub']) . ", user_assigned_hub) > 0", null, false);
            }
            if ($filters['email'] !== '') {
                $builder->like('userEmail', $filters['email']);
            }
            if ($filters['name'] !== '') {
                $builder->like('userName', $filters['name']);
            }
        };

        $countBuilder = $db->table('users');
        $applyFilters($countBuilder);
        $total = $countBuilder->countAllResults();
        $perPage = 10;
        $page = max(1, $page);

        $builder = $db->table('users');
        $applyFilters($builder);
        $users = $builder->orderBy('id', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResult();
        $rolesList = $this->supportModel->show('role', 'ASC');
        $roles = [];
        foreach ($rolesList as $role) {
            $roles[$role->id] = $role->name;
        }

        $pager = service('pager');
        $data = [
            'page_title' => 'Admin Users',
            'data' => [
                'code' => $users,
                'links' => $pager->makeLinks($page, $perPage, $total, 'admin_full'),
                'selected_role' => $filters['role'],
                'selected_hub' => $filters['hub'],
                'selected_email' => $filters['email'],
                'selected_name' => $filters['name'],
            ],
            'count' => ($page - 1) * $perPage,
            'roles' => $roles,
            'all_role' => $rolesList,
            'hub_list' => $this->supportModel->show('hub', 'ASC'),
        ];

        return $this->render('admin/user/list_user', $data);
    }

    public function add()
    {
        if ($response = $this->allowed()) {
            return $response;
        }

        $admin = session()->get('admin_user');
        $roleCondition = ['status' => 1];
        if (($admin->role ?? 1) != 1) {
            $roleCondition['parent_id'] = $admin->role;
        }

        return $this->render('admin/user/add_user', [
            'page_title' => 'Add Admin User',
            'role' => $this->supportModel->show_condition('role', 'ASC', $roleCondition),
            'hubs' => $this->supportModel->show_condition('hub', 'ASC', ['status' => 1]),
            'kyc' => $this->supportModel->show_condition('kyc_type', 'ASC', ['status' => 1]),
        ]);
    }

    public function insert()
    {
        if ($response = $this->allowed()) {
            return $response;
        }

        $rules = [
            'userName' => 'required',
            'userEmail' => 'required|valid_email|is_unique[users.userEmail]',
            'userPhone' => 'required',
            'userRole' => 'required|integer',
            'password' => 'required',
            'confirmPassword' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger');
        }

        $post = $this->request->getPost();
        $data = $this->userData($post, true);
        try {
            if ($fileName = $this->upload('profilePhoto', 'users/profile_photo', ['jpg', 'jpeg', 'png', 'webp'])) {
                $data['profile_photo'] = $fileName;
            }
            if ($fileName = $this->upload('kycDoc', 'users/kyc', ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                $data['kyc_document'] = $fileName;
            }
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('error_class', 'alert-danger');
        }

        if ($this->supportModel->insert('users', $data)) {
            $this->logActivity('INSERT', null, $data);
            return redirect()->to('/admin/users')->with('error', 'Successfully Added.')->with('error_class', 'alert-success');
        }

        return redirect()->back()->withInput()->with('error', 'Failed To Add.')->with('error_class', 'alert-danger');
    }

    public function edit_user(int $id)
    {
        if ($response = $this->allowed()) {
            return $response;
        }
        $user = $this->supportModel->find('users', $id);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound('Admin user was not found.');
        }

        return $this->render('admin/user/edit_user', [
            'page_title' => 'Edit Admin User',
            'data' => $user,
            'role' => $this->supportModel->show_condition('role', 'ASC', ['status' => 1]),
            'hubs' => $this->supportModel->show_condition('hub', 'ASC', ['status' => 1]),
            'kyc' => $this->supportModel->show_condition('kyc_type', 'ASC', ['status' => 1]),
        ]);
    }

    public function update_user(int $id)
    {
        if ($response = $this->allowed()) {
            return $response;
        }
        $old = $this->supportModel->find('users', $id);
        if (! $old) {
            throw PageNotFoundException::forPageNotFound('Admin user was not found.');
        }

        $rules = [
            'userName' => 'required',
            'userEmail' => "required|valid_email|is_unique[users.userEmail,id,{$id}]",
            'userPhone' => 'required',
            'userRole' => 'required|integer',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()))->with('error_class', 'alert-danger');
        }

        $data = $this->userData($this->request->getPost(), false);
        try {
            if ($fileName = $this->upload('profilePhoto', 'users/profile_photo', ['jpg', 'jpeg', 'png', 'webp'])) {
                $data['profile_photo'] = $fileName;
            }
            if ($fileName = $this->upload('kycDoc', 'users/kyc', ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                $data['kyc_document'] = $fileName;
            }
        } catch (\RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage())->with('error_class', 'alert-danger');
        }

        if ($this->supportModel->update('users', $data, $id)) {
            $this->logActivity('UPDATE', (array) $old, $data);
            return redirect()->to('/admin/users')->with('error', 'Successfully Updated.')->with('error_class', 'alert-success');
        }

        return redirect()->back()->withInput()->with('error', 'Failed To Update.')->with('error_class', 'alert-danger');
    }

    public function delete()
    {
        if ($response = $this->allowed()) {
            return $response;
        }
        $id = (int) $this->request->getPost('id');
        $old = $this->supportModel->find('users', $id);
        if ($old && $this->supportModel->delete('users', $id)) {
            $this->logActivity('DELETE', (array) $old, null);
            return redirect()->to('/admin/users')->with('error', 'Successfully Deleted.')->with('error_class', 'alert-success');
        }

        return redirect()->to('/admin/users')->with('error', 'Failed To Delete.')->with('error_class', 'alert-danger');
    }

    public function user_export()
    {
        if ($response = $this->allowed()) {
            return $response;
        }

        $roles = [];
        foreach ($this->supportModel->show('role', 'ASC') as $role) {
            $roles[$role->id] = $role->name;
        }
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['#', 'User Name', 'Email', 'Phone', 'Role', 'Date of Birth', 'Joining Date', 'Created At', 'Status']);
        foreach ($this->supportModel->show('users', 'DESC') as $index => $user) {
            fputcsv($stream, [
                $index + 1, $user->userName ?? '', $user->userEmail ?? '', $user->userPhone ?? '',
                $roles[$user->role ?? 0] ?? 'N/A', $user->dob ?? '', $user->joining_date ?? '',
                $user->date ?? '', ($user->status ?? 0) == 1 ? 'Active' : 'Inactive',
            ]);
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="UserList_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    private function userData(array $post, bool $creating): array
    {
        $data = [
            'userName' => trim((string) ($post['userName'] ?? '')),
            'userEmail' => trim((string) ($post['userEmail'] ?? '')),
            'userPhone' => trim((string) ($post['userPhone'] ?? '')),
            'role' => (int) ($post['userRole'] ?? 0),
            'dob' => $post['birthDate'] ?? '',
            'joining_date' => $post['joinDate'] ?? '',
            'default_user_hub' => implode(',', (array) ($post[$creating ? 'user_assign_hub' : 'default_user_hub'] ?? [])),
            'kyc_type' => $post['kycType'] ?? '',
            'kyc_no' => $post['kycNo'] ?? '',
            'user_assigned_hub' => implode(',', (array) ($post['userHubs'] ?? [])),
        ];
        if ($creating) {
            $data['password'] = $post['password'];
            $data['confirm_passowrd'] = $post['confirmPassword'];
            $data['date'] = date('Y-m-d H:i:s');
            $data['status'] = (int) ($post['status'] ?? 1);
        } else {
            $data['status'] = (int) ($post['status'] ?? 0);
            if (! empty($post['password'])) {
                $data['password'] = $post['password'];
            }
        }
        return $data;
    }

    private function upload(string $field, string $directory, array $extensions): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid()) {
            throw new \RuntimeException(ucwords(preg_replace('/(?<!^)[A-Z]/', ' $0', $field)) . ': ' . $file->getErrorString());
        }
        if ($file->getSizeByUnit('kb') > 5000) {
            throw new \RuntimeException(ucwords(preg_replace('/(?<!^)[A-Z]/', ' $0', $field)) . ' must not exceed 5 MB.');
        }
        if (! in_array(strtolower($file->getExtension()), $extensions, true)) {
            throw new \RuntimeException(ucwords(preg_replace('/(?<!^)[A-Z]/', ' $0', $field)) . ' file type is not allowed.');
        }
        // Browser-accessible user files belong under the CI4 public directory.
        $path = FCPATH . 'uploads/' . $directory;
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }
        $name = $file->getRandomName();
        $file->move($path, $name);
        return $name;
    }

    private function logActivity(string $action, ?array $old, ?array $new): void
    {
        $admin = session()->get('admin_user');
        db_connect()->table('activity_logs')->insert([
            'user_id' => session()->get('admin_login_id'),
            'user_name' => $admin->userName ?? '',
            'module_name' => 'Users',
            'action_type' => $action,
            'old_data' => $old === null ? null : json_encode($old),
            'new_data' => $new === null ? null : json_encode($new),
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
