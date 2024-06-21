<?php

define("ROUTES", [
    'order' => [
        'GET',
    ],
    'orderspending' => [
        'GET'
    ],
    'ordersoutofstock' => [
        'GET'
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
