<?php

class job{
    const TYPE_TERMOSTAT = 'termostat';
    const TYPE_TERMOMETER = 'termometer';
    const TERMOSTAT_SCRIPT='./termostat/bin/writer.sh';
    const TERMOMETER_SCRIPT='./termometer/bin/writer.sh';
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
        foreach (self::DEVICES as $device){
            switch ($device['type']) {
                case self::TYPE_TERMOSTAT:
                    exec(self::TERMOSTAT_SCRIPT . " ". $device['mac']);
                    break;
                case self::TYPE_TERMOMETER:
                    exec(self::TERMOMETER_SCRIPT . " ". $device['mac']);
                    break;
            }
            $this->resetHci();
            sleep(2);
        }
    }

    private function resetHci()
    {
        exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
    }
}

$job = new job;
$job();
