<?php
namespace Smarthome\EQ3;

require __DIR__ . '/../../vendor/autoload.php';

use Smarthome\EQ3\EQ3;

define("PUBLISH", "publish");
define("SUBSCRIBE","subscribe");

$eq3 = new EQ3();

switch ($argv[1]){
    case PUBLISH:
        $eq3->publish();
        break;
    case SUBSCRIBE:
        $eq3->subscribe();
        break;
}