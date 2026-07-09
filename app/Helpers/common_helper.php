<?php

use App\Models\SupportModel;
use App\Models\UserModel;


if (! function_exists('job_status')) {
    function job_status($jobId, $token)
    {
        $apiurl = 'https://ltl-clients-api.delhivery.com/manifest?job_id=' . trim($jobId);
        $accesstoken = 'Bearer ' . $token;
        $output = curl_get($apiurl, $accesstoken);

        $status = '';
        $waybills = '';
        $lrnum = '';

        if (array_key_exists('success', $output) && $output['success'] && $output['data']['status'] === 'Complete') {
            $waybills = implode(',', $output['data']['waybills']);
            $lrnum = $output['data']['lrnum'];
            $status = $output['data']['status'];
        } elseif (array_key_exists('status', $output) && $output['status']['type'] === 'Complete' && $output['status']['value'] === 'DataError') {
            $status = '';
            $waybills = '';
            $lrnum = 'R';
        } elseif (array_key_exists('status', $output) && $output['status']['type'] === 'Complete' && $output['status']['value'] === 'RemoteError') {
            $status = '';
            $waybills = '';
            $lrnum = 'R';
        } elseif (array_key_exists('message', $output) && $output['message'] === 'Manifestation details not found') {
            $status = '';
            $waybills = '';
            $lrnum = 'R';
        }

        return ['status' => $status, 'lrnum' => $lrnum, 'waybills' => $waybills];
    }
}

if (! function_exists('getState')) {
    function getState($zip)
    {
        $url = base_url("api/apic/state/{$zip}");
        $a = curl_get($url, '');

        return [
            'city' => $a['data']['city'],
            'state' => $a['data']['state'],
            'state_code' => $a['data']['state_code'],
        ];
    }
}

if (! function_exists('token_key')) {
    function token_key($panel)
    {
        $token_key = support_model()->search_col('b2b-partner', 'apikey', ['title' => $panel]);
        return $token_key ? $token_key->apikey : null;
    }
}

if (! function_exists('token_b2c')) {
    function token_b2c($ship)
    {
        $token_key = support_model()->find_col('partner', 'api', $ship);
        return $token_key ? $token_key->api : null;
    }
}

if (! function_exists('b2bmatrix')) {
    function b2bmatrix($panel)
    {
        if ($panel === 'Rivigo') {
            $matrix = 12;
            $pname = $pp = 'Rivigo';
        } elseif ($panel === 'Gati') {
            $matrix = 11;
            $pname = $pp = 'Gati';
        } elseif ($panel === 'Oxyzen') {
            $matrix = 4;
            $pname = $pp = 'Oxyzen';
        } elseif ($panel === 'DTDC B2B') {
            $matrix = 16;
            $pname = $pp = 'DTDC B2B';
        } elseif ($panel === 'Smartr') {
            $matrix = 12;
            $pname = $pp = 'Smartr';
        } elseif ($panel === 'Vxpress') {
            $matrix = 18;
            $pname = $pp = 'Vxpress';
        } elseif ($panel === 'Ekart B2B') {
            $matrix = 5;
            $pname = $pp = 'Ekart B2B';
        } elseif ($panel === 'Bluedart Surface' || $panel === 'Bluedart Air') {
            $matrix = 6;
            $pname = $panel;
            $pp = 'Bluedart';
        } else {
            $pp = 'Delhivery';
            if ($panel === 'Delhivery Dense') {
                $matrix = 9;
                $pname = 'Delhivery Dense';
            } elseif ($panel === 'Delhivery Cargo') {
                $matrix = 16;
                $pname = 'Delhivery Cargo';
            } elseif ($panel === 'Delhivery Economy') {
                $matrix = 9;
                $pname = 'Delhivery Economy';
            } else {
                $matrix = 24;
                $pname = 'Delhivery Lite';
            }
        }

        return ['matrix' => $matrix, 'pname' => $pname, 'pp' => $pp];
    }
}

