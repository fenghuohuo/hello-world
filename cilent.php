<?php
/**
 * User: ZXB
 * Date: 2017/2/8
 * Time: 14:58
 */

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

$client->on('connect', function($client){
    echo "connect!\n";

    $client->send("client is connect");
});

$client->on('receive', function($client, $data){
    echo "server data:$data!\n";

    sleep(1);

    $client->send("client data:$data\n");
});

$client->on('close', function($client){
    echo "connect!.\n";
});

$client->on('error', function($client){
    echo "error!.\n";
});

$client->connect('127.0.0.1', 9501);