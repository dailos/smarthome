<?php


namespace Smarthome;


use PhpMqtt\Client\MqttClient;

class Consumer
{

    const SERVER= 'volumio';
    const CLIENT= 'raspi';
    const PORT = 1883;
    const TOPIC = 'erik/termostat/set/';
    private $mqtt;

    public function __construct()
    {
        $this->mqtt = new MqttClient(self::SERVER, self::PORT, self::CLIENT);
    }

    public function __invoke()
    {
        $this->mqtt->connect();
        $this->mqtt->subscribe(self::TOPIC, function ($topic, $message) {
            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
        }, 0);
        $this->mqtt->loop(true);
        $this->mqtt->disconnect();
    }
}

$consumer = new Consumer;
$consumer();