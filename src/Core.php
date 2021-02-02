<?php
namespace Smarthome;

class Core
{  
    const TERMOSTAT_SCRIPT = __DIR__ . "/EQ3/script.exp 00:1A:22:12:DF:0E ";        
    
    private $mqttClient;    

    public function __construct(Client $client)
    {
        $this->mqttClient = $client;       
    }
   
    public function __invoke()
    {
        while (true){                         
            if(!file_exists(Client::ACTION_FILE)){
                sleep(1);
                return;
            }
            $actionsStr = file_get_contents(Client::ACTION_FILE);         
            unlink(Client::ACTION_FILE);             
            foreach(explode(',', $actionsStr) as $action){               
                if(!empty($action) && $action !== 'refresh'){
                    shell_exec(self::TERMOSTAT_SCRIPT . $action);                     
                }       
            }            
            $this->sendStatusUpdate();                                                                  
        }
    }

    private function sendStatusUpdate()
    {
        exec(self::TERMOSTAT_SCRIPT . "devjson", $status);
        $status = implode(' ', $status);                         
        return $this->mqttClient->publish("erik/termostat/status", $status); 
    }
}