if (! function_exists('b2bservice')) {
    function b2bservice($pincode1, $pincode2, $pp, $res, $state2, $pname, $bihar)
    {
        $service = 'No';

        if ($pp === 'Delhivery') {
            $show = support_model()->search_col('b2b-partner', 'jwt', ['title' => $pname]);
            if ($show) {
                $url = 'https://ltl-clients-api.delhivery.com/pincode-service/' . $pincode2 . '?weight=10';
                $accesstoken = 'Bearer ' . $show->jwt;
                $output = curl_get($url, $accesstoken, '');
                if (array_key_exists('success', $output) && $output['success']) {
                    $service = 'Yes';
                }
            }

            if ($state2 === 'Bihar' && $bihar === 'No') {
                $service = 'No';
            }
        } elseif ($pp === 'Smartr') {
            $service = support_model()->getRows('smartr_pin', ['pincode' => $pincode1]) > 0 && support_model()->getRows('smartr_pin', ['pincode' => $pincode2]) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'Vxpress') {
            $service = support_model()->getRows('vxpress_pins', ['pincode' => $pincode1]) > 0 && support_model()->getRows('vxpress_pins', ['pincode' => $pincode2]) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'DTDC B2B') {
            $service = support_model()->getRows('dtdc_pins', ['pincode' => $pincode2, 'zone!=' => '']) > 0 && support_model()->getRows('dtdc_pins', ['pincode' => $pincode1, 'zone!=' => '']) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'Ekart B2B') {
            $service = support_model()->getRows('ekart', ['pincode' => $pincode2]) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'Rivigo') {
            $service = support_model()->getRows('rivigopin', ['pincode' => $pincode2, 'delivery' => 'Yes']) > 0 && support_model()->getRows('rivigopin', ['pincode' => $pincode1, 'pickup' => 'Yes']) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'Oxyzen') {
            $service = support_model()->getRows('oxygen', ['pincode' => $pincode2]) > 0 ? 'Yes' : 'No';
        } elseif ($pp === 'Gati') {
            $service = empty($res['errmsg']) && ! empty($res) ? 'Yes' : 'No';
        } elseif ($pp === 'Bluedart') {
            if (support_model()->getRows('bluedart_pins', ['pincode' => $pincode2]) > 0 && $res === 'Prepaid') {
                $kk = support_model()->search('bluedart_pins', ['pincode' => $pincode2]);
                if ($pname === 'Bluedart Surface' && $kk->sfc === 'Yes') {
                    $service = 'Yes';
                } elseif ($pname === 'Bluedart Air' && $kk->air === 'Yes') {
                    $service = 'Yes';
                }
            }
        }

        return $service;
    }
}

if (! function_exists('divisor')) {
    function divisor($panel, $login)
    {
        $num = support_model()->getRows('matrix_charge', ['login_id' => $login, 'panel' => $panel]);
        $condition = $num > 0 ? "login_id='{$login}' and panel='{$panel}'" : "panel='{$panel}' and login_id=''";

        return support_model()->search('matrix_charge', $condition);
    }
}

if (! function_exists('b2b_zone')) {
    function b2b_zone($state1, $state2, $city1, $city2, $panel, $matrix, $partner, $pincode1, $pincode2, $login)
    {
        $zone1 = '';
        $zone2 = '';
        $table = '';

        if ($matrix === 5) {
            $cz1 = "city='{$state1}' and partner like '%{$partner}%'";
            $cz2 = "city='{$state2}' and partner like '%{$partner}%'";
            $z1 = support_model()->search_col('zonearea', 'zone', $cz1);
            $z2 = support_model()->search_col('zonearea', 'zone', $cz2);
            $zone1 = $z1 ? $z1->zone : '';
            $zone2 = $z2 ? str_replace(' ', '', $z2->zone) : '';
            $table = 'price';
        } else {
            if ($panel === 'Rivigo') {
                $table_zone = 'rivigo_zone';
                $table = 'rivigo';
            } elseif ($panel === 'Gati') {
                $table_zone = 'gati_zone';
                $table = 'gati';
            } elseif ($panel === 'Oxyzen') {
                $table_zone = 'oxy_zone';
                $table = 'oxy';
            } elseif ($panel === 'DTDC B2B') {
                $table_zone = 'dtdc_pins';
                $table = 'dtdc_price';
            } elseif ($panel === 'Vxpress') {
                $table_zone = 'vxpress_zone';
                $table = 'vxpress';
            } elseif ($matrix === 24 && $panel !== 'DTDC B2B') {
                $table_zone = 'matrix_zone';
                $table = 'matrix';
            } elseif ($matrix === 9) {
                $table_zone = 'zone_nine';
                $table = 'air_price';
            } elseif ($panel === 'Smartr') {
                $table_zone = 'smartr_zone';
                $table = 'air_price';
            } elseif ($panel === 'Bluedart Surface') {
                $table_zone = 'bluedart_zone';
                $table = 'bluedart_price';
            } elseif ($panel === 'Bluedart Air') {
                $table_zone = 'bluedartair_zone';
                $table = 'bluedart_price';
            } elseif ($matrix === 16 && $panel !== 'DTDC B2B') {
                $table_zone = 'air_zone';
                $table = 'air_price';
            } else {
                $table_zone = 'matrix_zone';
                $table = 'matrix';
            }

            if ($panel !== 'DTDC B2B') {
                $data1 = support_model()->search_col($table_zone, 'zone', ['city' => $city1, 'state' => $state1]);
                if ($data1) {
                    $zone1 = $data1->zone;
                } else {
                    $data1 = support_model()->search($table_zone, ['state' => $state1, 'city' => '']);
                    if ($data1) {
                        $zone1 = $data1->zone;
                    } else {
                        $data1 = support_model()->search($table_zone, ['state' => $state1]);
                        $zone1 = $data1 ? $data1->zone : '';
                    }
                }

                $data2 = support_model()->search_col($table_zone, 'zone', ['city' => $city2, 'state' => $state2]);
                if ($data2) {
                    $zone2 = $data2->zone;
                } else {
                    $data2 = support_model()->search($table_zone, ['state' => $state2, 'city' => '']);
                    if ($data2) {
                        $zone2 = $data2->zone;
                    } else {
                        $data2 = support_model()->search($table_zone, ['state' => $state2]);
                        $zone2 = $data2 ? $data2->zone : '';
                    }
                }
            } else {
                $data1 = support_model()->search_col($table_zone, 'zone', ['pincode' => $pincode1]);
                $zone1 = $data1 ? $data1->zone : '';
                $data2 = support_model()->search_col($table_zone, 'zone', ['pincode' => $pincode2]);
                $zone2 = $data2 ? $data2->zone : '';
            }
        }

        return ['zone1' => $zone1, 'zone2' => $zone2, 'table' => $table];
    }
}

