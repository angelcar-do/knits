<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';

function obtenerTokenCsrf(): string
{
    if (
        !isset($_SESSION['csrf_token']) ||
        !is_string($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function exigirTokenCsrf(): void
{
    $tokenSesion = $_SESSION['csrf_token'] ?? '';
    $tokenPeticion = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (
        !is_string($tokenSesion) ||
        $tokenSesion === '' ||
        !is_string($tokenPeticion) ||
        $tokenPeticion === '' ||
        !hash_equals($tokenSesion, $tokenPeticion)
    ) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'ok' => false,
            'mensaje' => 'La solicitud no superó la validación de seguridad. Recarga la página e inténtalo de nuevo.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}
