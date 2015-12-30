<?php
include_once dirname(__FILE__).'/vendor/autoload.php';
$listener = new SF\Listener\Http('zf.dev');
$server = new SF\Server\Server($listener);
$server->run();
