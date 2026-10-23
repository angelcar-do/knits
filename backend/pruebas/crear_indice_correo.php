<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.mongodb.php';

$mongo = new MongoDB\Client($config['uri']);
$db = $mongo->selectDatabase($config['database']);

$resultado = $db->usuarios->createIndex(
    ['correo' => 1],
    ['unique' => true, 'name' => 'correo_unico']
);

echo "Índice creado o ya existente: " . $resultado . PHP_EOL;
