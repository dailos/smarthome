<?php
namespace Smarthome;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client();

if($argv[1] == 'subscribe'){
    $client->subcribe();
}else{
    $core = new Core($client);
    $core();
}

