<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/admin.php';
require_once __DIR__ . '/../config/csrf.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido.']);
    exit;
}

exigirAdministrador();
exigirTokenCsrf();

try {
    require_once __DIR__ . '/../conexion_mongodb.php';

    $datos = json_decode(
        file_get_contents('php://input'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    $pedidoId = $datos['pedido_id'] ?? '';
    $estado = $datos['estado'] ?? '';

    $estadosPermitidos = [
        'pendiente',
        'en_preparacion',
        'enviado',
        'entregado',
        'cancelado'
    ];

    if (!is_string($pedidoId) ||
        !preg_match('/^[a-f0-9]{24}$/i', $pedidoId)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'mensaje' => 'ID de pedido no válido.']);
        exit;
    }

    if (!is_string($estado) || !in_array($estado, $estadosPermitidos, true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'mensaje' => 'Estado no válido.']);
        exit;
    }

    $resultado = $db->pedidos->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($pedidoId)],
        ['$set' => ['estado' => $estado]]
    );

    if ($resultado->getMatchedCount() === 0) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensaje' => 'Pedido no encontrado.']);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Estado del pedido actualizado.'
    ], JSON_UNESCAPED_UNICODE);
} catch (JsonException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'mensaje' => 'El JSON enviado no es válido.']);
} catch (Throwable $e) {
    error_log('Error al actualizar estado del pedido: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudo actualizar el pedido.'
    ], JSON_UNESCAPED_UNICODE);
}
