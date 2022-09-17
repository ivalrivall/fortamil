<?php

use Illuminate\Support\Arr;

$dropshipper = [
    [
        'action' => 'read',
        'subject' => 'Auth'
    ],
    [
        'action' => 'read',
        'subject' => 'ACL'
    ],
];
$warehouse = [
    [
        'action' => 'read',
        'subject' => 'Auth'
    ],
    [
        'action' => 'read',
        'subject' => 'ACL'
    ],
];
$admin = array_merge_recursive(
    [
        [
            'action' => 'manage',
            'subject' => 'all'
        ]
    ], $warehouse, $dropshipper);
$superAdmin = $admin;

return [
    'admin' => array_unique($admin, SORT_REGULAR),
    'super_admin' => array_unique($superAdmin, SORT_REGULAR),
    'dropshipper' => array_unique($dropshipper, SORT_REGULAR),
    'warehouse_officer' => array_unique($warehouse, SORT_REGULAR)
];
