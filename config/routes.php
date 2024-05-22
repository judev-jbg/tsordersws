<?php

define("ROUTES", [
    '/' => [
        'GET' => 'homeHandler',
    ],
    '/users' => [
        'GET' => 'usersHandler',
    ],
    '/contact' => [
        'GET' => 'contactHandler',
    ],
]);
