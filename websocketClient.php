<?php
/**
 * User: ZXB
 * Date: 2017/2/10
 * Time: 16:03
 */

$cli = new Swoole\Coroutine\Http\Client('127.0.0.1', 9501);

$cli->setHeaders([
    'Host' => "localhost",
    "User-Agent" => 'Chrome/49.0.2587.3',
    'Accept' => 'text/html,application/xhtml+xml,application/xml',
    'Accept-Encoding' => 'gzip',
]);
$cli->set([ 'timeout' => 1]);
$cli->get('/index.php');
echo $cli->body;
$cli->close();