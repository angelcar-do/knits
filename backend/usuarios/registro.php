```php
<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../conexion.php";

// ==========================================
// RESPUESTA JSON
// ==========================================

function responder(int $estado, array $datos): void
{
    http_response_code($estado);

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

// ==========================================
// VALIDAR MÉTODO HTTP
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, [
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
}

// ==========================================
// RECIBIR LOS DATOS
// ==========================================

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($datos)) {
    responder(400, [
        "ok" => false,
        "mensaje" => "Los datos enviados no son válidos."
    ]);
}

$nombre = trim($datos["nombre"] ?? "");
$correo = strtolower(trim($datos["correo"] ?? ""));
$contrasena = $datos["contrasena"] ?? "";

// ==========================================
// VALIDAR CAMPOS OBLIGATORIOS
// ==========================================

if ($nombre === "" || $correo === "" || $contrasena === "") {
    responder(400, [
        "ok" => false,
        "mensaje" => "Completa todos los campos."
    ]);
}

// ==========================================
// VALIDAR NOMBRE Y CORREO
// ==========================================

if (
    strlen($nombre) > 100 ||
    strlen($correo) > 190 ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL)
) {
    responder(400, [
        "ok" => false,
        "mensaje" => "Revisa el nombre y el correo electrónico."
    ]);
}

// ==========================================
// VALIDAR CONTRASEÑA
// ==========================================

if (!is_string($contrasena) || strlen($contrasena) < 8) {
    responder(400, [
        "ok" => false,
        "mensaje" => "La contraseña debe tener al menos 8 caracteres."
    ]);
}

// ==========================================
// REGISTRAR USUARIO EN LA BASE DE DATOS
// ==========================================

try {
    // Comprobar si el correo ya existe.
    $consulta = $pdo->prepare(
        "SELECT id
         FROM usuarios
         WHERE correo = ?
         LIMIT 1"
    );

    $consulta->execute([$correo]);

    if ($consulta->fetch(PDO::FETCH_ASSOC)) {
        responder(409, [
            "ok" => false,
            "mensaje" => "Ese correo electrónico ya está registrado."
        ]);
    }

    // Encriptar la contraseña antes de guardarla.
    $hash = password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );

    if ($hash === false) {
        responder(500, [
            "ok" => false,
            "mensaje" => "No se pudo procesar la contraseña."
        ]);
    }

    // Guardar el nuevo usuario.
    $insertar = $pdo->prepare(
        "INSERT INTO usuarios (
            nombre,
            correo,
            contrasena_hash
        ) VALUES (?, ?, ?)"
    );

    $insertar->execute([
        $nombre,
        $correo,
        $hash
    ]);

    responder(201, [
        "ok" => true,
        "mensaje" => "Cuenta creada correctamente."
    ]);

} catch (PDOException $e) {
    error_log("Error de registro: " . $e->getMessage());

    responder(500, [
        "ok" => false,
        "mensaje" => "No se pudo completar el registro. Inténtalo nuevamente."
    ]);
}
```
