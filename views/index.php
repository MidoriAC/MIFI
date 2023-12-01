<?php
include "../model/conexion.php";
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // El usuario no ha iniciado sesión, redirige a la página de inicio de sesión
    header("location: ../views/login.php");
    exit(); // Asegura que el script se detenga después de la redirección
}
// A partir de aquí, puedes mostrar el contenido de la página principal solo si el usuario ha iniciado sesión

// Asegúrate de que el usuario haya iniciado sesión antes de realizar la consulta
if (isset($_SESSION['userId'])) {
    $idUsuario = $_SESSION['userId'];
  } else {
    // Obtén el nombre de usuario de la sesión
    $nombreUsuario = $_SESSION['username'];
  
    // Realiza una consulta para obtener el ID del usuario utilizando PDO
    $sql = "SELECT idUsuario FROM usuario WHERE username = :nombreUsuario";
    $stmt = $base_de_datos->prepare($sql);
    $stmt->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
    $stmt->execute();
  
    if ($stmt->rowCount() > 0) {
        $fila = $stmt->fetch();
        $idUsuario = $fila['idUsuario'];
  
        // Almacena el ID de usuario en la sesión para su posterior uso
        $_SESSION['userId'] = $idUsuario;
    } else {
        // Maneja el error si la consulta no fue exitosa
        echo "Error en la consulta: No se pudo obtener el ID de usuario";
    }
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIFI</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    
    <link rel="short cut icon" href="../public/icons/iconomarca.ico">
    <!--Fuente para textro de Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+SA+Beginner&family=Noto+Sans&family=Ubuntu:wght@500&display=swap" rel="stylesheet">

</head>
<body>
    <div class="sidebar">
        <img id="logo" src="../public/img/logo_oficial-sinfondo.png" alt="">
       
        <ul>
            <li><a href="#" onclick="cargarContenido('home')"><img src="../public/icons/home.svg" alt="">Home</a></li>
            <li><a href="#" onclick="cargarContenido('cuenta')"><img src="../public/icons/clipboard.svg" alt="">Cuenta</a></li>
            <li><a href="#" onclick="cargarContenido('movimiento')"><img src="../public/icons/clipboard.svg" alt="">Moviemiento</a></li>
            <li><a href="../controller/logout.php"><img src="../public/icons/user.svg" alt="">Salir</a></li>
        </ul>
    </div>
    <div class="content">
        <div id="contenido">
            <!-- Contenido dinámico se cargará aquí -->
        </div>
    </div>
    <script src="../views/js/scriptindex.js"></script>
</body>
</html>
