<?php
namespace Smarthome\EQ3;

class Core
{  
    const SCRIPT = __DIR__ . "/script.exp 00:1A:22:12:DF:0E ";     
    const RETRIES = 5;   
    
    private $mqttClient;    

    public function __construct(Client $client)
    {
        $this->mqttClient = $client;       
    }
   
    public function __invoke()
    {
        while (true){                                      
            if(file_exists(Client::ACTION_FILE)){
                $actionsStr = file_get_contents(Client::ACTION_FILE);                                                  
                foreach(explode(',', $actionsStr) as $action){                                   
                    if($action === 'refresh'){
                        $this->sendStatusUpdate(); 
                    }else{
                        $this->setStatus($action);
                    }                                               
                }                                    
                unlink(Client::ACTION_FILE);                                             
            }                                   
            sleep(1);
        }
    }

    private function sendStatusUpdate()
    {
        exec(self::SCRIPT . "devjson", $status);
        $status = implode(' ', $status);                         
        return $this->mqttClient->publish("erik/termostat/status", $status); 
    }

    private function setStatus($action)
    {
        if(!empty($action)){
            for($i = 0; $i <= self::RETRIES; $i++){
                exec(self::SCRIPT . $action, $status);      	    
                foreach ($status as $line){
		            $words = explode(":", $line);
                    if($words[0] === 'Temperature' && isset($words[1])){
                        $result = '{"temperature" : '. substr(trim($words[1]), 0, -3) . ' }';
                        return $this->mqttClient->publish("erik/termostat/status", $result); 
                    }                    
                }
            }                           
        } 
    }
}
