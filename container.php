<?php
/**
 * User: ZXB
 * Date: 2017/2/15
 * Time: 9:30
 */

$ws = new swoole_websocket_server("0.0.0.0", 9501);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $fd[] = $request->fd;
    $GLOBALS['fd'][] = $fd;
    $ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $msg =  'from'.$frame->fd.":{$frame->data}\n";
    $receive = [];

    echo $msg;

    if ($frame->data == "User.server") {
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        $client->connect("127.0.0.1", 9502);
        $client->send("take server: User.server");
        $receive[] = $client->on('receive', function ($fd, $data) {
            return $data;
        });
    }
    var_dump($receive);

//    foreach($GLOBALS['fd'] as $aa){
//        foreach($aa as $i){
//            echo "server: $msg";
//            $ws->push($i,$msg);
//        }
//    }
//     $ws->push($frame->fd, "server: {$frame->data}");
//     $ws->push($frame->fd, "server: {$frame->data}");
});

$ws->start();