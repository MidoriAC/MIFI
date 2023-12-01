<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos (debe incluir tu lógica de conexión)
    include_once "../model/conexion.php";

    // Recupera los datos del formulario
    $username = $_POST['username'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validación de campos (puedes agregar más validaciones si es necesario)

    if (empty($username) || empty($nombre) || empty($email) || empty($password)) {
        echo "<script language='JavaScript'>
        alert('Por favor, ingresa todos los datos solicitados');
        location.assign('../views/login.php');
        </script>";
    } else {
        // Inserta el usuario con idRol = 2 en la tabla 'usuario'
        $insertUsuario = "INSERT INTO usuario (username, password, idRol) VALUES (:username, :password, 2)";
        $stmtUsuario = $base_de_datos->prepare($insertUsuario);
        $stmtUsuario->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtUsuario->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmtUsuario->execute();

        // Obtiene el ID del usuario insertado
        $idUsuario = $base_de_datos->lastInsertId();

        // Inserta el cliente en la tabla 'cliente'
        $insertCliente = "INSERT INTO cliente (idUsuario, nombre, email) VALUES (:idUsuario, :nombre, :email)";
        $stmtCliente = $base_de_datos->prepare($insertCliente);
        $stmtCliente->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmtCliente->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtCliente->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtCliente->execute();

        // Inserta el perfil en la tabla 'perfil'
        $insertPerfil = "INSERT INTO perfil (idUsuario) VALUES (:idUsuario)";
        $stmtPerfil = $base_de_datos->prepare($insertPerfil);
        $stmtPerfil->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmtPerfil->execute();

        // Redirige o muestra un mensaje de registro exitoso
        //header("Location: registro_exitoso.php");
        //header("Location: ../views/login.html");
        echo "<script language='JavaScript'>
        alert('Registro exitoso, por favor Inicia Sesión ');
        location.assign('../views/login.php');
        </script>";
    }
}
?>
