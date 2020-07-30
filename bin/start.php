#!/usr/bin/env php
<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('CONFIG_PATH') && define('CONFIG_PATH', BASE_PATH . '/config/');

require BASE_PATH.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

# 一键协程化
\Swoole\Runtime::enableCoroutine($flags = SWOOLE_HOOK_ALL);

new \SwooleServer\HttpServer;