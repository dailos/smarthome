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
        while (true)
        {                         
            $this->readCommands();                
            if(count($this->actionQueue)){ 
                $this->execAction(array_shift($this->actionQueue));             
            }else{                                          
                $this->addToQueue('termometer_status');  
            }           
        }
    }

    private function addToQueue($type, $command = null, $highPrio = false)
    {
        $action = ['type' => $type, 'command' => $command];
        if($highPrio){            
            array_unshift($this->actionQueue, $action);
        }else{
            $this->actionQueue[] = $action;
        }        
    }

    private function execAction($action)
    {                              
        switch ($action['type']) 
        {
            case 'termostat_command':
                shell_exec(self::TERMOSTAT_SCRIPT . $action['command']);
                break;
            case 'termostat_status':
                exec(self::TERMOSTAT_SCRIPT . "devjson", $status);
                $status = implode(' ', $status);                         
                $this->mqttClient->publish("erik/termostat/status", $status);                
                break;
            case 'termometer_status':
                shell_exec(self::TERMOMETER_SCRIPT);
                break;
        }
    }

    private function readCommands()
    {        
        if(file_exists(Client::COMMAND_FILE)){
            $commands = file_get_contents(Client::COMMAND_FILE);         
            unlink(Client::COMMAND_FILE);            
                foreach( explode(',', $commands) as $command){               
                    if(!empty($command)){
                        $this->addToQueue('termostat_command', $command, true);                    
                     }       
                }                       
            $this->addToQueue('termostat_status');  
        }
    }    
}
