<?php
include "test.php";
use Swoole\Coroutine;
use App\Lib\Coroutine\DB;

Coroutine\run(function(){
    $arr = DB::select("SELECT * FROM `user`");
    var_dump($arr);

    $arr = DB::select("SELECT * FROM `user` WHERE `id` = :id", [':id' => 1]);
    var_dump($arr);

    // $res = DB::execute("INSERT INTO `user` set `xh` = :xh", [':xh' => 'xh004']);
    // var_dump($res);

    DB::transaction(function () {
        DB::execute("INSERT INTO `user` set `xh` = :xh, `name` = :name", [':xh' => 'xh005', ':name' => '事务']);
        # 故意报错，测试事务回滚
        DB::execute("UPDATE `user` set `name` = :name WHERE `xh1` = :xh", [':xh' => 'xh005', ':name' => '事务1']);
    });
});
