<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/admin.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido.']);
    exit;
}

exigirAdministrador();

try {
    require_once __DIR__ . '/../conexion_mongodb.php';

    $pedidos = $db->pedidos->find(
        [],
        [
            'sort' => ['creado_en' => -1],
            'limit' => 200
        ]
    );

    $resultado = [];

    foreach ($pedidos as $pedido) {
        $resultado[] = [
            'id' => (string) $pedido['_id'],
            'usuario_id' => (string) ($pedido['usuario_id'] ?? ''),
            'productos' => $pedido['productos'] ?? [],
            'total' => $pedido['total'] ?? 0,
            'moneda' => $pedido['moneda'] ?? 'COP',
            'estado' => $pedido['estado'] ?? 'pendiente',
            'creado_en' => isset($pedido['creado_en'])
                ? $pedido['creado_en']->toDateTime()->format('Y-m-d H:i:s')
                : null
        ];
    }

    echo json_encode(
        ['ok' => true, 'pedidos' => $resultado],
        JSON_UNESCAPED_UNICODE
    );
} catch (Throwable $e) {
    error_log('Error al listar pedidos de administración: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudieron cargar los pedidos.'
    ], JSON_UNESCAPED_UNICODE);
}
