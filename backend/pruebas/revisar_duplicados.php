<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.mongodb.php';

$mongo = new MongoDB\Client($config['uri']);
$db = $mongo->selectDatabase($config['database']);

$duplicados = $db->usuarios->aggregate([
    ['$group' => [
        '_id' => '$correo',
        'cantidad' => ['$sum' => 1]
    ]],
    ['$match' => ['cantidad' => ['$gt' => 1]]]
]);

$encontrados = false;

foreach ($duplicados as $documento) {
    $encontrados = true;
    echo "Correo duplicado: " . $documento['_id'] .
         " | Cantidad: " . $documento['cantidad'] . PHP_EOL;
}

if (!$encontrados) {
    echo "No se encontraron correos duplicados." . PHP_EOL;
}

