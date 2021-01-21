<?php
namespace Smarthome;

class Job{
    const TYPE_TERMOSTAT = 'termostat';
    const TYPE_TERMOMETER = 'termometer';
    const TERMOSTAT_SCRIPT='./Scripts/eq3.exp';
    const FILE_PATH = './Data/';
    const REFRESH_HCI0_AT= ['10', '30', '50'];
    const DEVICES = [
        [
            'mac' => '00:1A:22:12:DF:0E',
            'type' => self::TYPE_TERMOSTAT,
            'label' => 'Erik',
        ],
        [
            'mac' => 'A4:C1:38:44:E9:EB',
            'type' => self::TYPE_TERMOMETER,
            'label' => 'Erik'
        ],
        [
            'mac' => 'A4:C1:38:C7:07:6F',
            'type' => self::TYPE_TERMOMETER,
            'location' => 'livingroom'
        ],
        [
            'mac' => 'A4:C1:38:BC:6B:C8',
            'type' => self::TYPE_TERMOMETER,
            'location' => 'office'
        ],
    ];

    public function __invoke()
    {
        if (in_array(date('i'),self::REFRESH_HCI0_AT)) {
            exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
            sleep(2);
        }
        foreach (self::DEVICES as $device){
            $status = null;
            switch ($device['type']) {
                case self::TYPE_TERMOSTAT:
                    exec(self::TERMOSTAT_SCRIPT . " ". $device['mac'] . " devjson", $status);
                    break;
                case self::TYPE_TERMOMETER:
                    $status = $this->getTermometerValues($device['mac']);
                    break;
            }
            if($status){
                file_put_contents(self::FILE_PATH . $device['mac'] .'.json', $status);
            }
        }
    }

    private function getTermometerValues($mac)
    {
        exec("timeout 10 gatttool -b $mac --char-write-req --handle='0x0038' --value=\"0100\" --listen | grep \"Notification handle\" -m 1", $response);
        $values = explode(':', $response);
        $result = explode(' ', $values[1]);
        return json_encode([
            'temperature' => hexdec($result[1].$result[0])/100,
            'humidity' => hexdec($result[2]),
            'battery' => hexdec($result[4].$result[3])/1000
        ]);
    }
}

$job = new Job;
$job();
