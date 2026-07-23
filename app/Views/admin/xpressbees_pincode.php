<?php

$yesNo = static function ($value): string {
    return in_array(strtoupper(trim((string) $value)), ['1', 'Y', 'YES', 'TRUE', 'ODA'], true) ? 'Yes' : 'No';
};

echo view('admin/pincode/list', [
    'page_title' => $page_title ?? 'Xpressbees Pincodes',
    'code' => $code ?? [],
    'count' => $count ?? 0,
    'links' => $links ?? '',
    'permission' => $permission ?? ($GLOBALS['permission'] ?? []),
    'selected_pincode' => $selected_pincode ?? '',
    'import_title' => 'Import Xpressbees Pincode',
    'sample_title' => 'Sample Format (Xpressbees Pincode)',
    'sample_headers' => ['PINCODE', 'STATE', 'ODA', 'SALE_ZONE', 'ODA_KM'],
    'sample_rows' => [
        ['110001', 'DELHI', 'ODA', 'N1', ''],
        ['111115', 'HARYANA', 'REGULAR', 'N2', ''],
    ],
    'columns' => [
        ['label' => 'Pincode', 'value' => 'pincode'],
        ['label' => 'State', 'value' => 'state'],
        ['label' => 'ODA', 'value' => static fn ($row): string => $yesNo($row->oda ?? '')],
        ['label' => 'SALE ZONE', 'value' => 'sale_zone'],
    ],
    'search_placeholder' => 'Search Xpressbees Pincode',
    'search_route' => 'admin/pincode/xpressbees_pincode',
    'import_route' => 'admin/pincode/import_xpressbees_pincode',
    'delete_route' => 'admin/pincode/delete_xpressbees_pincode',
    'export_route' => 'admin/pincode/export_xpressbees_pincode',
    'delete_confirm' => 'Do you really want to delete this Xpressbees Pincode record ?',
]);
