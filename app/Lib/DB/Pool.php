<?php
namespace App\Lib\DB;

use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;

class Pool
{
    /*
    * @var PDOPool
    */
    static protected $pool;

    public static function getInstance()
    {
        if(!self::$pool){
            self::init();
        }

        return self::$pool;
    }

    protected static function init()
    {
        $cnf = config('database');
        $config = new PDOConfig;
        $config->withHost($cnf['host'])
            ->withPort($cnf['port'])
            ->withDbName($cnf['database'])
            ->withCharset($cnf['charset'])
            ->withUsername($cnf['username'])
            ->withPassword($cnf['password']);
        self::$pool = new PDOPool($config, $cnf['size']);
    }

    /*
    * @return \PDO | \Swoole\Database\PDOProxy
    */
    public static function getConn()
    {
        $pool = self::getInstance();
        return $pool->get();
    }

    /*
    * @param  \PDO | \Swoole\Database\PDOProxy $conn
    */
    public static function releaseConn($conn)
    {
        $pool = self::getInstance();
        $pool->put($conn);
    }
}