<?php

$config = require __DIR__ . '/config.local.php';

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['database']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => $config['ssl_ca'],
    ]);

} catch (PDOException $e) {
    error_log('Error de conexi?n MySQL: ' . $e->getMessage());
    http_response_code(500);
    exit('No fue posible conectar con la base de datos.');
}
