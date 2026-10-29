<?php
declare(strict_types=1);

function studioResponder(int $estado, array $datos): never
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function studioExigirMetodo(string $metodo): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $metodo) {
        header('Allow: ' . $metodo);
        studioResponder(405, [
            'ok' => false,
            'mensaje' => 'Método no permitido.'
        ]);
    }
}

function studioLeerJson(): array
{
    try {
        $entrada = json_decode(
            file_get_contents('php://input'),
            true,
            64,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException $e) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'El JSON enviado no es válido.']);
    }

    if (!is_array($entrada)) {
        studioResponder(400, ['ok' => false, 'mensaje' => 'La solicitud no tiene el formato esperado.']);
    }

    return $entrada;
}

function studioCodigoValido(mixed $codigo): bool
{
    return is_string($codigo) && preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $codigo) === 1;
}

function studioImporteValido(mixed $importe): bool
{
    return (is_int($importe) || (is_float($importe) && is_finite($importe)))
        && $importe >= 0
        && $importe <= 1000000000;
}

function studioFechaAhora(): MongoDB\BSON\UTCDateTime
{
    return new MongoDB\BSON\UTCDateTime();
}
