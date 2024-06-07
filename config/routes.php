<?php

define("ROUTES", [
    'order' => [
        'GET'
    ],
    'orderspending' => [
        'GET',
    ],
    'orderoutofstock' => [
        'GET',
    ],
    'ordershistory' => [
        'GET', 'POST'
    ],
    'ordersreadytoShip' => [
        'GET', 'POST', 'PATCH', 'DELETE'
    ],
    'registershipment' => [
        'POST',
    ],
]);
