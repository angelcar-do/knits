<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/admin.php';
require_once __DIR__ . '/../config/csrf.php';
header('Content-Type: application/json; charset=utf-8');

function responderProducto(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') responderProducto(405, ['ok'=>false,'mensaje'=>'Método no permitido.']);
exigirAdministrador();
exigirTokenCsrf();

try {
    require_once __DIR__ . '/../conexion_mongodb.php';
    $id = trim((string)($_POST['id'] ?? ''));
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $categoria = trim((string)($_POST['categoria'] ?? ''));
    $precioRaw = $_POST['precio'] ?? null;
    $activoRaw = (string)($_POST['activo'] ?? '1');
    if ($nombre === '' || mb_strlen($nombre) > 100) responderProducto(422, ['ok'=>false,'mensaje'=>'Escribe un nombre de hasta 100 caracteres.']);
    if ($categoria === '' || mb_strlen($categoria) > 60) responderProducto(422, ['ok'=>false,'mensaje'=>'Escribe una categoría de hasta 60 caracteres.']);
    if (!is_numeric($precioRaw) || (float)$precioRaw < 0 || (float)$precioRaw > 1000000000) responderProducto(422, ['ok'=>false,'mensaje'=>'El precio no es válido.']);
    if (mb_strlen($descripcion) > 1000) responderProducto(422, ['ok'=>false,'mensaje'=>'La descripción supera los 1000 caracteres.']);
    if (!in_array($activoRaw, ['0','1'], true)) responderProducto(422, ['ok'=>false,'mensaje'=>'La disponibilidad no es válida.']);

    $coleccion = $db->productos;
    $existente = null;
    if ($id !== '') {
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) responderProducto(400, ['ok'=>false,'mensaje'=>'Identificador de producto no válido.']);
        $existente = $coleccion->findOne(['_id'=>new MongoDB\BSON\ObjectId($id)]);
        if (!$existente) responderProducto(404, ['ok'=>false,'mensaje'=>'El producto ya no existe.']);
    }

    $imagenRuta = (string)($existente['imagen'] ?? '');
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        $archivo = $_FILES['imagen'];
        if ($archivo['error'] !== UPLOAD_ERR_OK) responderProducto(422, ['ok'=>false,'mensaje'=>'No se pudo recibir la fotografía.']);
        if ($archivo['size'] > 5 * 1024 * 1024) responderProducto(422, ['ok'=>false,'mensaje'=>'La fotografía no puede superar 5 MB.']);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);
        $extensiones = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!isset($extensiones[$mime]) || @getimagesize($archivo['tmp_name']) === false) responderProducto(422, ['ok'=>false,'mensaje'=>'Formato no válido. Usa JPG, PNG o WEBP.']);
        $directorio = dirname(__DIR__, 2) . '/uploads/productos';
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) throw new RuntimeException('No se pudo preparar la carpeta de imágenes.');
        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $extensiones[$mime];
        if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombreArchivo)) throw new RuntimeException('No se pudo guardar la fotografía.');
        $imagenRuta = 'uploads/productos/' . $nombreArchivo;
    } elseif (!$existente) {
        responderProducto(422, ['ok'=>false,'mensaje'=>'Selecciona una fotografía para el producto.']);
    }

    $codigo = (string)($existente['codigo'] ?? '');
    if ($codigo === '') {
        $base = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre) ?: $nombre;
        $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $base), '-'));
        if ($base === '') $base = 'producto';
        $codigo = substr($base, 0, 45) . '-' . bin2hex(random_bytes(3));
    }
    $datos = [
        'codigo' => $codigo,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => (float)$precioRaw,
        'imagen' => $imagenRuta,
        'categoria' => $categoria,
        'activo' => $activoRaw === '1',
        'actualizado_en' => new MongoDB\BSON\UTCDateTime()
    ];
    if ($existente) {
        $coleccion->updateOne(['_id'=>new MongoDB\BSON\ObjectId($id)], ['$set'=>$datos]);
        responderProducto(200, ['ok'=>true,'mensaje'=>'Producto actualizado correctamente.']);
    }
    $datos['creado_en'] = new MongoDB\BSON\UTCDateTime();
    $coleccion->insertOne($datos);
    responderProducto(201, ['ok'=>true,'mensaje'=>'Producto publicado correctamente.']);
} catch (Throwable $e) {
    error_log('Admin guardar producto: '.$e->getMessage());
    responderProducto(500, ['ok'=>false,'mensaje'=>'No se pudo guardar el producto. Revisa el registro de errores del servidor.']);
}
