<?php

header('Content-Type: text/plain; charset=utf-8');

try {
    require __DIR__ . '/conexion.php';

    $resultado = $pdo->query(
        'SELECT VERSION() AS version, DATABASE() AS base_datos'
    );

    $datos = $resultado->fetch();

    echo "CONEXION EXITOSA CON AIVEN\n";
    echo "Version MySQL: " . $datos['version'] . "\n";
    echo "Base de datos: " . $datos['base_datos'] . "\n";

} catch (Throwable $e) {
    error_log('Error en prueba MySQL: ' . $e->getMessage());
    http_response_code(500);
    echo "FALLO LA CONEXION. Revisa la configuracion y los logs de PHP.";
}