if (! function_exists('price')) {
    function price($zone1, $zone2, $panel, $table, $login)
    {
        $panel = trim(preg_replace('/\s+/', ' ', $panel));

        if ($panel === 'bluedart_air') {
            $panel = 'Bluedart Air';
        } elseif ($panel === 'bluedart_surface') {
            $panel = 'Bluedart Surface';
        }

        $row = support_model()->search($table, [
            'zone' => $zone1,
            'panel' => $panel,
            'login_id' => $login,
        ]);

        if (! $row) {
            $db = Config\Database::connect();
            $builder = $db->table($table);
            $builder->where('zone', $zone1);
            $builder->where('panel', $panel);
            $builder->where('login_id IS NULL', null, false);
            $row = $builder->get()->getRow();
        }

        if (! $row || ! isset($row->$zone2)) {
            return 0;
        }

        return (float) $row->$zone2;
    }
}

if (! function_exists('mincost')) {
    function mincost($panel, $login)
    {
        $mincharge = support_model()->search_col('mincharge', 'amount', ['login_id' => $login, 'panel' => $panel]);
        if (! empty($mincharge)) {
            return $mincharge->amount;
        }

        $mincharge = support_model()->search_col('mincharge', 'amount', ['login_id' => '', 'panel' => $panel]);
        return $mincharge ? $mincharge->amount : 400;
    }
}

if (! function_exists('handling')) {
    function handling($w, $pp, $qty, $hand_charge, $hand_charge1, $hand_charge2)
    {
        $handling = 0;

        if ($w >= 100 && strpos($pp, 'Delhivery') !== false) {
            if ($w <= 250) {
                [$unit, $max] = explode(':', $hand_charge);
                $handling = $qty * $w * $unit >= $max ? $qty * $w * $unit : $max;
            } elseif ($w <= 400) {
                [$unit, $max] = explode(':', $hand_charge1);
                $handling = $qty * $w * $unit >= $max ? $qty * $w * $unit : $max;
            } else {
                [$unit, $max] = explode(':', $hand_charge2);
                $handling = $qty * $w * $unit >= $max ? $qty * $w * $unit : $max;
            }
        } elseif ($pp === 'Gati' || $pp === 'Ekart B2B') {
            $rs = $w <= 70 ? $hand_charge : ($w <= 200 ? $hand_charge1 : $hand_charge2);
            $handling = $qty * $rs;
        } elseif ($pp === 'DTDC B2B') {
            $rs = $w <= 100 ? $hand_charge : $hand_charge1;
            $handling = $qty * $rs;
        } elseif ($pp === 'Vxpress') {
            if ($w <= 500) {
                $handling = max($qty * $w * 2, 1000);
            } else {
                $handling = max($qty * $w * 5, 3000);
            }
        } elseif ($pp === 'Bluedart') {
            $rs = $w > 200 ? $hand_charge1 : $hand_charge;
            $handling = $qty * $rs;
        } elseif ($pp === 'Smartr') {
            $rs = $w > 200 ? $hand_charge1 : $hand_charge;
            $handling = $qty * $rs;
        }

        return $handling;
    }
}

if (! function_exists('b2boda')) {
    function b2boda($pincode2, $pp, $weight, $oda_charge, $res, $pname)
    {
        $oda = 0;

        if ($pp === 'Delhivery') {
            $show = support_model()->search_col('b2b-partner', 'jwt', ['title' => $pname]);
            if ($show) {
                $url = 'https://ltl-clients-api.delhivery.com/pincode-service/' . $pincode2 . '?weight=10';
                $accesstoken = 'Bearer ' . $show->jwt;
                $output = curl_get($url, $accesstoken, '');

                if (array_key_exists('success', $output) && $output['success'] && $output['data']['pincode_serviceability_data'][0]['oda']) {
                    $oda = max($oda_charge[0] * $weight, $oda_charge[1]);
                }
            }
        } elseif (support_model()->getRows('oxygen', ['pincode' => $pincode2, 'oda' => 'ODA']) > 0 && $pp === 'Oxyzen') {
            $oda = max($oda_charge[0] * $weight, $oda_charge[1]);
        } elseif ($pp === 'Vxpress') {
            $oda = 0;
            foreach ([$pincode2, $res] as $pin) {
                if (support_model()->getRows('vxpress_pins', ['pincode' => $pin, 'oda!=' => 'No']) > 0) {
                    $ss = support_model()->search('vxpress_pins', ['pincode' => $pin]);
                    $rates = ['ODA A' => [800, 3000], 'ODA B' => [1000, 3500], 'ODA C' => [1500, 4000], 'ODA D' => [2000, 4500]];
                    [$a, $c] = $rates[$ss->oda] ?? [0, 0];
                    $od = max($weight * 2, $a);
                    $oda += min($od, $c);
                }
            }
        } elseif (support_model()->getRows('ekart', ['pincode' => $pincode2, 'oda' => 'Yes']) > 0 && $pp === 'Ekart B2B') {
            $oda = max($oda_charge[0] * $weight, $oda_charge[1]);
        } elseif ($pp === 'Rivigo') {
            $oda = max($oda_charge[0] * $weight, $oda_charge[1]);
        } elseif (support_model()->getRows('dtdc_pins', ['pincode' => $pincode2, 'oda' => 'Yes']) > 0 && $pp === 'DTDC B2B') {
            $distance = support_model()->search_col('dtdc_pins', 'km', ['pincode' => $pincode2, 'oda' => 'Yes'])->km;
            $oda = dtdcOda($distance, $weight);
        } elseif ($pp === 'Gati') {
            $gati = explode(',', implode(',', $res['serviceDtls'][0]));
            $distance = $gati[4];
            $oda = gatiOda($distance, $weight);
        } elseif ($pp === 'Bluedart') {
            $kk = support_model()->search('bluedart_pins', ['pincode' => $pincode2]);
            if ($pname === 'Bluedart Surface' && $kk->oda_sfc === 'EDL') {
                $oda = bluedartOda($kk->distance_sfc, $weight, $res);
            } elseif ($pname === 'Bluedart Air' && $kk->oda_air === 'EDL') {
                $oda = bluedartOda($kk->distance_air, $weight, $res);
            }
        }

        return $oda;
    }
}

