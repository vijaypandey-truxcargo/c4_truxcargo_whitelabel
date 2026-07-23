<?php

$yesNo = static function ($value): string {
    return in_array(strtoupper(trim((string) $value)), ['1', 'Y', 'YES', 'TRUE', 'ODA', 'EDL'], true) ? 'Yes' : 'No';
};

echo view('admin/pincode/list', [
    'page_title' => $page_title ?? 'Bluedart Surface Pincodes',
    'code' => $code ?? [],
    'count' => $count ?? 0,
    'links' => $links ?? '',
    'permission' => $permission ?? ($GLOBALS['permission'] ?? []),
    'selected_pincode' => $selected_pincode ?? '',
    'import_title' => 'Import Bluedart Surface',
    'sample_title' => 'Sample Format',
    'sample_headers' => [
        'PINCODE',
        'AREA',
        'AREADESC',
        'BRANCH',
        'STATE',
        'STATE_CODE',
        'PURCHASE_ZONE',
        'SFC_SERVICE',
        'SFC_SERV_LOC_INB',
        'SFC_SERV_LOC_OUTB',
        'SFC_TAT',
        'EDL_SFC',
        'SFC_EDL_DIST',
    ],
    'sample_rows' => [
        ['700000', 'CCU', 'KOLKATA', 'CCU', 'WEST BENGAL', 'WB', 'E2', 'Both', 'ERO', 'PST', '168', 'True', '10'],
        ['700004', 'CCU', 'KOLKATA', 'CCU', 'WEST BENGAL', 'WB', 'E2', 'Both', 'ERO', 'PST', '168', 'False', '0'],
    ],
    'columns' => [
        ['label' => 'Pincode', 'value' => 'pincode'],
        ['label' => 'Area', 'value' => 'area'],
        ['label' => 'Area Desc', 'value' => 'area_desc'],
        ['label' => 'Branch', 'value' => 'branch'],
        ['label' => 'State', 'value' => 'state'],
        ['label' => 'State Code', 'value' => 'state_code'],
        ['label' => 'Purchase Zone', 'value' => 'purchase_zone'],
        ['label' => 'EDL SFC', 'value' => static fn ($row): string => $yesNo($row->eld_sfc ?? '')],
        ['label' => 'SFC EDL DIST', 'value' => 'sfc_eld_dist'],
    ],
    'search_placeholder' => 'Search By Pincode',
    'search_route' => 'admin/pincode/bluedart_surface',
    'import_route' => 'admin/pincode/import_bluedart_surface',
    'delete_route' => 'admin/pincode/delete_bluedart_surface',
    'export_route' => 'admin/pincode/export_bluedart_surface',
]);
