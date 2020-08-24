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

            $users = \App\Lib\Coroutine\DB::select("SELECT * FROM `user`");

            $data = [
                'users' => $users,
                'foo' => \App\Lib\Coroutine\Redis::get("foo"),
                'foo1' => \App\Lib\Coroutine\Redis::get("foo1"),
            ];

            $response->header('Content-Type', 'application/json');
            $response->end(json_encode($data));
        }catch(\Throwable $e){
            var_dump([
                'error' => $e->getMessage(), 
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'back_trace' => $e->getTraceAsString(),
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