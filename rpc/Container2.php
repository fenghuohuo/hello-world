<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 17/2/6
 * Time: 上午10:26
 */

class Container
{
    public $serv = null;

    //protected $table = null;

    protected $conns = [];

    protected $requests = [];

    public function __construct($host, $port)
    {
        $this->serv = new \Swoole\Server($host, $port, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->serv->on('start', function($serv) {
            echo "Service:Start...\n";
        });
        $this->serv->on('connect', function ($serv, $fd){
            echo "Client:Connect.\n";
        });
        $this->serv->on('receive', [$this, 'onReceive']);

        $this->serv->on('close', function ($serv, $fd) {
            echo "Client: Close.\n";
        });

        //$this->table = new \Swoole\Table(1024);
        //$this->table->column('fd', \Swoole\Table::TYPE_STRING, 512);
        //$this->table->create();
    }

    public function start()
    {
        $this->serv->set(array(
            'worker_num' => 1,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 50,
            'dispatch_mode'=>1,
        ));
        $this->serv->start();
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        //$this->conns[] = ['fd' => $fd];
        print_r($this->conns);


        $req = json_decode($data, true);
        $connInfo = $serv->connection_info($fd);
        //注册服务信息
        if (isset($req['command']) && $req['command'] == 'register') {
            //$this->table->set($req['serviceId'], ['fd' => $fd]);
            $this->conns[$req['serviceId']] = $fd;

            return;
        }

        if ($req['flag'] == 1) { //发起RPC调用
            //$conn = $this->table->get($req['serviceId']);
            $conn = $this->conns[$req['serviceId']];
            //$this->table->set($connInfo['remote_ip'].':'.$connInfo['remote_port'], ['fd' => $fd]);
            $this->requests[$connInfo['remote_ip'].':'.$connInfo['remote_port']] = $fd;
            $req['remote_ip'] = $connInfo['remote_ip'];
            $req['remote_port'] = $connInfo['remote_port'];
            $serv->send($conn, json_encode($req));
            print_r($this->requests);
        }

        if ($req['flag'] == 2) { //RPC返回结果
            print_r($this->requests);
            //$conn = $this->table->get($req['remote_ip'].':'.$req['remote_port']);
            $conn = $this->requests[$req['remote_ip'].':'.$req['remote_port']];
            //$this->table->del($req['remote_ip'].':'.$req['remote_port']);
            $serv->send($conn, json_encode($req));
            unset($this->requests[$connInfo['remote_ip'].':'.$connInfo['remote_port']]);
            unset($req['remote_ip']);
            unset($req['remote_port']);
        }
    }
}

$serv = new Container("127.0.0.1", 9501);

$serv->start();