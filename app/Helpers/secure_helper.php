<?php

use App\Models\SupportModel;
use App\Models\UserModel;

if (! function_exists('support_model')) {
    function support_model(): SupportModel
    {
        return new SupportModel();
    }
}

if (! function_exists('user_model')) {
    function user_model(): UserModel
    {
        return new UserModel();
    }
}

if (! function_exists('format_date')) {
    function format_date($date, string $format = 'd M Y')
    {
        if (empty($date)) {
            return '';
        }

        return date($format, is_numeric($date) ? (int) $date : strtotime($date));
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime($date, string $format = 'd M Y H:i:s')
    {
        if (empty($date)) {
            return '';
        }

        return date($format, is_numeric($date) ? (int) $date : strtotime($date));
    }
}

if (! function_exists('current_customer')) {
    function current_customer()
    {
        return session()->get('customer_user');
    }
}

if (! function_exists('customer_name')) {
    function customer_name()
    {
        $customer = current_customer();

        return $customer ? ($customer->first . ' ' . $customer->last) : null;
    }
}

if (! function_exists('b2b_address')) {
    function b2b_address($id)
    {
        return support_model()->find('address', $id);
    }
}

if (! function_exists('pickhouse')) {
    function pickhouse($id)
    {
        return support_model()->find('pickup', $id);
    }
}

if (! function_exists('register_user')) {
    function register_user($id)
    {
        return support_model()->find_col(
            'registration',
            'email,first,last,company,gst,address,state,gstpercentage,gst,pincode,aadhar',
            $id
        );
    }
}

if (! function_exists('upper')) {
    function upper($title)
    {
        $loginId = session()->get('login_id');
        $support = support_model();
        $user = user_model()->profile($loginId);
        $wallet = user_model()->wallet($loginId);
        $bank = $support->show_condition('bank', 'ASC', ['login_id' => $loginId]);

        return view('head', ['page_title' => $title, 'body' => ''])
            . view('menu', ['user' => $user])
            . view('header', ['user' => $user, 'wallet' => $wallet, 'bank' => $bank]);
    }
}

if (! function_exists('upper_new')) {
    function upper_new($title)
    {
        $loginId = session()->get('login_id');
        $user = user_model()->profile($loginId);
        $wallet = user_model()->wallet($loginId);

        return view('head', ['page_title' => $title, 'body' => ''])
            . view('header', ['user' => $user, 'wallet' => $wallet])
            . view('menu1', ['user' => $user]);
    }
}

if (! function_exists('token')) {
    function token($username, $password)
    {
        $token = support_model()->search_col('b2b-partner', 'jwt', [
            'username' => $username,
            'password' => $password,
        ]);

        return $token ? $token->jwt : null;
    }
}

if (! function_exists('job_status')) {
    function job_status($jobId, $token)
    {
        // TODO: implement the external partner API request here
        return [];
    }
}

if (! function_exists('job_process')) {
    function job_process()
    {
        $support = support_model();
        $loginId = session()->get('login_id');
        if (! $loginId) {
            return;
        }

        $today = date('Y-m-d');
        $condition = 'job_id="" and login_id="' . $loginId . '" and date NOT LIKE "%' . $today . '%"';
        $support->delete_condition('order_waybills', $condition);

        $num = $support->getRows('order_waybills', ['login_id' => $loginId, 'status' => 'Processing']);

        if ($num > 0) {
            $items = $support->select_rows_limit('order_waybills', 'id,job_id,panel', 'ASC', '3', ['login_id' => $loginId, 'status' => 'Processing']);

            foreach ($items as $item) {
                $id = $item->id;
                $jobId = $item->job_id;
                $api = $support->search_col('b2b-partner', 'jwt', ['title' => $item->panel]);
                $token = $api ? $api->jwt : null;
                $result = job_status($jobId, $token);

                if (! empty($result['status']) && $result['status'] === 'Complete' && array_key_exists('lrnum', $result)) {
                    $post = [
                        'lrnum' => $result['lrnum'],
                        'status' => $result['status'],
                        'waybills' => $result['waybills'] ?? null,
                    ];

                    $support->update('order_waybills', $post, $id);

                    $reason = 'Debit For Order Creation LR No : ' . $post['lrnum'];
                    $support->update_condition('wallet', ['reason' => $reason], ['order_id' => $jobId]);
                }
            }
        }
    }
}

if (! function_exists('job_process1')) {
    function job_process1()
    {
        $support = support_model();
        $loginId = session()->get('login_id');
        if (! $loginId) {
            return;
        }

        $num = $support->getRows('order_waybills', ['add_by' => $loginId, 'status' => 'Processing']);

        if ($num > 0) {
            $items = $support->select_rows_limit('order_waybills', 'id,job_id,panel', 'ASC', '2', ['login_id' => $loginId, 'status' => 'Processing']);

            foreach ($items as $item) {
                $id = $item->id;
                $jobId = $item->job_id;
                $api = $support->search_col('b2b-partner', 'jwt', ['title' => $item->panel]);
                $token = $api ? $api->jwt : null;
                $result = job_status($jobId, $token);

                if (! empty($result['status']) && $result['status'] === 'Complete') {
                    $post = [
                        'lrnum' => $result['lrnum'] ?? null,
                        'status' => $result['status'],
                        'waybills' => $result['waybills'] ?? null,
                    ];

                    $support->update('order_waybills', $post, $id);

                    $reason = 'Debit For Order Creation LR No : ' . $post['lrnum'];
                    $support->update_condition('wallet', ['reason' => $reason], ['order_id' => $jobId]);
                }
            }
        }
    }
}

if (! function_exists('curl_get')) {
    function curl_get($url, $postData = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (! empty($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }
}

if (! function_exists('state')) {
    function state($pincode)
    {
        $url = base_url("api/apic/state/{$pincode}");
        $result = curl_get($url);

        return $result['data'] ?? null;
    }
}

if (! function_exists('lrnum')) {
    function lrnum($lr)
    {
        return support_model()->getRows('lrnumber', ['lr' => $lr, 'status' => 1]);
    }
}

if (! function_exists('orating')) {
    function orating($id, $table)
    {
        $data = support_model()->find_col($table, 'overall', $id);

        $rate1 = $data->overall * 10;
        $rate = round($rate1 / 10) * 10;

        return '<div class="progress1">'
            . '<div><span class="stars-container stars-' . $rate . '">★★★★★</span></div>'
            . '</div>';
    }
}

if (! function_exists('percentage')) {
    function percentage($param)
    {
        $rate = round($param * 10);

        if ($param >= 9) {
            $color = 'green';
        } elseif ($param < 9 && $param >= 5) {
            $color = 'orange';
        } else {
            $color = 'red';
        }

        return ['rate' => $rate, 'color' => $color];
    }
}

if (! function_exists('warehouse_check')) {
    function warehouse_check($pincode)
    {
        $loginId = session()->get('login_id');
        $count = support_model()->getRows('pickup', [
            'login_id' => $loginId,
            'pincode' => $pincode,
            'status' => 'On',
        ]);

        return $count > 0 ? 1 : 0;
    }
}

if (! function_exists('csv')) {
    function csv($name = '')
    {
        $title = empty($name) ? 'report' : $name . '_report';

        return '<script>'
            . 'function downloadCSV(csv, filename) {'
            . 'var csvFile; var downloadLink;'
            . 'csvFile = new Blob([csv], {type: "text/csv"});'
            . 'downloadLink = document.createElement("a");'
            . 'downloadLink.download = filename;'
            . 'downloadLink.href = window.URL.createObjectURL(csvFile);'
            . 'downloadLink.style.display = "none";'
            . 'document.body.appendChild(downloadLink);'
            . 'downloadLink.click();'
            . '}'
            . 'function exportTableToCSV(filename) {'
            . 'var table_id = "report"; var csv = [];'
            . 'var rows = document.querySelectorAll("table#" + table_id + " tr");'
            . 'for (var i = 0; i < rows.length; i++) {'
            . 'var row = [], cols = rows[i].querySelectorAll("td, th");'
            . 'for (var j = 0; j < cols.length; j++) row.push(cols[j].innerText);'
            . 'csv.push(row.join(","));'
            . '}'
            . 'downloadCSV(csv.join("\n"), filename);'
            . '}'
            . 'exportTableToCSV("' . $title . '.csv"); window.close();'
            . '</script>';
    }
}

if (! function_exists('multicsv')) {
    function multicsv($name = '')
    {
        return '<script src="' . base_url('assets/js/multicsv.js') . '"></script>';
    }
}

if (! function_exists('delhivery_label')) {
    function delhivery_label($lrnum)
    {
        $loginId = session()->get('login_id');
        $user = user_model()->profile($loginId);

        $lab = '<a href="' . base_url("order/label/a4/{$lrnum}") . '" target="_blank" class="btn"><i class="fa fa-barcode"></i> &nbsp; Shipping Label (A4 Size)</a>'
            . '<a href="' . base_url("order/label/sm/{$lrnum}") . '" target="_blank" class="btn"><i class="fa fa-barcode"></i> &nbsp; Shipping Label (4\"X2")</a>'
            . '<a href="' . base_url("order/label/md/{$lrnum}") . '" target="_blank" class="btn"><i class="fa fa-barcode"></i> &nbsp; Shipping Label (4\"X2.5")</a>'
            . '<a href="' . base_url("order/label/std/{$lrnum}") . '" target="_blank" class="btn"><i class="fa fa-barcode"></i> &nbsp; Shipping Label (3\"X2")</a>';

        if (empty($user->label)) {
            return $lab;
        }

        if ($user->label === 'a4') {
            $label = '(A4 Size)';
        } elseif ($user->label === 'sm' || $user->label === 'sm3') {
            $label = '(4"X2)';
        } elseif ($user->label === 'std') {
            $label = '(3"X2)';
        } elseif ($user->label === 'md' || $user->label === 'md3') {
            $label = '(4"X2.5)';
        } else {
            $label = '(3"X3)';
        }

        if (in_array($user->label, ['a4', 'sm', 'md', 'std'], true)) {
            $label = '<div id="d1' . $lrnum . '">'
                . '<a href="' . base_url("order/label/{$user->label}/{$lrnum}") . '" target="_blank" class="btn"><i class="fa fa-barcode"></i> &nbsp; Shipping Label ' . $label . '</a>';
        } else {
            $label = '<div id="d1' . $lrnum . '">';
        }

        $label .= '<span class="bold" style="cursor: pointer; font-size: 13px; text-align: left; display: block; padding: 6px 12px;" id="delhivery_label' . $lrnum . '"><i class="fa fa-chevron-down"></i> &nbsp;  Change Label Size</span></div>';
        $label .= '<div id="d2' . $lrnum . '" style="display:none">' . $lab . '</div>';
        $label .= "<script>$('#delhivery_label{$lrnum}').click(function(event) { $('#d1{$lrnum}').css('display', 'none'); $('#d2{$lrnum}').css('display', 'block'); });</script>";

        return $label;
    }
}
