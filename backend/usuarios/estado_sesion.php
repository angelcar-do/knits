
<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$logueado = (
    isset($_SESSION['usuario']) &&
    is_array($_SESSION['usuario']) &&
    !empty($_SESSION['usuario']['id'])
);

echo json_encode([
    'logueado' => $logueado,
    'csrf_token' => obtenerTokenCsrf()
], JSON_UNESCAPED_UNICODE);
