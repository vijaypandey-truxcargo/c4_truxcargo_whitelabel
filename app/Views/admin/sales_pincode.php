<?php

$yesNo = static function ($value): string {
    return in_array(strtoupper(trim((string) $value)), ['1', 'Y', 'YES', 'TRUE', 'ODA'], true) ? 'Yes' : 'No';
};
$serviceMap = $service_map ?? [];

echo view('admin/pincode/list', [
    'page_title' => $page_title ?? 'Sales Pincodes',
    'code' => $code ?? [],
    'count' => $count ?? 0,
    'links' => $links ?? '',
    'permission' => $permission ?? ($GLOBALS['permission'] ?? []),
    'selected_pincode' => $selected_pincode ?? '',
    'import_title' => 'Import Sales Pincode',
    'sample_title' => 'Sample Format (Sales Pincode)',
    'sample_headers' => ['PINCODE', 'STATE', 'ODA', 'SALE_ZONE', 'SERVICE_CODE', 'ODA_KM'],
    'sample_rows' => [
        ['110001', 'DELHI', 'ODA', 'N1', 'PAFEX_ROAD_TIME_CRITICAL', '170'],
        ['111115', 'HARYANA', 'REGULAR', 'N2', 'PAFEX_DOCUMENTS_TIME_CRITICAL', ''],
    ],
    'columns' => [
        ['label' => 'Pincode', 'value' => 'pincode'],
        ['label' => 'State', 'value' => 'state'],
        ['label' => 'ODA', 'value' => static fn ($row): string => $yesNo($row->oda ?? '')],
        ['label' => 'Service Code', 'value' => static fn ($row): string => $serviceMap[$row->service_id ?? 0] ?? ''],
    ],
    'search_placeholder' => 'Search Sales Pincode',
    'search_route' => 'admin/pincode/sales_pincode',
    'import_route' => 'admin/pincode/import_sales_pincode',
    'delete_route' => 'admin/pincode/delete_sales_pincode',
    'export_route' => 'admin/pincode/export_sales_pincode',
    'delete_confirm' => 'Do you really want to delete this Sales Pincode record ?',
]);
