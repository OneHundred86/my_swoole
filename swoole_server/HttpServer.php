<?php
namespace SwooleServer;
use App\Lib\DB\Pool as DBPool;
use App\Lib\Redis\Pool as RedisPool;

class HttpServer
{
    protected $server;

    public function __construct()
    {
        $cnf = config('server.http');
        echo sprintf("Http Server running: http://%s:%s", $cnf['host'], $cnf['port']) . PHP_EOL;
        $this->server = new \Swoole\Http\Server($cnf['host'], $cnf['port']);
        # 设置
        $this->server->set(config('server.swoole_setting'));

        # 回调函数
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerStop', [$this, 'onWorkerStop']);

        $this->server->start();
    }

    public function onRequest($request, $response)
    {
        try {
            // var_dump($request->server, $request->header, $request->cookie, $request->get, $request->post);

            # 获取数据库连接
            $pdo = DBPool::getConn();
            # 归还数据库连接
            \Swoole\Coroutine::defer(function() use($pdo) {
                DBPool::releaseConn($pdo);
            });

            # 获取redis连接
            $redis = RedisPool::getConn();
            # 归还redis连接
            \Swoole\Coroutine::defer(function() use($redis){
                RedisPool::releaseConn($redis);
            });


            $statement = $pdo->prepare('SELECT * From user');
            $result = $statement->execute();
            if(!$result){
                throw new RuntimeException('Execute failed');
            }
            $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

            // \Swoole\Coroutine::sleep(1);

            $data = [
                'users' => $users,
                'foo' => $redis->get("foo"),
            ];

            $response->header('Content-Type', 'application/json');
            $response->end(json_encode($data));
        }catch(\Throwable $e){
            var_dump([
                'error' => $e->getMessage(), 
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $response->status(500);
            $response->end("Server Error");
        }
    }

    public function onWorkerStart(\Swoole\Server $server, int $worker_id)
    {
        echo sprintf("worker start: %s", $worker_id) . PHP_EOL;
    }

    public function onWorkerStop(\Swoole\Server $server, int $worker_id)
    {
        echo sprintf("worker stop: %s", $worker_id) . PHP_EOL;
    }
}