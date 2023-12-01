<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // El usuario no ha iniciado sesión, redirige a la página de inicio de sesión
    header("location: ../views/login.php");
    exit(); // Asegura que el script se detenga después de la redirección
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
</head>
<body>
    <h1>¡Bienvenido a la página de inicio <?php echo $_SESSION['username']; ?>!</h1>
    <p>Este es el contenido de la sección de inicio.</p>
</body>
</html>
