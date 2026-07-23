<?php

echo view('admin/pincode/list', [
    'page_title' => $page_title ?? 'Delhivery Pincodes',
    'code' => $code ?? [],
    'count' => $count ?? 0,
    'links' => $links ?? '',
    'permission' => $permission ?? ($GLOBALS['permission'] ?? []),
    'selected_pincode' => $selected_pincode ?? '',
    'import_title' => 'Import Delhivery Pincode',
    'sample_title' => 'Sample Format',
    'sample_headers' => ['PINCODE', 'ORIGIN', 'DELIVERY', 'CITY', 'CODE', 'STATE', 'GREEN', 'ZONE'],
    'sample_rows' => [
        ['306106', 'FALSE', 'FALSE', 'Pali', '8', 'RAJASTHAN', 'FALSE', 'N3'],
        ['783337', 'FALSE', 'FALSE', 'Kokrajhar', '18', 'ASSAM', 'FALSE', 'NE2'],
    ],
    'columns' => [
        ['label' => 'Pincode', 'value' => 'pincode'],
        ['label' => 'Origin', 'value' => 'origin'],
        ['label' => 'Delivery', 'value' => 'delivery'],
        ['label' => 'City', 'value' => 'city'],
        ['label' => 'Code', 'value' => 'code'],
        ['label' => 'State', 'value' => 'state'],
        ['label' => 'Green', 'value' => 'green'],
        ['label' => 'Zone', 'value' => 'zone'],
    ],
    'search_placeholder' => 'Search By Pincode',
    'search_route' => 'admin/pincode/delhivery_pincode',
    'import_route' => 'admin/pincode/import_delhivery_pincode',
    'delete_route' => 'admin/pincode/delete_delhivery_pincode',
    'export_route' => 'admin/pincode/export_delhivery_pincode',
]);
