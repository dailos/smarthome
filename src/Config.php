<?php

namespace Smarthome;

class Config
{
    const TYPE_TERMOSTAT = 'termostat';
    const TYPE_TERMOMETER = 'termometer';
    const BROKER= 'volumio.local';
    const TERMOSTAT_SCRIPT = __DIR__."/../bin/eq3.exp";
    const REFRESH_HCI0_AT= ['10', '30', '50'];

    static public function getDevices()
    {
        return json_decode(file_get_contents('devices.json'), true);
    }
}