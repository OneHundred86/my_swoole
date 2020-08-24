<?php
namespace App\Lib\Coroutine;
use Swoole\Coroutine;
use App\Lib\DB\Pool;
use App\Lib\DB\Database;

// singleton in the same coroutine
class DB extends Facade
{
    /**
     * get singleton object in the same coroutine
     * @return Object
     */
    public static function getObject()
    {
        $db = Context::get('_db');
        if(!$db){
            $conn = Pool::getConn();
            $db = new Database($conn);
            Context::set('_db', $db);

            # 归还连接
            Coroutine::defer(function() use($conn) {
                Pool::releaseConn($conn);
            });
        }

        return $db;
    }
}