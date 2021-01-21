<?php

class eq3{
    const SCRIPT = "./bin/eq3.exp";
    const MAC_REGEX = "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/";
    const BOOST_MAPPING = ["ON" => "boost", "OFF" => "boost off"];
    const LOCK_MAPPING = ["ON" => "lock", "OFF" => "unlock"];
    const FILE_PATH = "./data/";

    private $mac;
    private $params;
    private $command;
    private $filepath;

    public function __construct()
    {
        $this->params = $_GET;
        if(isset($this->params['mac']) && preg_match(self::MAC_REGEX, $this->params['mac'])){
            $this->mac = $this->params['mac'];
            $this->filepath = self::FILE_PATH . $this->mac . ".json";
            $this->command = $this->getCommand();
        }
    }

    public function handle()
    {
        if($this->command){
            shell_exec(self::SCRIPT ." ". $this->mac ." ". $this->command);
        }
        header('Content-Type: application/json');
        echo file_get_contents($this->filepath);
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

$request = new eq3();
$request->handle();


