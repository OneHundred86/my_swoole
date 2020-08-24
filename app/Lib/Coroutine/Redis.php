<?php
namespace App\Lib\Coroutine;
use Swoole\Coroutine;
use App\Lib\Redis\Pool;

// singleton in the same coroutine
class Redis extends Facade
{
    /**
     * get singleton object in the same coroutine
     * @return Object
     */
    protected static function getObject()
    {
        $conn = Context::get('_redis_conn');
        if(!$conn){
            $conn = Pool::getConn();
            Context::set('_redis_conn', $conn);

            # 归还连接
            Coroutine::defer(function() use($conn) {
                Pool::releaseConn($conn);
            });
        }

        return $conn;
    }
}