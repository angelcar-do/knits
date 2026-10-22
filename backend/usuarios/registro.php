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

    $nombre = trim((string)($entrada['nombre'] ?? ''));
    $correo = strtolower(trim((string)($entrada['correo'] ?? '')));
    $contrasena = (string)($entrada['contrasena'] ?? '');

    if ($nombre === '' || mb_strlen($nombre) > 100 ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 190 || strlen($contrasena) < 8) {
        responder(400, ['ok' => false, 'mensaje' => 'Revisa los datos ingresados.']);
    }

    $usuarios = $db->usuarios;

    if ($usuarios->countDocuments(['correo' => $correo]) > 0) {
        responder(409, ['ok' => false, 'mensaje' => 'Este correo ya está registrado.']);
    }

    $usuarios->insertOne([
        'nombre' => $nombre,
        'correo' => $correo,
        'contrasena_hash' => password_hash($contrasena, PASSWORD_DEFAULT),
        'creado_en' => new MongoDB\BSON\UTCDateTime()
    ]);

    responder(201, ['ok' => true, 'mensaje' => 'Cuenta creada correctamente.']);

} catch (Throwable $e) {
    error_log('Registro KNITS: ' . $e->getMessage());
    responder(500, ['ok' => false, 'mensaje' => 'No se pudo crear la cuenta. Inténtalo más tarde.']);
}
