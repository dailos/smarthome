<?php
namespace Smarthome;

require __DIR__.'/../vendor/autoload.php';

use Smarthome\Devices\Termostat;
use Smarthome\Devices\Types;

class Publisher{
    const REFRESH_HCI0_AT= ['10', '30', '50'];
    const BROKER = "volumio";

    private $devices;

    public function __construct()
    {
        $this->devices = json_decode(file_get_contents('devices.json'), true);
    }

    public function __invoke()
    {
        $this->refreshHICIfNeeded();
        foreach ($this->devices as $device){
            $status = null;
            switch ($device['type']) {
                case Types::TERMOSTAT:
                    exec(Termostat::SCRIPT . " ". $device['mac'] . " devjson", $status);
                    $status=implode(' ', $status);
                    break;
                case Types::TERMOMETER:
                    $status = $this->getTermometerValues($device['mac']);
                    break;
            }
            if($status){
                $topic = $this->getTopic($device);
                shell_exec("mosquitto_pub -h ".self::BROKER." -t $topic -m '" . $status ."'");
            }
        }
    }

    private function getTopic($device)
    {
        return $device['location'] ."/".$device['type']."/status/";
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
        if (in_array(date('i'),self::REFRESH_HCI0_AT)) {
            exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
            sleep(2);
        }
    }
}

$publisher = new Publisher();
$publisher();
