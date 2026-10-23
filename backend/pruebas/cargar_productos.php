<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.mongodb.php';

try {
    $mongo = new MongoDB\Client($config['uri']);
    $db = $mongo->selectDatabase($config['database']);
    $productos = $db->productos;

    $catalogo = [
        [
            'codigo' => 'monedero-tejido',
            'nombre' => 'Monedero tejido',
            'descripcion' => 'Monedero tejido a mano.',
            'precio' => 45000,
            'imagen' => 'img/monedero.jpg',
            'categoria' => 'Accesorios',
            'activo' => true
        ],
        [
            'codigo' => 'vestido-tejido',
            'nombre' => 'Vestido tejido',
            'descripcion' => 'Vestido tejido a mano.',
            'precio' => 200000,
            'imagen' => 'img/vestido.jpg',
            'categoria' => 'Ropa',
            'activo' => true
        ],
        [
            'codigo' => 'gorro-tejido',
            'nombre' => 'Gorro tejido',
            'descripcion' => 'Gorro tejido a mano.',
            'precio' => 25000,
            'imagen' => 'img/gorro.jpg',
            'categoria' => 'Accesorios',
            'activo' => true
        ],
        [
            'codigo' => 'mochila-tejida',
            'nombre' => 'Mochila tejida',
            'descripcion' => 'Mochila tejida a mano.',
            'precio' => 125000,
            'imagen' => 'img/mochila.jpg',
            'categoria' => 'Bolsos',
            'activo' => true
        ]
    ];

    foreach ($catalogo as $producto) {
        $productos->updateOne(
            ['codigo' => $producto['codigo']],
            ['$set' => $producto],
            ['upsert' => true]
        );

        echo "Producto cargado: " . $producto['nombre'] . PHP_EOL;
    }

    echo "Carga del catálogo terminada." . PHP_EOL;

} catch (Throwable $e) {
    error_log('Carga del catálogo KNITS: ' . $e->getMessage());
    fwrite(STDERR, "Error al cargar el catálogo: " . $e->getMessage() . PHP_EOL);
    exit(1);
}