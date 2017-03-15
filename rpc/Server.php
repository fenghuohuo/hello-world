<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 17/2/6
 * Time: 上午10:37
 */
use \Swoole\Client as SClient;

class Server
{
    public $client;
    protected $serviceId = 0;

    public function __construct()
    {
        $this->client = new SClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $this->serviceId = crc32('get.userInfo');
        $this->client->on("connect", function(SClient $cli) {
            $data = ['command' => 'register', 'serviceId' => $this->serviceId];
            $cli->send(json_encode($data));
        });

        $this->client->on("receive", [$this, 'onReceive']);

        $this->client->on("error", function(SClient $cli){
            echo "error\n";
        });

        $this->client->on("close", function(SClient $cli){
            echo "Connection close\n";
        });
    }

    public function connect()
    {
        $this->client->connect('127.0.0.1', 9501);
    }

    public function onReceive(SClient $cli, $data)
    {
        $req = json_decode($data, true);
        if ($req['serviceId'] == $this->serviceId) {
            $rsp = $req;
            $rsp['flag'] = 2;
            $rsp['data'] =$this->getUserInfo($req['data']['uid']);
            $cli->send(json_encode($rsp));
        }
    }

    protected function getUserInfo($uid)
    {
        return ['uid' => $uid, 'nickname' => 'longmsdu', 'avatar' => 'a:default'];
    }
}

$client = new Server();

$client->connect();