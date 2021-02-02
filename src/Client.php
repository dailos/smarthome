<?php
namespace Smarthome;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class Client
{
    const SERVER = "volumio.local";
    const ACTION_FILE = __DIR__ . "/actions.csv";
    private $mqtt;

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(self::SERVER);            
    }        

    public function subscribe()
    {
        $this->connect();
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $action)  {  
            file_put_contents(self::ACTION_FILE, $action . ",", FILE_APPEND);                  
        }, 0);                 
        $this->mqtt->loop(true); 
        $this->mqtt->close();
    }

    public function publish($topic, $message)
    {                  
        $this->connect();    
        $this->mqtt->publish($topic, $message);
        $this->mqtt->close();
    }    

    private function connect()
    {
        try{
            $this->mqtt->connect();
        }catch(ConnectingToBrokerFailedException $e){            
            die("connection to " . self::SERVER ." failed\n");
        }            
    }
}