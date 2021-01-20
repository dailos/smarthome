<?php

class job{
    const TYPE_TERMOSTAT = 'termostat';
    const TYPE_TERMOMETER = 'termometer';
    const TERMOSTAT_SCRIPT='./termostat/bin/writer.sh';
    const TERMOMETER_SCRIPT='./termostat/bin/writer.sh';
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
            'mac' => 'A4:C1:38:44:E9:EB',
            'type' => self::TYPE_TERMOMETER,
            'location' => 'livingroom'
        ],
    ];

    public function __invoke()
    {
        foreach (self::DEVICES as $device){
            switch ($device['type']) {
                case self::TYPE_TERMOSTAT:
                    shell_exec(self::TERMOSTAT_SCRIPT . " ". $device['mac']);
                    break;
                case self::TYPE_TERMOMETER:
                    shell_exec(self::TERMOMETER_SCRIPT . " ". $device['mac']);
                    break;
            }
        }
        $this->resetHci();
    }

    private function resetHci()
    {
        shell_exec('sudo hciconfig hci0 down && sudo hciconfig hci0 up');
    }
}

$job = new job;
$job();