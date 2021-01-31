<?php
namespace Smarthome;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class Mosquitto
{
    const BROKER = "volumio.local";
    const COMMAND_FILE = __DIR__ . "/commands.txt";
    private $mqtt;

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(self::BROKER);
        try{
            $this->mqtt->connect();
        }catch(ConnectingToBrokerFailedException $e){            
            die("connection to " . self::BROKER ." failed\n");
        }                                            
    }        

    public function subscribe()
    {
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command)  {  
            file_put_contents(self::COMMAND_FILE, $command . ",", FILE_APPEND);                                         
        }, 0);          
        $this->mqtt->loop(true); 
        $this->mqtt->close();
    }

    public function getMqtt()
    {
        return $this->mqtt;
    }

}