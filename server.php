<?php
include_once dirname(__FILE__).'/vendor/autoload.php';
$listener = new LZ\Listener\Http('lumza.dev:82');
$threadManager = new LZ\Thread\Manager(2);
$server = new LZ\Server\Server($listener, $threadManager);
$server->run();
