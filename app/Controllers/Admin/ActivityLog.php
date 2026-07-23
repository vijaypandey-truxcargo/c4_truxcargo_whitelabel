<?php

namespace App\Controllers\Admin;
use App\Controllers\Admin\Secure;

class ActivityLog extends Secure
{
    function __construct() {
        parent::__construct();
    }

    public function index($count = 0)
    {
        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        try {
            $total = db_connect()->table('activity_logs')->countAllResults();
            $logs = db_connect()->table('activity_logs')
                ->orderBy('id', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResult();
        } catch (\Throwable $exception) {
            $total = 0;
            $logs = [];
        }

        $pager = service('pager');
        $data = [
            'page_title' => 'Activity Logs',
            'body'       => 'bg-light',
            'data'       => $this->supportModel->find_col('config', 'logo', 1),
            'wconfig'    => $this->wconfig,
            'admin_user' => session()->get('admin_user'),
            'permission' => $this->permission,
            'count'      => $offset,
            'links'      => $pager->makeLinks($page, $perPage, $total),
            'logs'       => $logs,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view('admin/master/activity_logs', $data)
            . view('admin/footer');
    }

    public function user_login_activity($count = 0)
    {
        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $selectedUserName = '';
        $selectedUserType = '';

        if (! $this->request->getPost('reset')) {
            $selectedUserName = trim((string) $this->request->getPost('user_name'));
            $selectedUserType = trim((string) $this->request->getPost('login_type'));
        }

        $builder = db_connect()->table('user_login_activity');
        $countBuilder = db_connect()->table('user_login_activity');

        if ($selectedUserName !== '') {
            $builder->like('name', $selectedUserName);
            $countBuilder->like('name', $selectedUserName);
        }

        if ($selectedUserType !== '') {
            $builder->where('login_as', $selectedUserType);
            $countBuilder->where('login_as', $selectedUserType);
        }

        $total = $countBuilder->countAllResults();
        $code = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        $pager = service('pager');
        $data = [
            'page_title'         => 'User Login Activity Report',
            'body'               => 'bg-light',
            'data'               => $this->supportModel->find_col('config', 'logo', 1),
            'wconfig'            => $this->wconfig,
            'admin_user'         => session()->get('admin_user'),
            'permission'         => $this->permission,
            'count'              => $offset,
            'links'              => $pager->makeLinks($page, $perPage, $total),
            'code'               => $code,
            'selected_user_name' => $selectedUserName,
            'selected_user_type' => $selectedUserType,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view('admin/user_login_activity', $data)
            . view('admin/footer');
    }    

    public function software_screen_time_report($count = 0)
    {
        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;
        $selectedUserName = '';

        if (! $this->request->getPost('reset')) {
            $selectedUserName = trim((string) $this->request->getPost('user_name'));
        }

        $builder = db_connect()->table('user_screen_time');
        $countBuilder = db_connect()->table('user_screen_time');
        $userListBuilder = db_connect()->table('users')->select('id,userName');

        if ($selectedUserName !== '') {
            $matchedUsers = db_connect()->table('users')
                ->select('id')
                ->like('userName', $selectedUserName)
                ->get()
                ->getResult();
            $userIds = array_map(static fn ($user) => $user->id, $matchedUsers);

            if ($userIds === []) {
                $builder->where('1 = 0', null, false);
                $countBuilder->where('1 = 0', null, false);
            } else {
                $builder->whereIn('user_id', $userIds);
                $countBuilder->whereIn('user_id', $userIds);
            }
        }

        $total = $countBuilder->countAllResults();
        $code = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        $userList = $userListBuilder->get()->getResult();
        $user_map = [];
        foreach ($userList as $c) {
            $user_map[$c->id] = $c->userName;
        }

        $pager = service('pager');
        $data = [
            'page_title'         => 'Software Screen Time Report',
            'body'               => 'bg-light',
            'data'               => $this->supportModel->find_col('config', 'logo', 1),
            'wconfig'            => $this->wconfig,
            'admin_user'         => session()->get('admin_user'),
            'permission'         => $this->permission,
            'count'              => $offset,
            'links'              => $pager->makeLinks($page, $perPage, $total),
            'code'               => $code,
            'selected_user_name' => $selectedUserName,
            'user_map'           => $user_map,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view('admin/software_screen_time_report', $data)
            . view('admin/footer');
    }
}      
