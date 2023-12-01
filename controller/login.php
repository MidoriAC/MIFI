<?php
session_start();
include_once "../model/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validación de campos vacíos
    if (empty($username) || empty($password)) {
        echo "<script language='JavaScript'>
        alert('Por favor, ingresa el usuario y la contraseña.');
        location.assign('../views/login.php');
        </script>";
    } else {
        // Verifica las credenciales en la base de datos
        $sql = "SELECT * FROM usuario WHERE username = :username";
        $stmt = $base_de_datos->prepare($sql); // Usamos $base_de_datos en lugar de $conn
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Inicio de sesión exitoso, establece una variable de sesión
            $_SESSION['username'] = $username;
            $_SESSION['userId'] = $user['idUsuario'];//PARA OBTENER EL ID DEL USUARIO DE LA BASE DE DATOS E IMPRIMIR O USARLO EN CUALQUIER PARTE DEL CODIGO
            header("location: ../views/index.php"); // Redirige al usuario a la página deseada después del inicio de sesión
        } else {
            echo "<script language='JavaScript'>
            alert('Usuario o contraseña incorrecta');
            location.assign('../views/login.php');
            </script>";
        }
    }
}
?>