if (! function_exists('bluedartOda')) {
    function bluedartOda($distance, $weight, $zone)
    {
        $oda = 0;
        if ($zone === 'NE' || $zone === 'JK') {
            $oda = max($weight * 15, 3000);
        } elseif ($distance <= 50) {
            if ($weight <= 100) {
                $oda = 550;
            } elseif ($weight <= 250) {
                $oda = 990;
            } elseif ($weight <= 500) {
                $oda = 1100;
            } elseif ($weight <= 1000) {
                $oda = 1375;
            } else {
                $oda = 1650;
            }
        } elseif ($distance <= 100) {
            if ($weight <= 100) {
                $oda = 825;
            } elseif ($weight <= 250) {
                $oda = 1210;
            } elseif ($weight <= 500) {
                $oda = 1375;
            } elseif ($weight <= 1000) {
                $oda = 1650;
            } else {
                $oda = 1925;
            }
        } elseif ($distance <= 150) {
            if ($weight <= 100) {
                $oda = 1100;
            } elseif ($weight <= 250) {
                $oda = 1650;
            } elseif ($weight <= 500) {
                $oda = 1925;
            } elseif ($weight <= 1000) {
                $oda = 2200;
            } else {
                $oda = 2750;
            }
        } elseif ($distance <= 200) {
            if ($weight <= 100) {
                $oda = 1375;
            } elseif ($weight <= 250) {
                $oda = 1925;
            } elseif ($weight <= 500) {
                $oda = 2200;
            } elseif ($weight <= 1000) {
                $oda = 2475;
            } else {
                $oda = 3300;
            }
        } elseif ($distance <= 250) {
            if ($weight <= 100) {
                $oda = 1650;
            } elseif ($weight <= 250) {
                $oda = 2200;
            } elseif ($weight <= 500) {
                $oda = 2750;
            } elseif ($weight <= 1000) {
                $oda = 3300;
            } else {
                $oda = 3960;
            }
        } elseif ($distance <= 300) {
            if ($weight <= 100) {
                $oda = 1925;
            } elseif ($weight <= 250) {
                $oda = 2500;
            } elseif ($weight <= 500) {
                $oda = 3125;
            } elseif ($weight <= 1000) {
                $oda = 3800;
            } else {
                $oda = 4560;
            }
        } elseif ($distance <= 350) {
            if ($weight <= 100) {
                $oda = 2200;
            } elseif ($weight <= 250) {
                $oda = 2800;
            } elseif ($weight <= 500) {
                $oda = 3550;
            } elseif ($weight <= 1000) {
                $oda = 4300;
            } else {
                $oda = 5160;
            }
        } elseif ($distance <= 400) {
            if ($weight <= 100) {
                $oda = 2475;
            } elseif ($weight <= 250) {
                $oda = 3100;
            } elseif ($weight <= 500) {
                $oda = 3950;
            } elseif ($weight <= 1000) {
                $oda = 4800;
            } else {
                $oda = 5760;
            }
        } elseif ($distance <= 450) {
            if ($weight <= 100) {
                $oda = 2750;
            } elseif ($weight <= 250) {
                $oda = 3400;
            } elseif ($weight <= 500) {
                $oda = 4350;
            } elseif ($weight <= 1000) {
                $oda = 5300;
            } else {
                $oda = 6360;
            }
        } elseif ($distance <= 500) {
            if ($weight <= 100) {
                $oda = 3025;
            } elseif ($weight <= 250) {
                $oda = 3700;
            } elseif ($weight <= 500) {
                $oda = 4750;
            } elseif ($weight <= 1000) {
                $oda = 5800;
            } else {
                $oda = 6960;
            }
        } elseif ($distance > 500) {
            $oda = $weight * 14;
        } elseif ($weight > 1500) {
            $oda = $weight * 5;
        }

        return $oda;
    }
}

