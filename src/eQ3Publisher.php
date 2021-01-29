<?php
namespace Smarthome;

require __DIR__.'/../vendor/autoload.php';

use PhpMqtt\Client\MQTTClient;

class eQ3Publisher{

    private $mqtt;
    private $mac = "00:1A:22:12:DF:0E";

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(Config::BROKER);
        $this->mqtt->connect();
    }

    public function __invoke()
    {
        $this->refreshHICIfNeeded();
        exec(Config::TERMOSTAT_SCRIPT . " ". $this->mac . " devjson", $status);
        $status = implode(' ', $status);
        $this->mqtt->publish("erik/termostat/status", $status);
        $this->mqtt->close();
    }


    private function refreshHICIfNeeded(){
        if (in_array(date('i'),Config::REFRESH_HCI0_AT)) {
            exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
            sleep(2);
        }
    }
}

$publisher = new eQ3Publisher();
$publisher();
