<?php
// Inicia la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cierra la sesión
session_destroy();

// Redirige a la página de inicio de sesión
header("location: ../views/login.php");
exit();
?>