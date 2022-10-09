<?php

return [
    'admin' => [
        'admin',
        'basic',
        'customer.create',
        'product.create',
        'store.read'
    ],
    'cashier' => [
        'basic',
        'cashier',
        'cart',
        'cart.add_product',
        'cart.empty_quantity',
        'cart.remove',
    ],
    'dropshipper' => [
        'basic',
        'cart',
        'cart.add_product',
        'cart.empty_quantity',
        'cart.remove',
        'customer.create',
        'dropshipper',
        'store.read'
    ],
    'super_admin' => [
        'admin',
        'basic',
        'product.create',
        'super_admin',
        'store.read'
    ],
    'warehouse_officer' => [
        'basic',
        'product.create',
        'warehouse_officer',
    ],
];
