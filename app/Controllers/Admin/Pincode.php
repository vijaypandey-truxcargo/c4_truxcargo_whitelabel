<?php

namespace App\Controllers\Admin;
use App\Controllers\Admin\Secure;

class Pincode extends Secure
{
    private const PER_PAGE = 10;

    private const PAGES = [
        'criticalLog' => [
            'table' => 'criticalog_pins',
            'view' => 'admin/criticallog_pincode',
            'title' => 'Criticallog Pincodes',
            'route' => 'criticalLog',
            'filename' => 'critical_log_pincodes',
        ],
        'bluedart_surface' => [
            'table' => 'bluedart_surface',
            'view' => 'admin/bluedart_surface',
            'title' => 'Bluedart Surface Pincodes',
            'route' => 'bluedart_surface',
            'filename' => 'bluedart_surface_pincodes',
        ],
        'bluedart_air' => [
            'table' => 'bluedart_air',
            'view' => 'admin/bluedart_air',
            'title' => 'Bluedart Air Pincodes',
            'route' => 'bluedart_air',
            'filename' => 'bluedart_air_pincodes',
        ],
        'bluedart_dp' => [
            'table' => 'bluedart_dp',
            'view' => 'admin/bluedart_dp',
            'title' => 'Bluedart DP Pincodes',
            'route' => 'bluedart_dp',
            'filename' => 'bluedart_dp_pincodes',
        ],
        'bluedart' => [
            'table' => 'bluedart_pins',
            'view' => 'admin/bluedart_pincode',
            'title' => 'Bluedart Pincodes',
            'route' => 'bluedart',
            'filename' => 'bluedart_pincodes',
        ],
        'delhivery_pincode' => [
            'table' => 'delhivery_pincode',
            'view' => 'admin/delhivery_pincode',
            'title' => 'Delhivery Pincodes',
            'route' => 'delhivery_pincode',
            'filename' => 'delhivery_pincodes',
        ],
        'xpressbees_pincode' => [
            'table' => 'xpressbees_pincode',
            'view' => 'admin/xpressbees_pincode',
            'title' => 'Xpressbees Pincodes',
            'route' => 'xpressbees_pincode',
            'filename' => 'xpressbees_pincodes',
        ],
        'sales_pincode' => [
            'table' => 'sales_pincode',
            'view' => 'admin/sales_pincode',
            'title' => 'Sales Pincodes',
            'route' => 'sales_pincode',
            'filename' => 'sales_pincodes',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        ini_set('memory_limit', '512M');
    }

    public function criticalLog(int $page = 1)
    {
        return $this->listPage('criticalLog', $page);
    }

    public function bluedart_surface(int $page = 1)
    {
        return $this->listPage('bluedart_surface', $page);
    }

    public function bluedart_air(int $page = 1)
    {
        return $this->listPage('bluedart_air', $page);
    }

    public function bluedart_dp(int $page = 1)
    {
        return $this->listPage('bluedart_dp', $page);
    }

    public function bluedart(int $page = 1)
    {
        return $this->listPage('bluedart', $page);
    }

    public function delhivery_pincode(int $page = 1)
    {
        return $this->listPage('delhivery_pincode', $page);
    }

    public function xpressbees_pincode(int $page = 1)
    {
        return $this->listPage('xpressbees_pincode', $page);
    }

    public function sales_pincode(int $page = 1)
    {
        return $this->listPage('sales_pincode', $page);
    }

    public function import_critical_log()
    {
        return $this->importGeneric('criticalLog', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['Pincode', 'PINCODE']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'state_code' => $this->rowValue($row, ['State Code', 'STATE_CODE']),
                'service_type' => $this->rowValue($row, ['Service Type', 'SERVICE_TYPE']),
                'controlling_city_name' => $this->rowValue($row, ['Controlling City Name', 'CONTROLLING_CITY_NAME']),
                'purchase_zone' => $this->rowValue($row, ['Purchase Zone', 'PURCHASE_ZONE']),
            ];
        });
    }

    public function import_bluedart_surface()
    {
        return $this->importGeneric('bluedart_surface', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'area' => $this->rowValue($row, ['AREA']),
                'area_desc' => $this->rowValue($row, ['AREADESC', 'AREA DESC', 'AREA DESCRIPTION']),
                'branch' => $this->rowValue($row, ['BRANCH']),
                'state' => $this->rowValue($row, ['STATE']),
                'state_code' => $this->rowValue($row, ['STATE_CODE', 'STATE CODE']),
                'purchase_zone' => $this->rowValue($row, ['PURCHASE_ZONE', 'PURCHASE ZONE']),
                'sfc_service' => $this->rowValue($row, ['SFC_SERVICE', 'SFC SERVICE']),
                'sfc_serv_loc_inb' => $this->rowValue($row, ['SFC_SERV_LOC_INB', 'SFC SERV LOC INB']),
                'sfc_serv_loc_outb' => $this->rowValue($row, ['SFC_SERV_LOC_OUTB', 'SFC SERV LOC OUTB']),
                'sfc_tat' => $this->rowValue($row, ['SFC_TAT', 'SFC TAT']),
                'eld_sfc' => $this->yesNoFlag($this->rowValue($row, ['EDL_SFC', 'ELD_SFC', 'EDL SFC'])),
                'sfc_eld_dist' => $this->rowValue($row, ['SFC_EDL_DIST', 'SFC EDL DIST']),
            ];
        });
    }

    public function import_bluedart_air()
    {
        return $this->importGeneric('bluedart_air', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['CPINCODE', 'PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'area' => $this->rowValue($row, ['CAREA', 'AREA']),
                'area_desc' => $this->rowValue($row, ['CAREADESC', 'AREA DESC', 'AREA DESCRIPTION']),
                'branch' => $this->rowValue($row, ['CBRANCH', 'BRANCH']),
                'state' => $this->rowValue($row, ['CSTATE', 'STATE']),
                'purchase_zone' => $this->rowValue($row, ['PURCHASE ZONE', 'PURCHASE_ZONE']),
                'sale_zone' => $this->rowValue($row, ['SALE ZONE', 'SALE_ZONE']),
                'apeserv' => $this->rowValue($row, ['CAPESERV', 'APESERV']),
                'code' => $this->rowValue($row, ['CODE', 'code', 'APEX_SERV_LOC_INB', 'APEX SERV LOC INB']),
                'code1' => $this->rowValue($row, ['CODE1', 'code1', 'APEX_SERV_LOC_OUTB', 'APEX SERV LOC OUTB']),
                'tclserv' => $this->rowValue($row, ['CTCLSERV', 'TCLSERV']),
                'aptdD12' => $this->rowValue($row, ['CAPTDD12', 'APTDD12']),
                'apex_tat' => $this->rowValue($row, ['APEX TAT', 'APEX_TAT']),
                'edl_apex' => $this->rowValue($row, ['EDL_APEX', 'EDL APEX']),
                'apex_edl_dist' => $this->rowValue($row, ['EDL_APEX_DIST', 'APEXEDLDIST', 'APEX_EDL_DIST']),
            ];
        });
    }

    public function import_bluedart_dp()
    {
        return $this->importGeneric('bluedart_dp', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'area' => $this->rowValue($row, ['AREA']),
                'area_desc' => $this->rowValue($row, ['AREA DESCRIPTION', 'AREA_DESC']),
                'location' => $this->rowValue($row, ['LOCATION']),
                'loc_desc' => $this->rowValue($row, ['LOC DESCRIPTION', 'LOC_DESC']),
                'branch' => $this->rowValue($row, ['BRANCH']),
                'state' => $this->rowValue($row, ['STATE']),
                'state_code' => $this->rowValue($row, ['STATE CODE', 'STATE_CODE']),
                'purchase_zone' => $this->rowValue($row, ['PURCHASE ZONE', 'PURCHASE_ZONE']),
                'edl_dp' => $this->rowValue($row, ['EDL_DP', 'EDL DP']),
                'dp_service' => $this->rowValue($row, ['DP SERVICE', 'DP_SERVICE']),
                'code' => $this->rowValue($row, ['CODE']),
                'code1' => $this->rowValue($row, ['CODE1']),
                'tat' => $this->rowValue($row, ['TAT']),
            ];
        });
    }

    public function import_bluedart()
    {
        return $this->importGeneric('bluedart', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'state' => $this->rowValue($row, ['STATE', 'State']),
                'oda_air' => $this->rowValue($row, ['ODA']),
                'code' => $this->rowValue($row, ['CODE', 'Code']),
                'code1' => $this->rowValue($row, ['CODE1', 'Code1']),
                'distance_air' => $this->rowValue($row, ['DISTANCE', 'Distance']),
                'air' => 'Yes',
            ];
        });
    }

    public function surface_bluedart()
    {
        return $this->importGeneric('bluedart', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'state' => $this->rowValue($row, ['STATE', 'State']),
                'oda_sfc' => $this->rowValue($row, ['ODA']),
                'code' => $this->rowValue($row, ['CODE', 'Code']),
                'code1' => $this->rowValue($row, ['CODE1', 'Code1']),
                'distance_sfc' => $this->rowValue($row, ['DISTANCE', 'Distance']),
                'sfc' => 'Yes',
            ];
        });
    }

    public function import_delhivery_pincode()
    {
        return $this->importGeneric('delhivery_pincode', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'origin' => $this->rowValue($row, ['ORIGIN']),
                'delivery' => $this->rowValue($row, ['DELIVERY']),
                'city' => $this->rowValue($row, ['CITY']),
                'code' => $this->rowValue($row, ['CODE']),
                'state' => $this->rowValue($row, ['STATE']),
                'green' => $this->rowValue($row, ['GREEN']),
                'zone' => $this->rowValue($row, ['ZONE']),
            ];
        });
    }

    public function import_xpressbees_pincode()
    {
        return $this->importGeneric('xpressbees_pincode', function (array $row): ?array {
            $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
            if ($pincode === '') {
                return null;
            }

            return [
                'pincode' => $pincode,
                'state' => strtoupper($this->rowValue($row, ['STATE'])),
                'oda' => $this->yesNoFlag($this->rowValue($row, ['ODA'])),
                'sale_zone' => $this->rowValue($row, ['SALE_ZONE', 'SALE ZONE']),
                'oda_km' => $this->rowValue($row, ['ODA_KM', 'ODA KM']),
            ];
        });
    }

    public function import_sales_pincode()
    {
        $serviceIds = $this->serviceIdByCode();
        $missingServices = [];

        return $this->importGeneric(
            'sales_pincode',
            function (array $row) use ($serviceIds, &$missingServices): ?array {
                $pincode = $this->rowValue($row, ['PINCODE', 'Pincode']);
                $serviceCode = strtoupper($this->rowValue($row, ['SERVICE_CODE', 'SERVICE CODE']));

                if ($pincode === '' || $serviceCode === '') {
                    return null;
                }

                if (! isset($serviceIds[$serviceCode])) {
                    $missingServices[] = $serviceCode;
                    return null;
                }

                return [
                    'pincode' => $pincode,
                    'state' => strtoupper($this->rowValue($row, ['STATE'])),
                    'oda' => $this->yesNoFlag($this->rowValue($row, ['ODA'])),
                    'oda_distance' => $this->rowValue($row, ['ODA_KM', 'ODA KM']),
                    'sale_zone' => $this->rowValue($row, ['SALE_ZONE', 'SALE ZONE']),
                    'service_id' => $serviceIds[$serviceCode],
                ];
            },
            ['pincode', 'service_id'],
            static function () use (&$missingServices): string {
                $missingServices = array_unique(array_filter($missingServices));
                return $missingServices === []
                    ? ''
                    : 'Service Not Found: ' . implode(', ', $missingServices);
            }
        );
    }

    public function delete_critical_log()
    {
        return $this->deleteRecord('criticalLog');
    }

    public function delete_bluedart_surface()
    {
        return $this->deleteRecord('bluedart_surface');
    }

    public function delete_bluedart_air()
    {
        return $this->deleteRecord('bluedart_air');
    }

    public function delete_bluedart_dp()
    {
        return $this->deleteRecord('bluedart_dp');
    }

    public function delete_bluedart()
    {
        return $this->deleteRecord('bluedart');
    }

    public function delete_delhivery_pincode()
    {
        return $this->deleteRecord('delhivery_pincode');
    }

    public function delete_xpressbees_pincode()
    {
        return $this->deleteRecord('xpressbees_pincode');
    }

    public function delete_sales_pincode()
    {
        return $this->deleteRecord('sales_pincode');
    }

    public function export_critical_log()
    {
        return $this->exportPage('criticalLog');
    }

    public function export_bluedart_surface()
    {
        return $this->exportPage('bluedart_surface');
    }

    public function export_bluedart_air()
    {
        return $this->exportPage('bluedart_air');
    }

    public function export_bluedart_dp()
    {
        return $this->exportPage('bluedart_dp');
    }

    public function export_bluedart()
    {
        return $this->exportPage('bluedart');
    }

    public function export_delhivery_pincode()
    {
        return $this->exportPage('delhivery_pincode');
    }

    public function export_xpressbees_pincode()
    {
        return $this->exportPage('xpressbees_pincode');
    }

    public function export_sales_pincode()
    {
        return $this->exportPage('sales_pincode');
    }

    private function listPage(string $pageKey, int $routePage = 1)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $config = $this->pageConfig($pageKey);
        $page = max(1, (int) ($this->request->getGet('page') ?: $routePage));
        $offset = ($page - 1) * self::PER_PAGE;
        $selectedPincode = trim((string) $this->request->getGetPost('pincode'));

        $db = db_connect();
        $applyFilter = static function ($builder) use ($selectedPincode): void {
            if ($selectedPincode !== '') {
                $builder->where('pincode', $selectedPincode);
            }
        };

        $countBuilder = $db->table($config['table']);
        $applyFilter($countBuilder);
        $total = $countBuilder->countAllResults();

        $builder = $db->table($config['table']);
        $applyFilter($builder);
        $rows = $builder
            ->orderBy('id', 'ASC')
            ->limit(self::PER_PAGE, $offset)
            ->get()
            ->getResult();

        return $this->render($config['view'], [
            'page_title' => $config['title'],
            'code' => $rows,
            'count' => $offset,
            'links' => service('pager')->makeLinks($page, self::PER_PAGE, $total),
            'selected_pincode' => $selectedPincode,
            'service_map' => $pageKey === 'sales_pincode' ? $this->serviceCodeById() : [],
        ]);
    }

    private function importGeneric(
        string $pageKey,
        callable $mapper,
        array $uniqueFields = ['pincode'],
        ?callable $extraMessage = null
    ) {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $config = $this->pageConfig($pageKey);
        $redirect = '/admin/pincode/' . $config['route'];

        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || strtolower($file->getClientExtension()) !== 'csv') {
            return redirect()->to($redirect)
                ->with('error', 'Please upload a valid CSV file.')
                ->with('error_class', 'alert-danger');
        }

        $handle = fopen($file->getTempName(), 'r');
        if (! $handle) {
            return redirect()->to($redirect)
                ->with('error', 'Unable to read uploaded CSV file.')
                ->with('error_class', 'alert-danger');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return redirect()->to($redirect)
                ->with('error', 'CSV file is empty.')
                ->with('error_class', 'alert-danger');
        }

        $headers = $this->normalizeHeaders($header);
        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $rowCount = 0;
        $db = db_connect();

        while (($values = fgetcsv($handle)) !== false) {
            if ($this->emptyCsvRow($values)) {
                continue;
            }

            $rowCount++;
            $data = $mapper($this->combineCsvRow($headers, $values));

            if (empty($data)) {
                $skipped++;
                continue;
            }

            $condition = [];
            foreach ($uniqueFields as $field) {
                if (! isset($data[$field]) || trim((string) $data[$field]) === '') {
                    $skipped++;
                    continue 2;
                }

                $condition[$field] = $data[$field];
            }

            $builder = $db->table($config['table']);
            $existing = $builder->select('id')->where($condition)->get()->getRow();

            if ($existing) {
                if ($db->table($config['table'])->where($condition)->update($data)) {
                    $updated++;
                } else {
                    $skipped++;
                }
                continue;
            }

            if ($db->table($config['table'])->insert($data)) {
                $inserted++;
            } else {
                $skipped++;
            }
        }

        fclose($handle);

        $message = $config['title'] . " import complete. Total Rows ({$rowCount}) | Inserted ({$inserted}) | Updated ({$updated}) | Not Inserted ({$skipped})";
        $extra = $extraMessage ? trim((string) $extraMessage()) : '';
        if ($extra !== '') {
            $message .= "\n" . $extra;
        }

        return redirect()->to($redirect)
            ->with('error', $message)
            ->with('error_class', 'alert-success');
    }

    private function deleteRecord(string $pageKey)
    {
        if ($response = $this->guard('Add Pincode')) {
            return $response;
        }

        $config = $this->pageConfig($pageKey);
        $redirect = '/admin/pincode/' . $config['route'];
        $id = (int) $this->request->getPost('id');

        if ($id > 0 && $this->supportModel->delete($config['table'], $id)) {
            return redirect()->to($redirect)
                ->with('error', 'Successfully Deleted.')
                ->with('error_class', 'alert-success');
        }

        return redirect()->to($redirect)
            ->with('error', 'Failed To Delete.')
            ->with('error_class', 'alert-danger');
    }

    private function exportPage(string $pageKey)
    {
        if ($response = $this->guard('View Pincode')) {
            return $response;
        }

        $config = $this->pageConfig($pageKey);
        $columns = $this->exportColumns($pageKey);
        $serviceMap = $pageKey === 'sales_pincode' ? $this->serviceCodeById() : [];
        $stream = fopen('php://temp', 'w+');

        fputcsv($stream, array_merge(['#'], array_column($columns, 'header')));

        $rows = db_connect()->table($config['table'])->orderBy('id', 'ASC')->get()->getResult();
        foreach ($rows as $index => $row) {
            $line = [$index + 1];
            foreach ($columns as $column) {
                $line[] = $this->formatExportValue($row, $column, $serviceMap);
            }
            fputcsv($stream, $line);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $config['filename'] . '_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    private function exportColumns(string $pageKey): array
    {
        $columns = [
            'criticalLog' => [
                ['header' => 'Pincode', 'field' => 'pincode'],
                ['header' => 'State Code', 'field' => 'state_code'],
                ['header' => 'Service Type', 'field' => 'service_type'],
                ['header' => 'Controlling City Name', 'field' => 'controlling_city_name'],
                ['header' => 'Purchase Zone', 'field' => 'purchase_zone'],
            ],
            'bluedart_surface' => [
                ['header' => 'PINCODE', 'field' => 'pincode'],
                ['header' => 'AREA', 'field' => 'area'],
                ['header' => 'AREADESC', 'field' => 'area_desc'],
                ['header' => 'BRANCH', 'field' => 'branch'],
                ['header' => 'STATE', 'field' => 'state'],
                ['header' => 'STATE_CODE', 'field' => 'state_code'],
                ['header' => 'PURCHASE_ZONE', 'field' => 'purchase_zone'],
                ['header' => 'SFC_SERVICE', 'field' => 'sfc_service'],
                ['header' => 'SFC_SERV_LOC_INB', 'field' => 'sfc_serv_loc_inb'],
                ['header' => 'SFC_SERV_LOC_OUTB', 'field' => 'sfc_serv_loc_outb'],
                ['header' => 'SFC_TAT', 'field' => 'sfc_tat'],
                ['header' => 'EDL_SFC', 'field' => 'eld_sfc', 'format' => 'yes_no'],
                ['header' => 'SFC_EDL_DIST', 'field' => 'sfc_eld_dist'],
            ],
            'bluedart_air' => [
                ['header' => 'CPINCODE', 'field' => 'pincode'],
                ['header' => 'CAREA', 'field' => 'area'],
                ['header' => 'CAREADESC', 'field' => 'area_desc'],
                ['header' => 'CBRANCH', 'field' => 'branch'],
                ['header' => 'CSTATE', 'field' => 'state'],
                ['header' => 'PURCHASE ZONE', 'field' => 'purchase_zone'],
                ['header' => 'SALE ZONE', 'field' => 'sale_zone'],
                ['header' => 'CAPESERV', 'field' => 'apeserv'],
                ['header' => 'APEX_SERV_LOC_INB', 'field' => 'code'],
                ['header' => 'APEX_SERV_LOC_OUTB', 'field' => 'code1'],
                ['header' => 'CTCLSERV', 'field' => 'tclserv'],
                ['header' => 'CAPTDD12', 'field' => 'aptdD12'],
                ['header' => 'APEX TAT', 'field' => 'apex_tat'],
                ['header' => 'EDL_APEX', 'field' => 'edl_apex'],
                ['header' => 'EDL_APEX_DIST', 'field' => 'apex_edl_dist'],
            ],
            'bluedart_dp' => [
                ['header' => 'PINCODE', 'field' => 'pincode'],
                ['header' => 'AREA', 'field' => 'area'],
                ['header' => 'AREA DESCRIPTION', 'field' => 'area_desc'],
                ['header' => 'LOCATION', 'field' => 'location'],
                ['header' => 'LOC DESCRIPTION', 'field' => 'loc_desc'],
                ['header' => 'BRANCH', 'field' => 'branch'],
                ['header' => 'STATE', 'field' => 'state'],
                ['header' => 'STATE CODE', 'field' => 'state_code'],
                ['header' => 'PURCHASE ZONE', 'field' => 'purchase_zone'],
                ['header' => 'EDL_DP', 'field' => 'edl_dp'],
                ['header' => 'DP SERVICE', 'field' => 'dp_service'],
                ['header' => 'CODE', 'field' => 'code'],
                ['header' => 'CODE1', 'field' => 'code1'],
                ['header' => 'TAT', 'field' => 'tat'],
            ],
            'bluedart' => [
                ['header' => 'Pincode', 'field' => 'pincode'],
                ['header' => 'State', 'field' => 'state'],
                ['header' => 'SFC', 'field' => 'sfc'],
                ['header' => 'AIR', 'field' => 'air'],
                ['header' => 'ODA SFC', 'field' => 'oda_sfc'],
                ['header' => 'ODA AIR', 'field' => 'oda_air'],
                ['header' => 'Code', 'field' => 'code'],
                ['header' => 'Code1', 'field' => 'code1'],
                ['header' => 'Distance SFC', 'field' => 'distance_sfc'],
                ['header' => 'Distance AIR', 'field' => 'distance_air'],
            ],
            'delhivery_pincode' => [
                ['header' => 'PINCODE', 'field' => 'pincode'],
                ['header' => 'ORIGIN', 'field' => 'origin'],
                ['header' => 'DELIVERY', 'field' => 'delivery'],
                ['header' => 'CITY', 'field' => 'city'],
                ['header' => 'CODE', 'field' => 'code'],
                ['header' => 'STATE', 'field' => 'state'],
                ['header' => 'GREEN', 'field' => 'green'],
                ['header' => 'ZONE', 'field' => 'zone'],
            ],
            'xpressbees_pincode' => [
                ['header' => 'PINCODE', 'field' => 'pincode'],
                ['header' => 'STATE', 'field' => 'state'],
                ['header' => 'ODA', 'field' => 'oda', 'format' => 'yes_no'],
                ['header' => 'SALE_ZONE', 'field' => 'sale_zone'],
                ['header' => 'ODA_KM', 'field' => 'oda_km'],
            ],
            'sales_pincode' => [
                ['header' => 'PINCODE', 'field' => 'pincode'],
                ['header' => 'STATE', 'field' => 'state'],
                ['header' => 'ODA', 'field' => 'oda', 'format' => 'yes_no'],
                ['header' => 'SALE_ZONE', 'field' => 'sale_zone'],
                ['header' => 'SERVICE_CODE', 'field' => 'service_id', 'format' => 'service_code'],
                ['header' => 'ODA_KM', 'field' => 'oda_distance'],
            ],
        ];

        return $columns[$pageKey] ?? [];
    }

    private function formatExportValue(object $row, array $column, array $serviceMap): string
    {
        $field = $column['field'];
        $value = $row->{$field} ?? '';

        if (($column['format'] ?? '') === 'yes_no') {
            return $this->yesNoText($value);
        }

        if (($column['format'] ?? '') === 'service_code') {
            return $serviceMap[$value] ?? '';
        }

        return (string) $value;
    }

    private function normalizeHeaders(array $header): array
    {
        return array_map(fn ($value) => $this->normalizeKey((string) $value), $header);
    }

    private function combineCsvRow(array $headers, array $values): array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            $value = trim((string) ($values[$index] ?? ''));
            $row[$header] = $value;
            $row[str_replace(' ', '_', $header)] = $value;
        }

        return $row;
    }

    private function rowValue(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            $normalized = $this->normalizeKey($key);
            $underscored = str_replace(' ', '_', $normalized);

            if (array_key_exists($normalized, $row) && trim((string) $row[$normalized]) !== '') {
                return trim((string) $row[$normalized]);
            }

            if (array_key_exists($underscored, $row) && trim((string) $row[$underscored]) !== '') {
                return trim((string) $row[$underscored]);
            }
        }

        return '';
    }

    private function normalizeKey(string $key): string
    {
        $key = preg_replace('/^\xEF\xBB\xBF/', '', $key) ?? $key;
        $key = preg_replace('/\s+/', ' ', trim($key)) ?? $key;

        return strtoupper($key);
    }

    private function emptyCsvRow(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function yesNoFlag(string $value): int
    {
        return in_array(strtoupper(trim($value)), ['1', 'Y', 'YES', 'TRUE', 'ODA', 'EDL'], true) ? 1 : 0;
    }

    private function yesNoText($value): string
    {
        return $this->yesNoFlag((string) $value) === 1 ? 'Yes' : 'No';
    }

    private function serviceIdByCode(): array
    {
        $services = db_connect()->table('service')->select('id, code')->get()->getResult();
        $map = [];
        foreach ($services as $service) {
            $code = strtoupper(trim((string) ($service->code ?? '')));
            if ($code !== '') {
                $map[$code] = (int) $service->id;
            }
        }

        return $map;
    }

    private function serviceCodeById(): array
    {
        $services = db_connect()->table('service')->select('id, code')->get()->getResult();
        $map = [];
        foreach ($services as $service) {
            $map[$service->id] = $service->code;
        }

        return $map;
    }

    private function pageConfig(string $pageKey): array
    {
        if (! isset(self::PAGES[$pageKey])) {
            throw new \InvalidArgumentException("Unknown pincode page: {$pageKey}");
        }

        return self::PAGES[$pageKey];
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

    private function render(string $view, array $data): string
    {
        $data += [
            'body' => 'bg-light',
            'wconfig' => $this->wconfig,
            'admin_user' => session()->get('admin_user'),
            'permission' => $this->permission,
        ];

        $GLOBALS['permission'] = $this->permission;

        return view('admin/header', $data)
            . view($view, $data)
            . view('admin/footer');
    }
}
