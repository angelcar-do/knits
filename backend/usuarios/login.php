<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

function responder($estado, $datos) {
    http_response_code($estado);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, [
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
}

require_once __DIR__ . "/../conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    responder(400, [
        "ok" => false,
        "mensaje" => "Los datos enviados no son válidos."
    ]);
}

$correo = strtolower(trim($datos["correo"] ?? ""));
$contrasena = $datos["contrasena"] ?? "";

if ($correo === "" || $contrasena === "") {
    responder(400, [
        "ok" => false,
        "mensaje" => "Escribe tu correo y contraseña."
    ]);
}

try {
    $consulta = $pdo->prepare(
        "SELECT id, nombre, correo, contrasena_hash
         FROM usuarios
         WHERE correo = ?
         LIMIT 1"
    );

    $consulta->execute([$correo]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario ||
        !password_verify($contrasena, $usuario["contrasena_hash"])) {
        responder(401, [
            "ok" => false,
            "mensaje" => "Correo o contraseña incorrectos."
        ]);
    }

    session_regenerate_id(true);

    $_SESSION["usuario_id"] = $usuario["id"];
    $_SESSION["usuario_nombre"] = $usuario["nombre"];
    $_SESSION["usuario_correo"] = $usuario["correo"];

    responder(200, [
        "ok" => true,
        "mensaje" => "Inicio de sesión correcto.",
        "usuario" => [
            "nombre" => $usuario["nombre"],
            "correo" => $usuario["correo"]
        ]
    ]);

} catch (PDOException $e) {
    error_log("Error de inicio de sesión: " . $e->getMessage());

    responder(500, [
        "ok" => false,
        "mensaje" => "Error al consultar la base de datos."
    ]);
}