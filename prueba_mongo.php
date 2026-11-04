<?php
require 'vendor/autoload.php';

$config = require 'config.mongodb.php';

$cliente = new MongoDB\Client($config['uri']);
$cliente->selectDatabase('admin')->command(['ping' => 1]);

echo "MONGODB CONECTADO CORRECTAMENTE";
