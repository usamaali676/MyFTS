<?php
require 'vendor/autoload.php';

use MehediJaman\LaravelZkteco\LaravelZkteco;

// Try passing password in constructor
$zk = new LaravelZkteco('192.168.99.18', 4370, 123456);

$connected = $zk->connect();
echo "Connected: " . var_export($connected, true) . "\n";

if ($connected) {
    $zk->disableDevice();
    $att   = $zk->getAttendance();
    $users = $zk->getUser();
    $zk->enableDevice();
    $zk->disconnect();

    echo "Users count: "      . count($users ?? []) . "\n";
    echo "Attendance count: " . count($att ?? [])   . "\n";
    print_r($att);
} else {
    // Check constructor signature
    $ref = new ReflectionClass($zk);
    $constructor = $ref->getConstructor();
    echo "\nConstructor params:\n";
    foreach ($constructor->getParameters() as $param) {
        echo " - " . $param->getName() . "\n";
    }
}
