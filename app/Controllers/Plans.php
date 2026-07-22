<?php

namespace App\Controllers;

use App\Controllers\Admin\Secure;

class Plans extends Secure
{
    public function index()
    {
        $this->upper('Shipping Plans');

        $activePlan = $this->supportModel->search_col(
            'buy_plan',
            'plan,date,edate',
            [
                'login_id' => session()->get('login_id'),
                'status'   => 'Active',
            ]
        );

        try {
            $plans = $this->supportModel->show('plan', 'ASC');
        } catch (\Throwable $exception) {
            $plans = [];
        }

        $data1 = $this->supportModel->search('plan', ['title' => 'Startup']) ?? (object) [];
        $data2 = $this->supportModel->search('plan', ['title' => 'Small Business']) ?? (object) [];
        $data3 = $this->supportModel->search('plan', ['title' => 'Enterprise']) ?? (object) [];

        $data = [
            'active_plan' => $activePlan,
            'plans'       => $plans,
            'data1'       => $data1,
            'data2'       => $data2,
            'data3'       => $data3,
            'user'        => $this->user,
            'wallet'      => $this->wallet,
            'wconfig'     => $this->wconfig,
            'plan'        => $this->plan,
        ];

        return view('admin/plans', $data)
            . view('footer', $data);
    }
}
