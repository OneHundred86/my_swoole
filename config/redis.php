<?php

return [
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'port' => env('REDIS_PORT', 6379),
    'auth' => env('REDIS_AUTH', ''),
    'db_index' => env('REDIS_DB_INDEX', 0),
    'time_out' => env('REDIS_TIMEOUT', 1),
    // 连接池的连接的数量
    'size' => 10,
];