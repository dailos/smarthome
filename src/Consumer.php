<?php

namespace Smarthome;

require __DIR__.'/../vendor/autoload.php';

use PhpMqtt\Client\MQTTClient;

class Consumer
{
    private $mqtt;

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(Config::BROKER);
        $this->mqtt->connect();
    }

    public function __invoke()
    {
        foreach (Config::getDevices() as $device){
            if($device['type'] === Config::TYPE_TERMOSTAT){
                $this->mqtt->subscribe($device['topic']."set",function ($topic, $command) use ($device)  {
                    echo Config::TERMOSTAT_SCRIPT ." ". $device['mac'] ." ". $command;
                    shell_exec(Config::TERMOSTAT_SCRIPT ." ". $device['mac'] ." ". $command);
                }, 0);
            }
        }

        $this->mqtt->loop(true);
        $this->mqtt->close();
    }
}

$consumer = new Consumer;
$consumer();