<?php
require_once __DIR__ . "/conexion.php";

echo "BASE DE DATOS ACTUAL:\n";
echo $pdo->query("SELECT DATABASE()")->fetchColumn() . "\n\n";

$sql = "SELECT id, nombre, correo, creado_en FROM usuarios ORDER BY id DESC";
$usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (!$usuarios) {
    echo "Todavía no hay usuarios registrados.\n";
    exit;
}

echo "USUARIOS REGISTRADOS:\n";
foreach ($usuarios as $usuario) {
    echo "ID: " . $usuario["id"] . "\n";
    echo "Nombre: " . $usuario["nombre"] . "\n";
    echo "Correo: " . $usuario["correo"] . "\n";
    echo "Fecha: " . $usuario["creado_en"] . "\n";
    echo "-------------------------\n";
}