if (! function_exists('gatiOda')) {
    function gatiOda($distance, $weight)
    {
        $oda = 0;

        if ($distance > 25 && $distance <= 100) {
            if ($weight <= 50) {
                $oda = 650;
            } elseif ($weight <= 100) {
                $oda = 750;
            } elseif ($weight <= 250) {
                $oda = 1000;
            } elseif ($weight <= 500) {
                $oda = 1150;
            } elseif ($weight <= 1000) {
                $oda = 1500;
            } else {
                $oda = 1750;
            }
        } elseif ($distance <= 150) {
            if ($weight <= 50) {
                $oda = 1000;
            } elseif ($weight <= 100) {
                $oda = 1250;
            } elseif ($weight <= 250) {
                $oda = 1500;
            } elseif ($weight <= 500) {
                $oda = 1750;
            } elseif ($weight <= 1000) {
                $oda = 2000;
            } else {
                $oda = 2500;
            }
        } elseif ($distance <= 200) {
            if ($weight <= 50) {
                $oda = 1250;
            } elseif ($weight <= 100) {
                $oda = 1500;
            } elseif ($weight <= 250) {
                $oda = 1750;
            } elseif ($weight <= 500) {
                $oda = 2000;
            } elseif ($weight <= 1000) {
                $oda = 2250;
            } else {
                $oda = 3000;
            }
        } elseif ($distance <= 300) {
            if ($weight <= 50) {
                $oda = 1500;
            } elseif ($weight <= 100) {
                $oda = 1750;
            } elseif ($weight <= 250) {
                $oda = 2000;
            } elseif ($weight <= 500) {
                $oda = 2500;
            } elseif ($weight <= 1000) {
                $oda = 3000;
            } else {
                $oda = 3600;
            }
        } else {
            $oda = 8000;
        }

        return $oda;
    }
}

if (! function_exists('dtdcOda')) {
    function dtdcOda($distance, $weight)
    {
        $oda = 0;

        if ($distance <= 75) {
            if ($weight <= 100) {
                $oda = 500;
            } elseif ($weight <= 250) {
                $oda = 800;
            } elseif ($weight <= 500) {
                $oda = 1000;
            } elseif ($weight <= 1000) {
                $oda = 1200;
            } else {
                $oda = 3 * $weight;
            }
        } elseif ($distance <= 150) {
            if ($weight <= 100) {
                $oda = 800;
            } elseif ($weight <= 250) {
                $oda = 1000;
            } elseif ($weight <= 500) {
                $oda = 1500;
            } elseif ($weight <= 1000) {
                $oda = 1700;
            } else {
                $oda = 5 * $weight;
            }
        } elseif ($distance <= 300) {
            if ($weight <= 100) {
                $oda = 1000;
            } elseif ($weight <= 250) {
                $oda = 1200;
            } elseif ($weight <= 500) {
                $oda = 2000;
            } elseif ($weight <= 1000) {
                $oda = 2000;
            } else {
                $oda = 7 * $weight;
            }
        } else {
            $oda = 7 * $weight;
        }

        return $oda;
    }
}

if (! function_exists('suffix')) {
    function suffix($login)
    {
        $suffix = '';
        $buy = support_model()->search_col('buy_plan', 'plan', ['login_id' => $login, 'status' => 'Active']);
        if ($buy) {
            $suffix = $buy->plan;
        }

        $reg = support_model()->find_col('registration', 'plan', $login);
        if (! empty($reg->plan)) {
            $suffix = $reg->plan;
        }

        return trim($suffix);
    }
}

if (! function_exists('suffix_date')) {
    function suffix_date($login, $date)
    {
        $suffix = '';
        $date1 = date('Y-m-d', strtotime($date));
        $buy = support_model()->search_col('buy_plan', 'plan,date,edate', "login_id='{$login}' and '{$date1}' BETWEEN date and edate");
        if ($buy) {
            $suffix = $buy->plan;
        }

        $reg = support_model()->find_col('registration', 'plan', $login);
        if (! empty($reg->plan)) {
            $suffix = $reg->plan;
        }

        return $suffix;
    }
}

if (! function_exists('surface_zone')) {
    function surface_zone($weight, $pincode1, $pincode2)
    {
        $reg = support_model()->search_col('partner', 'api', "partner like '%Delhivery%'");
        if ($reg) {
            $apiurl = 'https://track.delhivery.com/api/kinko/v1/invoice/charges/.json?ss=Delivered&md=S&cgm=' . $weight . '&o_pin=' . $pincode1 . '&d_pin=' . $pincode2;
            $accesstoken = 'Token ' . $reg->api;
            $output = curl_get($apiurl, $accesstoken);
            return $output[0]['zone'] ?? '';
        }

        return '';
    }
}

if (! function_exists('b2c_con')) {
    function b2c_con($weight1, $dtdc)
    {
        $bcondition = ' and partner NOT LIKE "%Ekart 10Kg%" ';

        if ($weight1 < 1) {
            $bcondition .= ' and kg between 0.1 and 1';
        } elseif ($weight1 > 0.4 && $weight1 < 4) {
            $bcondition .= ' and kg between 0.5 and 2';
        } elseif ($weight1 > 3 && $weight1 <= 6) {
            $bcondition .= ' and kg between 1 and 5';
        } elseif ($weight1 > 6 && $weight1 <= 20) {
            $bcondition .= ' and kg between 5 and 20';
        } elseif ($weight1 > 20) {
            $bcondition .= ' and kg between 24 and 60';
        }

        $bcondition .= $dtdc === 'Yes' && $weight1 < 40 ? ' or (partner LIKE "%DTDC%" and api!="")' : ' and partner NOT LIKE "%DTDC%"';

        return $bcondition;
    }
}

