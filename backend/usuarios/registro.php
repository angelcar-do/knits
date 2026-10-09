<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../conexion.php";

function responder(int $estado, array $datos): void {
    http_response_code($estado);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, ["ok" => false, "mensaje" => "Método no permitido."]);
}

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    responder(400, ["ok" => false, "mensaje" => "Datos inválidos."]);
}

$nombre = trim($datos["nombre"] ?? "");
$correo = strtolower(trim($datos["correo"] ?? ""));
$contrasena = $datos["contrasena"] ?? "";

if ($nombre === "" || $correo === "" || $contrasena === "") {
    responder(400, ["ok" => false, "mensaje" => "Completa todos los campos."]);
}

if (strlen($nombre) > 100 || strlen($correo) > 190 ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder(400, ["ok" => false, "mensaje" => "Revisa el nombre y el correo."]);
}

if (strlen($contrasena) < 8) {
    responder(400, ["ok" => false, "mensaje" => "La contraseña debe tener al menos 8 caracteres."]);
}

try {
    $consulta = $pdo->prepare(
        "SELECT id FROM usuarios WHERE correo = ? LIMIT 1"
    );
    $consulta->execute([$correo]);

    if ($consulta->fetch()) {
        responder(409, ["ok" => false, "mensaje" => "Ese correo ya está registrado."]);
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    $insertar = $pdo->prepare(
        "INSERT INTO usuarios (nombre, correo, contrasena_hash)
         VALUES (?, ?, ?)"
    );
    $insertar->execute([$nombre, $correo, $hash]);

    responder(201, ["ok" => true, "mensaje" => "Cuenta creada correctamente."]);

} catch (PDOException $e) {
    error_log("Error de registro: " . $e->getMessage());
    responder(500, ["ok" => false, "mensaje" => "No se pudo completar el registro."]);
}