<?php
/**
 * User: ZXB
 * Date: 2017/2/8
 * Time: 14:17
 */

$server = new swoole_server('0.0.0.0', 9501);

$server->on('connect', function ($server, $fd, $from_id){
    echo "connect.\n";
});

$server->on('close', function ($server, $fd, $from_id){
    echo "close\n";
});

$server->on('receive', function ($server, $fd, $from_id, $data){

    echo "client $fd data: $data\n";
    $server->send($fd, "server:$data");
});

$server->start();