if (! function_exists('b2c_active')) {
    function b2c_active($partner, $pincode1, $pincode2, $mode, $invoice_value, $cod_amount)
    {
        $active = 1;

        if (strpos($partner, 'Amazon') !== false) {
            $anum = support_model()->getRows('amazon', ['pincode' => $pincode2]);
            if ($anum == 0 || $invoice_value > 50000 || $cod_amount > 30000) {
                $active = 0;
            }
        } elseif (strpos($partner, 'Xpressbees') !== false) {
            $res = support_model()->getRows('xpress_pin', ['pincode' => $pincode1, 'fm' => 'Yes']);
            $res1 = support_model()->getRows('xpress_pin', ['pincode' => $pincode2, 'lm' => 'Yes']);
            $active = $res && $res1 ? 1 : 0;
        } elseif (strpos($partner, 'Ekart') !== false) {
            if ($partner === 'Ekart 10Kg') {
                $res = support_model()->getRows('ekart10', ['pincode' => $pincode1, 'fm' => 'Yes']);
                $res1 = support_model()->getRows('ekart10', ['pincode' => $pincode2, 'lm' => 'Yes']);
            } else {
                $res = support_model()->getRows('ekartb2c', ['pincode' => $pincode1, 'pickup' => 'Yes']);
                $res1 = support_model()->getRows('ekartb2c', ['pincode' => $pincode2, 'delivery' => 'Yes']);
            }
            $active = $res && $res1 ? 1 : 0;
        } elseif (strpos($partner, 'Bluedart') !== false) {
            $res = support_model()->getRows('bluedart_b2c', ['pincode' => $pincode1, 'fm' => 'Yes']);
            $res1 = support_model()->getRows('bluedart_b2c', ['pincode' => $pincode2, 'lm' => 'Yes']);
            $active = $res && $res1 ? 1 : 0;
        } elseif (strpos($partner, 'DTDC') !== false) {
            $res = support_model()->getRows('dtdc_pins', ['pincode' => $pincode1, 'fm' => 'Yes']);
            $res1 = support_model()->getRows('dtdc_pins', ['pincode' => $pincode2, 'lm' => 'Yes']);
            $active = $res && $res1 ? 1 : 0;
        } elseif (strpos($partner, 'Ecom') !== false) {
            $ecom = support_model()->search_col('ecom', 'city', ['pincode' => $pincode2]);
            $active = ! empty($ecom) ? 1 : 0;
        } elseif (strpos($partner, 'Shree Maruti') !== false) {
            $url = 'https://apis.delcaper.com/fulfillment/public/seller/order/check-ecomm-order-serviceability';
            $data = ['fromPincode' => (int) $pincode2, 'toPincode' => (int) $pincode2, 'isCodOrder' => true, 'deliveryMode' => 'SURFACE'];
            $data_json = json_encode($data);
            $output = curl_post($url, '', $data_json);
            $active = $output['status'] == 200 && $output['data']['serviceability'] ? 1 : 0;
        } elseif (strpos($partner, 'Shadowfax') !== false) {
            $reg = support_model()->search_col('partner', 'api', "partner like '%Shadowfax%'");
            $url = 'https://dale.shadowfax.in/api/v1/clients/serviceability/?service=customer_delivery&pincodes=' . $pincode2;
            $htoken = 'Authorization: Token ' . $reg->api;
            $output = curl_get($url, $htoken, '');
            $active = sizeof($output) > 0 ? 1 : 0;
        } elseif (strpos($partner, 'India Post') !== false) {
            $url = 'https://api.cept.gov.in/CommonFacilityMaster/api/values/Fetch_Facility';
            $data = ['Input_Pincode' => $pincode2];
            $data_json = json_encode($data);
            $output = curl_post($url, '', $data_json);
            $array = json_decode($output, true);
            $active = $array[0]['Validation Status'] == 'Valid Pincode' ? 1 : 0;
        } else {
            $reg = support_model()->search_col('partner', 'api', "partner like '%Delhivery%'");
            $key = $reg->api;
            $url = 'https://track.delhivery.com/c/api/pin-codes/json/?token=' . $key . '&filter_codes=' . $pincode2;
            $res = file_get_contents($url);
            $characters = json_decode($res);
            $active = ! empty($characters->delivery_codes) ? 1 : 0;
        }

        return $active;
    }
}

