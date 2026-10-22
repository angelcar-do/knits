
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config.mongodb.php';

try {
    $mongo = new MongoDB\Client($config['uri']);

    $db = $mongo->selectDatabase($config['database']);

    // Comprobar la conexión con MongoDB Atlas.
    $mongo->selectDatabase('admin')
          ->command(['ping' => 1]);

} catch (Throwable $e) {
    error_log('Error MongoDB: ' . $e->getMessage());

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');

    exit('No se pudo conectar con la base de datos.');
}