<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Método no permitido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$usuario = exigirUsuarioAutenticado();

require_once __DIR__ . '/../conexion_mongodb.php';

try {
    $cursor = $db->pedidos->find(
        ['usuario_id' => $usuario['id']],
        [
            'sort' => ['creado_en' => -1],
            'limit' => 100
        ]
    );

    $pedidos = [];

    foreach ($cursor as $pedido) {
        $fecha = null;

        if (isset($pedido['creado_en'])) {
            $fecha = $pedido['creado_en']
                ->toDateTime()
                ->format('Y-m-d H:i:s');
        }

        $pedidos[] = [
            'id' => (string) $pedido['_id'],
            'fecha' => $fecha,
            'productos' => $pedido['productos'] ?? [],
            'total' => $pedido['total'] ?? 0,
            'moneda' => $pedido['moneda'] ?? 'COP',
            'estado' => $pedido['estado'] ?? 'pendiente'
        ];
    }

    echo json_encode([
        'ok' => true,
        'pedidos' => $pedidos
    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

} catch (Throwable $e) {
    error_log('Error al listar pedidos: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudieron consultar los pedidos.'
    ], JSON_UNESCAPED_UNICODE);
}
