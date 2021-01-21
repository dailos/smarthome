<?php

$params = $_GET;
const REGEX =  "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/";
const FILE_PATH = './Data/';

if(isset($params['mac']) && $params['device'] && preg_match(REGEX, $params['mac'])){
    if($params['device'] == 'termostat'){
        $termostat = new Smarthome\Devices\Termostat($params);
        $termostat->handle();
    }
    header('Content-Type: application/json');
    echo file_get_contents(FILE_PATH . $params['mac'] . ".json");
}

