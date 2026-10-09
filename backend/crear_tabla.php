
<?php
require_once __DIR__ . "/conexion.php";

$sql = "
CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(190) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $pdo->exec($sql);
    echo "TABLA usuarios CREADA CORRECTAMENTE";
} catch (PDOException $e) {
    http_response_code(500);
    echo "ERROR AL CREAR LA TABLA: " . $e->getMessage();
}