<?php

class Mijia{
    const FILE_PATH = "./data/termometer_";
    const MAC_REGEX = "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/";

    private $mac;
    private $filepath;

    public function __construct()
    {
        if(isset($$_GET['mac']) && preg_match(self::MAC_REGEX, $_GET['mac'])){
            $this->mac = $_GET['mac'];
            $this->filepath = self::FILE_PATH . $this->mac . ".data";
        }
    }

    public function handle()
    {
        if($this->mac){
            header('Content-Type: application/json');
            $response = $this->getResponse();
            echo $response;
        }
    }

    private function getResponse()
    {
        $fileContent = file_get_contents($this->filepath);
        $values = explode(" ", $fileContent);
        return [
            "temperature" => $values[0],
            "humidity" => $values[1],
            "battery" => $values[2],
        ];
    }
}

$request = new Mijia();
$request->handle();


