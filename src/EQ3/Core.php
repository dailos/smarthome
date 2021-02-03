<?php
namespace Smarthome\EQ3;

class Core
{  
    const SCRIPT = __DIR__ . "/script.exp 00:1A:22:12:DF:0E ";        
    
    private $mqttClient;    

    public function __construct(Client $client)
    {
        $this->mqttClient = $client;       
    }
   
    public function __invoke()
    {
        while (true){                         
            sleep(1); 
            if(file_exists(Client::ACTION_FILE)){                                           
                $actionsStr = file_get_contents(Client::ACTION_FILE);         
                unlink(Client::ACTION_FILE);             
                foreach(explode(',', $actionsStr) as $action){               
                    if(!empty($action) && $action !== 'refresh'){
                        shell_exec(self::SCRIPT . $action);                     
                    }       
                }            
                $this->sendStatusUpdate();         
            }                                                         
        }
    }

    private function sendStatusUpdate()
    {
        exec(self::SCRIPT . "devjson", $status);
        $status = implode(' ', $status);                         
        return $this->mqttClient->publish("erik/termostat/status", $status); 
    }
}
