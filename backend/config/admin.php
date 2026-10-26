<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function exigirAdministrador(): array
{
    $usuario = exigirUsuarioAutenticado();

    if (($usuario['rol'] ?? '') !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'ok' => false,
            'mensaje' => 'No tienes permisos para acceder al panel.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    return $usuario;
}

