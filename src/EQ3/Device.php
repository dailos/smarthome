<?php
namespace Smarthome\EQ3;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class Device
{
    const BROKER = "volumio.local";
    const SCRIPT = __DIR__ . "/script.exp 00:1A:22:12:DF:0E ";    

    private $mqtt;    

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(self::BROKER);
        try{
            $this->mqtt->connect();
        }catch(ConnectingToBrokerFailedException $e){            
            die("connection to " .self::BROKER ." failed\n");
        }
        
    }

    public function subscribe()
    {
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command)  {           
            shell_exec(self::SCRIPT . $command);
        }, 0);
        $this->mqtt->loop(true);     
    }

    public function publish()
    {
        exec(self::SCRIPT . "devjson", $status);
        $status = implode(' ', $status);       
        $this->mqtt->publish("erik/termostat/status", $status);
        $this->mqtt->close();
    }
}
