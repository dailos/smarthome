<?php
namespace Smarthome;

require __DIR__.'/../vendor/autoload.php';

use PhpMqtt\Client\MQTTClient;

class Publisher{

    private $mqtt;

    public function __construct()
    {
        $this->mqtt =  new MQTTClient(Config::BROKER);
        $this->mqtt->connect();
    }

    public function __invoke()
    {
        $this->refreshHICIfNeeded();
        foreach (Config::getDevices() as $device){
            $status = null;
            switch ($device['type']) {
                case Config::TERMOSTAT:
                    exec(Config::TERMOSTAT_SCRIPT . " ". $device['mac'] . " devjson", $status);
                    $status = implode(' ', $status);
                    break;
                case Config::TYPE_TERMOMETER:
                    $status = $this->getTermometerValues($device['mac']);
                    break;
            }
            if($status){
                $this->mqtt->publish($device['location'] .'/'. $device['type'] .'/' ."status", $status);
            }
        }
        $this->mqtt->close();
    }

    private function getTermometerValues($mac)
    {
        exec("timeout 10 gatttool -b $mac --char-write-req --handle='0x0038' --value=\"0100\" --listen | grep \"Notification handle\" -m 1", $response);
        if(isset($response[0]) && strpos($response[0], '0x0036') !== false) {
            $result = explode(' ', $response[0]);
            return json_encode([
                'temperature' => hexdec($result[6] . $result[5]) / 100,
                'humidity' => hexdec($result[7]),
                'battery' => round (100 * ((hexdec($result[9] . $result[8]) / 1000) - 2.1 ))
            ]);
        }
        return null;
    }

    private function refreshHICIfNeeded(){
        if (in_array(date('i'),Config::REFRESH_HCI0_AT)) {
            exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
            sleep(2);
        }
    }
}

$publisher = new Publisher();
$publisher();
