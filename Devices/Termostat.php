<?php

namespace Smarthome\Devices;

class Termostat
{
    const SCRIPT = "./Scripts/eq3.exp";
    const BOOST_MAPPING = ["ON" => "boost", "OFF" => "boost off"];
    const LOCK_MAPPING = ["ON" => "lock", "OFF" => "unlock"];

    private $mac;
    private $params;
    private $command;

    public function __construct($params)
    {
        $this->params = $params;
        $this->mac = $this->params['mac'];
        $this->command = $this->getCommand();
    }

    public function handle()
    {
        if($this->command){
            shell_exec(self::SCRIPT ." ". $this->mac ." ". $this->command);
        }
    }

    private function getCommand()
    {
        if (isset($this->params['temperature'])){
            return " temp " . floatval(str_replace(",", ".", $this->params['temperature']));
        }
        if (isset($this->params['mode'])) {
            return $this->params['mode'];
        }
        if (isset($this->params['boost'])){
            return self::BOOST_MAPPING[$this->params['boost']];
        }
        if (isset($this->params['lock'])){
            return self::LOCK_MAPPING[$this->params['lock']];
        }
    }
}