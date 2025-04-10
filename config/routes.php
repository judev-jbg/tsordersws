<?php

define("ROUTES", [
    'order' => [
        'GET',
    ],
    'orderspending' => [
        'GET', 'PATCH'
    ],
    'ordersoutofstock' => [
        'GET', 'PATCH'
    ],
    'ordersshipfake' => [
        'GET',
    ],
    'ordershistory' => [
        'GET'
    ],
    'ordersreadytoship' => [
        'GET', 'POST', 'PATCH', 'DELETE'
    ],
    'registershipment' => [
        'PATCH'
    ],
]);
