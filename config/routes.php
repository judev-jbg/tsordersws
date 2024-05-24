<?php

define("ROUTES", [
    'order' => [
        'GET'
    ],
    'ordersPending' => [
        'GET',
    ],
    'orderOutOfStock' => [
        'GET',
    ],
    'ordersHistory' => [
        'GET','POST'
    ],
    'ordersReadyToShip' => [
        'GET','POST','PATCH','DELETE'
    ],
    'registerShipment' => [
        'POST',
    ],
]);
