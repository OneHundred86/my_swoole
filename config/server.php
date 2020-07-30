<?php

return [
    'http' => [
        'host' => env('HTTP_HOST', '127.0.0.1'),
        'port' => env('HTTP_PORT', 9501),
    ],

    'swoole_setting' => [
        'worker_num' => swoole_cpu_num(),
    ],
];