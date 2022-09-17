<?php

$dropshipper = [
    [
        'action' => 'manage',
        'subject' => 'all'
    ]
];
$warehouse = [
    [
        'action' => 'manage',
        'subject' => 'all'
    ]
];
$admin = [
    [
        'action' => 'manage',
        'subject' => 'all'
    ]
];
$superAdmin = array_merge([
    [
        'action' => 'manage',
        'subject' => 'all'
    ]
], $admin, $warehouse, $dropshipper);

return [
    'admin' => array_unique($admin),
    'super_admin' => array_unique($superAdmin),
    'dropshipper' => array_unique($dropshipper),
    'warehouse_officer' => array_unique($warehouse)
];
