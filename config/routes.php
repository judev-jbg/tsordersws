<?php

define("ROUTES", [
    'order' => [
        'GET'
    ],
    'ordersPending' => [
        'GET','POST'
    ],
    'orderOutOfStock' => [
        'GET',
    ],
    'ordersHistory' => [
        'GET',
    ],
    'ordersReadyToShip' => [
        'GET','POST','PATCH','DELETE'
    ],
    'registerShipment' => [
        'POST',
    ],
]);
