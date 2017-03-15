<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 17/2/9
 * Time: 下午1:39
 */
//use \Swoole\Atomic;

$client = new \Swoole\Client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1', 9501, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}

$data = [
    'serviceId' => crc32('get.userInfo'),
    'askId' => 1,
    'flag' => 1,
    'data' => ['uid' => 123]
];

$client->send(json_encode($data));
$result = $client->recv();
echo $result;
print_r(json_decode($result, true));
$client->close();