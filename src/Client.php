<?php
namespace Smarthome;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class Client
{
    const SERVER = "volumio.local";
    const COMMAND_FILE = __DIR__ . "/commands.txt";
    private $mqtt;

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(self::SERVER);            
    }        

    public function subscribe()
    {
        $this->connect();
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command)  {  
            file_put_contents(self::COMMAND_FILE, $command . ",", FILE_APPEND);                                         
        }, 0);        
        $this->mqtt->subscribe("erik/termostat/request",function ($topic, $command)  {  
            file_put_contents(self::COMMAND_FILE, ",", FILE_APPEND);                                         
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