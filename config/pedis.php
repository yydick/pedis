<?php

return [
    'NETWORK' => [
        'host' => env('pedis.NETWORK.host'),
        'port' => env('pedis.NETWORK.port,'),
        ''
    ],
    'GENERAL' => [
        'daemonize' => env('pedis.GENERAL.daemonize'),
        'pidfile' => env('pedis.GENERAL.pidfile'),
    ],
    'SNAPSHOTTING' => [],
    'CLIENTS' => [],
    'MEMORY_MANAGEMENT' => []
];
