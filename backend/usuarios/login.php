<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

function responder(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    require_once __DIR__ . '/../conexion_mongodb.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
    }

    $entrada = json_decode(file_get_contents('php://input'), true);

    if (!is_array($entrada)) {
        responder(400, ['ok' => false, 'mensaje' => 'Solicitud inválida.']);
    }

    $correo = strtolower(trim((string)($entrada['correo'] ?? '')));
    $contrasena = (string)($entrada['contrasena'] ?? '');

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $contrasena === '') {
        responder(400, ['ok' => false, 'mensaje' => 'Correo o contraseña inválidos.']);
    }

    $usuario = $db->usuarios->findOne(['correo' => $correo]);

    if (!$usuario || !password_verify($contrasena, $usuario['contrasena_hash'] ?? '')) {
        responder(401, ['ok' => false, 'mensaje' => 'Correo o contraseña incorrectos.']);
    }

    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id' => (string)$usuario['_id'],
        'nombre' => $usuario['nombre'],
        'correo' => $usuario['correo']
    ];

    responder(200, ['ok' => true, 'mensaje' => 'Inicio de sesión correcto.']);

} catch (Throwable $e) {
    error_log('Login KNITS: ' . $e->getMessage());
    responder(500, ['ok' => false, 'mensaje' => 'No se pudo iniciar sesión. Inténtalo más tarde.']);
}
