<?php

echo view('admin/pincode/list', [
    'page_title' => $page_title ?? 'Criticallog Pincodes',
    'code' => $code ?? [],
    'count' => $count ?? 0,
    'links' => $links ?? '',
    'permission' => $permission ?? ($GLOBALS['permission'] ?? []),
    'selected_pincode' => $selected_pincode ?? '',
    'import_title' => 'Import Pincode',
    'sample_title' => 'Sample Format',
    'sample_headers' => ['Pincode', 'State Code', 'Service Type', 'Controlling City Name', 'Purchase Zone'],
    'sample_rows' => [
        ['401210', 'MH', 'ODA', 'BOM - Mumbai Hub (O)', 'WEST2'],
        ['700001', 'WB', 'SERVICEABLE', 'CCU - Kolkata Hub (O)', 'EAST1'],
        ['243641', 'UP', 'NON SERVICEABLE', 'BLY - Bareilly Hub (R)', 'NORTH2'],
    ],
    'columns' => [
        ['label' => 'Pincode', 'value' => 'pincode'],
        ['label' => 'State Code', 'value' => 'state_code'],
        ['label' => 'Service Type', 'value' => 'service_type'],
        ['label' => 'Controlling City Name', 'value' => 'controlling_city_name'],
        ['label' => 'Purchase Zone', 'value' => 'purchase_zone'],
    ],
    'search_placeholder' => 'Search By Pincode',
    'search_route' => 'admin/pincode/criticalLog',
    'import_route' => 'admin/pincode/import_critical_log',
    'delete_route' => 'admin/pincode/delete_critical_log',
    'export_route' => 'admin/pincode/export_critical_log',
]);
