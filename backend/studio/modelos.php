<?php
declare(strict_types=1);

require_once __DIR__ . '/_comun.php';
header('Content-Type: application/json; charset=utf-8');
studioExigirMetodo('GET');

try {
    require_once __DIR__ . '/../conexion_mongodb.php';

    $cursor = $db->studio_modelos->find(
        ['activo' => true],
        [
            'projection' => [
                'codigo' => 1,
                'nombre' => 1,
                'descripcion' => 1,
                'imagen' => 1,
                'modelo_3d' => 1,
                'piezas' => 1,
                'tamanos' => 1,
                'opciones' => 1
            ],
            'sort' => ['nombre' => 1],
            'limit' => 100
        ]
    );

    $modelos = [];
    foreach ($cursor as $modelo) {
        $modelos[] = [
            'codigo' => (string)($modelo['codigo'] ?? ''),
            'nombre' => (string)($modelo['nombre'] ?? ''),
            'descripcion' => (string)($modelo['descripcion'] ?? ''),
            'imagen' => (string)($modelo['imagen'] ?? ''),
            'modelo_3d' => $modelo['modelo_3d'] ?? null,
            'piezas' => $modelo['piezas'] ?? [],
            'tamanos' => $modelo['tamanos'] ?? [],
            'opciones' => $modelo['opciones'] ?? []
        ];
    }

    studioResponder(200, ['ok' => true, 'modelos' => $modelos]);
} catch (Throwable $e) {
    error_log('Studio modelos: ' . $e->getMessage());
    studioResponder(500, ['ok' => false, 'mensaje' => 'No se pudieron consultar los modelos.']);
}
