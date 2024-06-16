<?php

define("ROUTES", [
    'order' => [
        'GET',
    ],
    'orderspending' => [
        'GET'
    ],
    'orderoutofstock' => [
        'GET'
    ],
    'ordershistory' => [
        'GET'
    ],
    'ordersreadytoShip' => [
        'GET', 'POST', 'PATCH', 'DELETE'
    ],
    'registershipment' => [
        'PATCH'
    ],
]);
