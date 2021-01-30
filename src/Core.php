<?php
namespace Smarthome;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class Core
{
    const BROKER = "volumio.local";
    const TERMOSTAT_SCRIPT = __DIR__ . "/EQ3/script.exp 00:1A:22:12:DF:0E ";    
    const TERMOMETER_SCRIPT = "sudo python ". __DIR__ . "/Mijia/mijia.py";

    private $mqtt;    
    private $queue = [];

    public function __construct()
    {
        $this->connect();
        $this->subscribe();                                 
    }        

    public function __invoke()
    {
        while (true)
        {            
            $this->mqtt->loop(true, true); 
            if(count($this->queue)){ 
                $this->execAction(array_shift($this->queue));             
            }else{
                $this->addToQueue('termostat_status');
                $this->addToQueue('termometer_status');  
            }           
        }
    }

    private function addToQueue($type, $command = null, $highPrio = false)
    {
        $action = ['type' => $type, 'command' => $command];
        if($highPrio){
            array_unshift($this->queue, $action);
        }else{
            $this->queue[] = $action;
        }
    }

    private function execAction($action)
    {               
        switch ($action['type']) 
        {
            case 'termostat_command':
                shell_exec(self::TERMOSTAT_SCRIPT . $command);
                break;
            case 'termostat_status':
                exec(self::TERMOSTAT_SCRIPT . "devjson", $status);
                $status = implode(' ', $status);       
                $this->mqtt->publish("erik/termostat/status", $status);
                break;
            case 'termometer_status':
                shell_exec(self::TERMOMETER_SCRIPT);
                break;
        }
    }

    private function connect()
    {
        $this->mqtt =  new MQTTClient(self::BROKER);
        try{
            $this->mqtt->connect();
        }catch(ConnectingToBrokerFailedException $e){            
            die("connection to " . self::BROKER ." failed\n");
        }       
    }

    private function subscribe()
    {
        $this->mqtt->subscribe("erik/termostat/set",function ($topic, $command)  {  
            $this->addToQueue('termostat_command', $command, true);                     
        }, 0);          
    }
}