if (! function_exists('discrepancy')) {
    function discrepancy($condition = '')
    {
        if (empty($condition)) {
            $order_date2 = date('Y-m-d', strtotime('+1 days')); 
            $order_date1 = date('Y-m-d', strtotime('-30 days')); 
            $condition = "login_id= '" . session()->get('login_id') . "' and date between  '$order_date1' and '$order_date2'";
        }

        $res = support_model()->select_rows('weight_recon', 'charged_weight,weight,awb,wallet,previous', 'Desc', $condition);
        $total = 0;
        $accept = 0;

        foreach ($res as $row) {
            if (empty($row->wallet)) {
                $w = $row->charged_weight / 1000 - $row->weight / 1000;
            } else {
                $num = support_model()->getRows('b2b_billing', "lr like '%{$row->awb}%'");
                $num1 = support_model()->getRows('b2b_invoice', "lr like '%{$row->awb}%'");
                $order = support_model()->search_col('order_waybills', 'cweight', "lrnum ='{$row->awb}'");
                $acc = $num > 0 || $num1 > 0 ? $row->charged_weight - $order->cweight : 0;
                if (! empty($row->previous)) {
                    $pp = explode('Weight :', $row->previous);
                    $p = $pp[1] ?? '';
                    if (! empty($p)) {
                        $pre = is_numeric($p) ? trim($p) : trim(explode(')', $p)[0] ?? '');
                        if (is_numeric($pre)) {
                            $w = $pre - $row->weight;
                            $acc = $pre - $row->charged_weight;
                        }
                    } else {
                        $w = $row->charged_weight - $row->weight;
                    }
                } else {
                    $w = $row->charged_weight - $row->weight;
                }
                $accept += $acc;
            }
            $total += $w;
        }

        return [
            'total' => round($total, 2),
            'acc' => round($accept, 2),
            'reject' => round($total - $accept, 2),
        ];
    }
}

if (! function_exists('order_info')) {
    function order_info($awb, $wallet)
    {
        if (empty($wallet)) {
            $res = support_model()->search_col('b2c_waybills', 'ship_with,products_desc,quantity,id,date,order_id,status,payment_mode,topay,ftopay,count,width,height,length,total_amount', ['waybill' => $awb]);
            $s = support_model()->search_col('partner', 'divisor,partner', ['id' => $res->ship_with]);
            $result = [
                'panel' => $s->partner,
                'part' => 'orderb2c',
                'unit' => 'gms',
                'name' => $res->products_desc,
                'qty' => $res->quantity,
                'id' => $res->id,
                'date' => $res->date,
                'order_id' => $res->order_id,
                'order_total' => $res->total_amount,
                'status' => $res->status,
                'divisor' => $s->divisor / 1000,
                'mode' => $res->payment_mode,
            ];
        } else {
            $res = support_model()->search_col('order_waybills', 'panel,description,qty,id,date,order_id,awb_status,d_mode,topay,ftopay,count,width,height,length,n_value', ['lrnum' => $awb]);
            $n_values = explode(',', $res->n_value);
            $total = array_sum($n_values);
            $div = divisor($res->panel, session()->get('login_id'));
            $result = [
                'panel' => $res->panel,
                'part' => 'order',
                'unit' => 'kg',
                'name' => $res->description,
                'qty' => $res->qty,
                'id' => $res->id,
                'date' => $res->date,
                'order_id' => $res->order_id,
                'order_total' => $total,
                'status' => $res->awb_status,
                'divisor' => $div->divisor,
                'mode' => $res->d_mode,
            ];
        }

        $result['count'] = $res->count;
        $result['width'] = $res->width;
        $result['height'] = $res->height;
        $result['length'] = $res->length;

        if ($res->topay === 'Yes') {
            $result['mode'] = 'To-Pay';
        } elseif ($res->ftopay === 'Yes') {
            $result['mode'] = 'Franchise ToPay';
        } elseif ($result['mode'] === 'CoD') {
            $result['mode'] = 'COD';
        }

        return $result;
    }
}

