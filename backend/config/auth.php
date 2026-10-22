<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';

function exigirUsuarioAutenticado(): array
{
    if (
        !isset($_SESSION['usuario']) ||
        !is_array($_SESSION['usuario']) ||
        empty($_SESSION['usuario']['id'])
    ) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'ok' => false,
            'mensaje' => 'Debes iniciar sesión para continuar.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    return $_SESSION['usuario'];
}
