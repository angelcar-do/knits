<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/admin.php';
require_once __DIR__ . '/../config/csrf.php';
header('Content-Type: application/json; charset=utf-8');
function responderAccion(int $status, array $data): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') responderAccion(405, ['ok'=>false,'mensaje'=>'Método no permitido.']);
exigirAdministrador();
exigirTokenCsrf();
try {
    require_once __DIR__ . '/../conexion_mongodb.php';
    $id = (string)($_POST['id'] ?? '');
    $accion = (string)($_POST['accion'] ?? '');
    if (!preg_match('/^[a-f0-9]{24}$/i', $id)) responderAccion(400, ['ok'=>false,'mensaje'=>'Identificador no válido.']);
    $activo = match ($accion) { 'publicar' => true, 'ocultar', 'eliminar' => false, default => null };
    if ($activo === null) responderAccion(400, ['ok'=>false,'mensaje'=>'Acción no válida.']);
    $resultado = $db->productos->updateOne(['_id'=>new MongoDB\BSON\ObjectId($id)], ['$set'=>['activo'=>$activo,'actualizado_en'=>new MongoDB\BSON\UTCDateTime()]]);
    if ($resultado->getMatchedCount() === 0) responderAccion(404, ['ok'=>false,'mensaje'=>'Producto no encontrado.']);
    $mensaje = $accion === 'publicar' ? 'Producto publicado.' : ($accion === 'ocultar' ? 'Producto ocultado.' : 'Producto retirado del catálogo.');
    responderAccion(200, ['ok'=>true,'mensaje'=>$mensaje]);
} catch (Throwable $e) {
    error_log('Admin acción producto: '.$e->getMessage());
    responderAccion(500, ['ok'=>false,'mensaje'=>'No se pudo completar la acción.']);
}
