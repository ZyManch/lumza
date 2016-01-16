<?php
include_once dirname(__FILE__).'/vendor/autoload.php';

$requestProvider = new LZ\Request\Http\Provider('lumza.dev:82');

$threadManager = new LZ\Thread\Manager(2);

print "Server started\n";
$server = new LZ\Engine\Server($requestProvider, $threadManager);
$server->loop();
