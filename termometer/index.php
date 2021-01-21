<?php

class Mijia{
    const FILE_PATH = "./data/";
    const MAC_REGEX = "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/";

    private $mac;
    private $filepath;

    public function __construct()
    {
        if(isset($_GET['mac']) && preg_match(self::MAC_REGEX, $_GET['mac'])){
            $this->mac = $_GET['mac'];
            $this->filepath = self::FILE_PATH . $this->mac . ".json";
        }
    }

    public function handle()
    {
        if($this->mac){
            header('Content-Type: application/json');
            echo file_get_contents($this->filepath);
        }
    }
}

$request = new Mijia();
$request->handle();


