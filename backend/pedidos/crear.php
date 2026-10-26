<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/csrf.php';

header('Content-Type: application/json; charset=utf-8');

function responderPedido(int $estado, array $datos): never
{
    http_response_code($estado);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        responderPedido(405, [
            'ok' => false,
            'mensaje' => 'Método no permitido.'
        ]);
    }

    // Solo los usuarios con sesión pueden crear pedidos.
    $usuario = exigirUsuarioAutenticado();
exigirTokenCsrf();
    require_once __DIR__ . '/../conexion_mongodb.php';

    $entrada = json_decode(
        file_get_contents('php://input'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    if (!is_array($entrada) ||
        !isset($entrada['productos']) ||
        !is_array($entrada['productos']) ||
        count($entrada['productos']) === 0 ||
        count($entrada['productos']) > 50) {
        responderPedido(400, [
            'ok' => false,
            'mensaje' => 'El carrito está vacío o no es válido.'
        ]);
    }

    $productosPedido = [];
    $total = 0;

    foreach ($entrada['productos'] as $item) {
        if (!is_array($item)) {
            responderPedido(400, [
                'ok' => false,
                'mensaje' => 'Hay un producto inválido en el pedido.'
            ]);
        }

        $codigo = $item['codigo'] ?? '';
        $cantidad = filter_var(
            $item['cantidad'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            !is_string($codigo) ||
            !preg_match('/^[a-z0-9-]{1,80}$/', $codigo) ||
            $cantidad === false ||
            $cantidad < 1 ||
            $cantidad > 100
        ) {
            responderPedido(400, [
                'ok' => false,
                'mensaje' => 'Revisa los productos y las cantidades.'
            ]);
        }

        $producto = $db->productos->findOne([
            'codigo' => $codigo,
            'activo' => true
        ]);

        if (!$producto) {
            responderPedido(400, [
                'ok' => false,
                'mensaje' => 'Un producto ya no está disponible.'
            ]);
        }

        $precio = $producto['precio'] ?? null;

        if (
            !is_numeric($precio) ||
            !is_finite((float)$precio) ||
            (float)$precio < 0 ||
            (float)$precio > 1000000000 ||
            floor((float)$precio) !== (float)$precio
        ) {
            responderPedido(500, [
                'ok' => false,
                'mensaje' => 'Hay un precio inválido en el catálogo.'
            ]);
        }

        $precio = (int)$precio;
        $subtotal = $precio * $cantidad;

        if ($subtotal > 100000000000) {
            responderPedido(400, [
                'ok' => false,
                'mensaje' => 'El importe del pedido es demasiado alto.'
            ]);
        }

        $total += $subtotal;

        $productosPedido[] = [
            'codigo' => $codigo,
            'nombre' => (string)$producto['nombre'],
            'precio_unitario' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        ];
    }

    if ($total <= 0) {
        responderPedido(400, [
            'ok' => false,
            'mensaje' => 'El total del pedido debe ser mayor que cero.'
        ]);
    }

    $resultado = $db->pedidos->insertOne([
        'usuario_id' => $usuario['id'],
        'productos' => $productosPedido,
        'total' => $total,
        'moneda' => 'COP',
        'estado' => 'pendiente',
        'creado_en' => new MongoDB\BSON\UTCDateTime()
    ]);

    responderPedido(201, [
        'ok' => true,
        'mensaje' => 'Pedido registrado correctamente.',
        'pedido_id' => (string)$resultado->getInsertedId(),
        'total' => $total,
        'moneda' => 'COP'
    ]);

} catch (JsonException $e) {
    responderPedido(400, [
        'ok' => false,
        'mensaje' => 'El contenido enviado no es un JSON válido.'
    ]);
} catch (Throwable $e) {
    error_log('Crear pedido KNITS: ' . $e->getMessage());

    responderPedido(500, [
        'ok' => false,
        'mensaje' => 'No se pudo registrar el pedido.'
    ]);
}
