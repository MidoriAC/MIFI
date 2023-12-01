<?php
$contraseña = "";
$usuario = "root";
$nombreBaseDeDatos = "mifi";
$rutaServidor = "localhost";
//$puerto = "3306";

try {
    $base_de_datos = new PDO("mysql:host=$rutaServidor;dbname=$nombreBaseDeDatos", $usuario, $contraseña);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ocurrió un error en la base de datos: " . $e->getMessage();
}
?>