<?php
namespace Smarthome;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client();

if(isset($argv[1]) && $argv == 'subscribe'){
    $client->subcribe();
}else{
    $core = new Core($client);
    $core();
}

