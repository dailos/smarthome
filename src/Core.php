<?php
namespace Smarthome;

class Core
{  
    const TERMOSTAT_SCRIPT = __DIR__ . "/EQ3/script.exp 00:1A:22:12:DF:0E ";    
    const TERMOMETER_SCRIPT = "sudo python ". __DIR__ . "/Mijia/mijia.py";  
    
    private $mqttClient;    
    private $actionQueue = [];

    public function __construct(Client $client)
    {
        $this->mqttClient = $client;       
    }
   
    public function __invoke()
    {
        while (true){                         
            $this->readActions();                            
            $this->execAction();                          
        }
    }

    private function addToQueue($action = null, $highPrio = false)
    {       
        if($highPrio){            
            array_unshift($this->actionQueue, $action);
        }else{
            $this->actionQueue[] = $action;
        }        
    }

    private function execAction()
    {                           
        $action = array_shift($this->actionQueue);   
        if(!$action){
            return shell_exec(self::TERMOMETER_SCRIPT);            
        }
              
        if ($action === 'refresh'){
            exec(self::TERMOSTAT_SCRIPT . "devjson", $status);
            $status = implode(' ', $status);                         
            return $this->mqttClient->publish("erik/termostat/status", $status); 
        }

        return shell_exec(self::TERMOSTAT_SCRIPT . $action);        
    }

    private function readActions()
    {        
        if(file_exists(Client::ACTION_FILE)){
            $actionsStr = file_get_contents(Client::ACTION_FILE);         
            unlink(Client::ACTION_FILE);    
            $actions = explode(',', $actionsStr);           
            foreach(array_reverse($actions) as $action){               
                if(!empty($action)){
                    $this->addToQueue($action, true);                    
                }       
            }            
            if(!in_array('refresh', $actions)){
                $this->addToQueue('refresh');   
            }               
            
        }
    }    
}
