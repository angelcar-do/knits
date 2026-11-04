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
    $cursor = $db->productos->find([], [
        'projection' => ['codigo'=>1,'nombre'=>1,'descripcion'=>1,'precio'=>1,'imagen'=>1,'categoria'=>1,'activo'=>1],
        'sort' => ['nombre' => 1],
        'limit' => 500
    ]);
    $productos = [];
    foreach ($cursor as $p) {
        $productos[] = [
            'id' => (string) $p['_id'],
            'codigo' => (string)($p['codigo'] ?? ''),
            'nombre' => (string)($p['nombre'] ?? ''),
            'descripcion' => (string)($p['descripcion'] ?? ''),
            'precio' => (float)($p['precio'] ?? 0),
            'imagen' => (string)($p['imagen'] ?? ''),
            'categoria' => (string)($p['categoria'] ?? ''),
            'activo' => (bool)($p['activo'] ?? true)
        ];
    }
    echo json_encode(['ok'=>true,'productos'=>$productos], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    error_log('Admin productos listar: '.$e->getMessage());
    http_response_code(500);
    echo json_encode(['ok'=>false,'mensaje'=>'No se pudieron consultar los productos.']);
}
