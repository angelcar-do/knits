<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/sesion.php';

header('Content-Type: application/json; charset=utf-8');

$logueado = (
    isset($_SESSION['usuario']) &&
    is_array($_SESSION['usuario']) &&
    !empty($_SESSION['usuario']['id'])
);

echo json_encode([
    'logueado' => $logueado
], JSON_UNESCAPED_UNICODE);