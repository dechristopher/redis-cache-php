<?php

return [
    'cache' => [
        'driver' => 'redis',
        'connections' => [
            'redis' => [
                'host' => '10.14.31.26',
                'port' => 6379,
                'password' => null,
            ]
        ]
    ]
];
