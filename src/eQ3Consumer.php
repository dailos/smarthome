<?php

namespace Smarthome;

require __DIR__.'/../vendor/autoload.php';

use PhpMqtt\Client\MQTTClient;

class eQ3Consumer
{
    private $mqtt;
    private $mac = "00:1A:22:12:DF:0E";

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(Config::BROKER);
        $this->mqtt->connect();
    }

    public function __invoke()
    {
        $mac = $this->mac;
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command) use ($mac)  {
            $exec = Config::TERMOSTAT_SCRIPT ." ". $mac ." ". $command ;
            shell_exec($exec);
        }, 0);
        $this->mqtt->loop(true);
        $this->mqtt->close();
    }
}

$consumer = new eQ3Consumer;
$consumer();