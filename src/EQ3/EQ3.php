<?php
namespace Smarthome\EQ3;

use PhpMqtt\Client\MQTTClient;

class EQ3
{
    const BROKER = "volumio.local";
    const SCRIPT = __DIR__ . "./script.exp 00:1A:22:12:DF:0E ";    

    private $mqtt;    

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(self::BROKER);
        $this->mqtt->connect();
    }

    public function subscribe()
    {
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command)  {
            echo $command;
            shell_exec(self::SCRIPT . $command);
        }, 0);
        $this->mqtt->loop(true);
        $this->mqtt->close();
    }

    public function publish()
    {
        shell_exec(self::SCRIPT . "devjson", $status);
        $status = implode(' ', $status);
        echo $status;
        $this->mqtt->publish("erik/termostat/status", $status);
        $this->mqtt->close();
    }
}
