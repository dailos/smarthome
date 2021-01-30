<?php
namespace Smarthome;

use PhpMqtt\Client\MQTTClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

class App
{
    const BROKER = "volumio.local";
    const TERMOSTAT_SCRIPT = __DIR__ . "/EQ3/script.exp 00:1A:22:12:DF:0E ";    
    const TERMOMETER_SCRIPT = "sudo python ". __DIR__ . "/Mijia/mijia.py";

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
            shell_exec(self::TERMOSTAT_SCRIPT . $command);
        }, 0);
        $this->mqtt->loop(true);     
    }

    public function publish()
    {              
        exec(self::TERMOSTAT_SCRIPT . "devjson", $status);
        $status = implode(' ', $status);       
        $this->mqtt->publish("erik/termostat/status", $status);
        $this->mqtt->close();
        shell_exec(self::TERMOMETER_SCRIPT);        
    }
}


require __DIR__ . '/../../vendor/autoload.php';


define("PUBLISH", "publish");
define("SUBSCRIBE","subscribe");

$app = new App();

switch ($argv[1]){
    case PUBLISH:
        $app->publish();
        break;
    case SUBSCRIBE:
        $app->subscribe();
        break;
}