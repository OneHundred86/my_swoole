<?php
namespace App\Lib\Redis;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class Pool
{
    /*
    * @var RedisPool
    */
    static protected $pool;

    public static function getInstance()
    {
        if(!self::$pool){
            self::init();
        }

        return self::$pool;
    }

    protected static function init(){
        $cnf = config('redis');
        $config = new RedisConfig;
        $config->withHost($cnf['host'])
            ->withPort($cnf['port'])
            ->withAuth($cnf['auth'])
            ->withDbIndex($cnf['db_index'])
            ->withTimeout($cnf['time_out']);
        self::$pool = new RedisPool($config, $cnf['size']);
    }

    /*
    * @return \Redis
    */
    public static function getConn()
    {
        $pool = self::getInstance();
        return $pool->get();
    }

    /*
    * @param \Redis $conn
    */
    public static function releaseConn($conn)
    {
        $pool = self::getInstance();
        $pool->put($conn);
    }
}