if (! function_exists('curl_post')) {
    function curl_post($url, $accesstoken, $data_json, $htoken = '', $htoken1 = '')
    {
        $header = ['Content-type: application/json', 'Accept: application/json'];
        if (! empty($accesstoken)) {
            $header[] = 'Authorization:' . $accesstoken;
        }
        if (! empty($htoken)) {
            $header[] = $htoken;
        }
        if (! empty($htoken1)) {
            $header[] = $htoken1;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('form_post')) {
    function form_post($url, $accesstoken, $data)
    {
        $header = [];
        if (! empty($accesstoken)) {
            $header[] = 'Authorization:' . $accesstoken;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array_merge($header, ['content-type: multipart/form-data;']),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('curl_put')) {
    function curl_put($url, $accesstoken, $data_json, $htoken = '')
    {
        $header = ['Content-type: application/json', 'Accept: application/json'];
        if (! empty($accesstoken)) {
            $header[] = 'Authorization:' . $accesstoken;
        }
        if (! empty($htoken)) {
            $header[] = $htoken;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => $header,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('curl_get')) {
    function curl_get($url, $accesstoken, $htoken = '')
    {
        $header = ['Content-type: application/json', 'Accept: application/json'];
        if (! empty($accesstoken)) {
            $header[] = 'Authorization: ' . $accesstoken;
        }
        if (! empty($htoken)) {
            $header[] = $htoken;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('ecom_post')) {
    function ecom_post($url, $data_json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('rivigo_post')) {
    function rivigo_post($url, $data_json)
    {
        $rivigo = support_model()->search_col('b2b-partner', 'jwt,apikey', ['title' => 'Rivigo']);
        $header = [
            'Content-type: application/json',
            'Accept: application/json',
            'Authorization: Bearer' . $rivigo->jwt,
            'appUuid: ' . $rivigo->apikey,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('amazon_post')) {
    function amazon_post($uri, $data, $method, $t = '')
    {
        $reg = support_model()->search_col('partner', 'api,client', "partner like '%Amazon%'");
        $host = 'sellingpartnerapi-eu.amazon.com';
        $requestUrl = 'https://' . $host . $uri . (! empty($t) ? '?' . $t : '');
        $aws = support_model()->search('xprestoken', ['title' => 'Amazon']);

        $accessKey = $reg->client;
        $secretKey = $reg->api;
        $region = 'eu-west-1';
        $service = 'execute-api';
        $terminationString = 'aws4_request';
        $algorithm = 'AWS4-HMAC-SHA256';
        $phpAlgorithm = 'sha256';
        $canonicalURI = $uri;
        $canonicalQueryString = $t;
        $signedHeaders = 'content-type;host;x-amz-date';
        $currentDateTime = new DateTime('UTC');
        $reqDate = $currentDateTime->format('Ymd');
        $reqDateTime = $currentDateTime->format('Ymd\THis\Z');

        $kSecret = $secretKey;
        $kDate = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
        $kRegion = hash_hmac($phpAlgorithm, $region, $kDate, true);
        $kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
        $kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

        $canonicalHeaders = [
            'content-type:application/json',
            'host:' . $host,
            'x-amz-date:' . $reqDateTime,
        ];
        $canonicalHeadersStr = implode("\n", $canonicalHeaders);
        $requestHasedPayload = hash($phpAlgorithm, $data);

        $canonicalRequest = implode("\n", [
            $method,
            $canonicalURI,
            $canonicalQueryString,
            $canonicalHeadersStr . "\n",
            $signedHeaders,
            $requestHasedPayload,
        ]);
        $requestHasedCanonicalRequest = hash($phpAlgorithm, utf8_encode($canonicalRequest));

        $credentialScopeStr = implode('/', [$reqDate, $region, $service, $terminationString]);
        $stringToSign = implode("\n", [
            $algorithm,
            $reqDateTime,
            $credentialScopeStr,
            $requestHasedCanonicalRequest,
        ]);

        $signature = hash_hmac($phpAlgorithm, $stringToSign, $kSigning);
        $authorizationHeaderStr = $algorithm . ' Credential=' . $accessKey . '/' . $credentialScopeStr . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;

        $headers = [
            'authorization:' . $authorizationHeaderStr,
            'content-length:' . strlen($data),
            'content-type: application/json',
            'x-amz-date: ' . $reqDateTime,
            'host: ' . $host,
            'x-amz-access-token: ' . $aws->token,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (empty($data) && ! empty($t)) {
            curl_setopt($ch, CURLOPT_POST, 0);
        } elseif (empty($data)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
    }
}

if (! function_exists('getRateModifierByServiceCustomer')) {
    function getRateModifierByServiceCustomer($service_id, $customer_id)
    {
        $today = date('Y-m-d');
        $db = Config\Database::connect();

        $db->select('rm.*, c.id AS charge_id, c.name AS charge_name, c.rate_type, c.is_gst_applicable, c.is_fsc_applicable_sales, c.apply_on_order_creation');
        $db->from('rate_modifier rm');
        $db->join('charge c', 'c.id = rm.charge_id', 'left');
        $db->where('rm.status', 1);
        $db->where('c.apply_on_order_creation', 1);
        $db->where_in('rm.billing_type', ['SALE', 'BOTH']);
        $db->where('rm.effective_from <=', $today);
        $db->where('FIND_IN_SET("' . $service_id . '", rm.service_id) !=', false);
        $db->group_start();
            $db->where('FIND_IN_SET("' . $customer_id . '", rm.customer_id) !=', false);
            $db->or_where('rm.customer_id', '');
            $db->or_where('rm.customer_id IS NULL', null, false);
        $db->group_end();

        return $db->get()->result();
    }
}

if (! function_exists('checkMinMaxValue')) {
    function checkMinMaxValue($value, $min = 0.00, $max = 0.00)
    {
        if ($min > 0 && $value < $min) {
            return (float) $min;
        }

        if ($max > 0 && $value > $max) {
            return (float) $max;
        }

        return $value;
    }
}

if (! function_exists('getVendorsByService')) {
    function getVendorsByService($id)
    {
        $db = Config\Database::connect();
        $service = $db->where('id', $id)->where('status', 1)->get('service')->row();

        if (! $service || empty($service->vendor)) {
            return [];
        }

        $vendor_ids = array_map('intval', explode(',', $service->vendor));
        $vendors = $db->where_in('id', $vendor_ids)->where('status', 1)->get('vendor')->result();

        foreach ($vendors as $v) {
            $v->service_mode = $service->mode;
        }

        return $vendors;
    }
}

if (! function_exists('customerServiceGet')) {
    function customerServiceGet($service_id, $login_id)
    {
        $db = Config\Database::connect();
        $db->from('customer_service_charge');
        $db->where('service_id', $service_id);
        $db->group_start();
            $db->where("FIND_IN_SET('{$login_id}', customer_id) !=", 0);
            $db->or_where('customer_id', '');
            $db->or_where('customer_id IS NULL', null, false);
        $db->group_end();
        $db->where('status', 1);
        $db->order_by('id', 'DESC');

        return $db->get()->row();
    }
}
