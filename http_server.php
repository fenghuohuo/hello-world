<?php
/**
 * User: ZXB
 * Date: 2017/2/14
 * Time: 15:39
 */

$server = new swoole_http_server("0.0.0.0", 9503);

$server->on('request', function($request, $response) {
    print_r($request);
});

$server->start();