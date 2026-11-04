<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../conexion_mongodb.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Método no permitido.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cursor = $db->productos->find(
        ['activo' => true],
        [
            'projection' => [
                'nombre' => 1,
                'descripcion' => 1,
                'precio' => 1,
                'imagen' => 1,
                'categoria' => 1
            ],
            'sort' => ['nombre' => 1]
        ]
    );

    $productos = [];

    foreach ($cursor as $producto) {
        $productos[] = [
            'nombre' => $producto['nombre'] ?? '',
            'descripcion' => $producto['descripcion'] ?? '',
            'precio' => $producto['precio'] ?? 0,
            'imagen' => $producto['imagen'] ?? '',
            'categoria' => $producto['categoria'] ?? ''
        ];
    }

    echo json_encode([
        'ok' => true,
        'productos' => $productos
    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

} catch (Throwable $e) {
    error_log('Listado de productos KNITS: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudieron consultar los productos.'
    ], JSON_UNESCAPED_UNICODE);
}