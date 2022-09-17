<?php

return [
    'admin' => [
        'admin',
        'basic',
        'customer.create',
        'product.create'
    ],
    'dropshipper' => [
        'basic',
        'cart',
        'cart.add_product',
        'cart.empty_quantity',
        'cart.remove',
        'customer.create',
        'dropshipper',
    ],
    'super_admin' => [
        'admin',
        'basic',
        'product.create',
        'super_admin',
    ],
    'warehouse_officer' => [
        'basic',
        'product.create',
        'warehouse_officer',
    ],
];
