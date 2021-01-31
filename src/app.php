<?php
namespace Smarthome;

require __DIR__ . '/../vendor/autoload.php';

$mosquitto = new Mosquitto();

if(arg[1] == 'subscribe'){
    $mosquitto->subcribe();
}else{
    $core = new Core($mosquitto);
    $core();
}

