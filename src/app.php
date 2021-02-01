<?php
namespace Smarthome;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client();

if(isset($argv[1]) && $argv[1] == 'subscribe'){
    $client->subscribe();
}else{
    $core = new Core($client);
    $core();
}

