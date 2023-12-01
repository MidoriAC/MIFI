<?php
include "../model/conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar datos del formulario
    $nombre = $_POST['nombre'];
    $fechaPago = $_POST['fechaPago'];
    $fechaCorte = $_POST['fechaCorte'];
    $idTipoCuenta = $_POST['idTipoCuenta'];
    $saldo = $_POST['saldo'];
    $idUsuario = $_SESSION['userId']; // Esto debe obtener el id del usuario desde la sesión

    // Validar los datos según tus requisitos
    // Puedes agregar validaciones aquí

    // Iniciar una transacción para asegurar la integridad de los datos en ambas tablas
    $base_de_datos->beginTransaction();

    // Preparar la sentencia SQL para la inserción en ctascliente
    $sql_ctascliente = "INSERT INTO ctascliente (nombre, fechaPago, fechaCorte, idTipoCuenta, saldo) VALUES (:nombre, :fechaPago, :fechaCorte, :idTipoCuenta, :saldo)";

    // Preparar la consulta para ctascliente
    $stmt_ctascliente = $base_de_datos->prepare($sql_ctascliente);

    // Vincular los parámetros con los valores para ctascliente
    $stmt_ctascliente->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt_ctascliente->bindParam(':fechaPago', $fechaPago, PDO::PARAM_STR);
    $stmt_ctascliente->bindParam(':fechaCorte', $fechaCorte, PDO::PARAM_STR);
    $stmt_ctascliente->bindParam(':idTipoCuenta', $idTipoCuenta, PDO::PARAM_INT);
    $stmt_ctascliente->bindParam(':saldo', $saldo, PDO::PARAM_INT);

    // Ejecutar la consulta para ctascliente
    if ($stmt_ctascliente->execute()) {
        // Inserción exitosa en ctascliente, obtén el último ID insertado
        $lastInsertId = $base_de_datos->lastInsertId();

        // Preparar la sentencia SQL para la inserción en cuenta
        $sql_cuenta = "INSERT INTO cuenta (idCliente, idCtaCliente) VALUES (:idCliente, :idCtaCliente)";

        // Preparar la consulta para cuenta
        $stmt_cuenta = $base_de_datos->prepare($sql_cuenta);

        // Vincular los parámetros con los valores para cuenta
        $stmt_cuenta->bindParam(':idCliente', $idUsuario, PDO::PARAM_INT); // Asegúrate de que $idUsuario sea correcto
        $stmt_cuenta->bindParam(':idCtaCliente', $lastInsertId, PDO::PARAM_INT); // Usamos el último ID insertado en ctascliente

        // Ejecutar la consulta para cuenta
        if ($stmt_cuenta->execute()) {
            // Inserción exitosa en ambas tablas, confirmar la transacción
            $base_de_datos->commit();

            // Redirigir a la página de éxito o a donde lo necesites
            header("Location: ../views/index.php");
            exit();
        } else {
            // Manejar errores si la inserción en cuenta no fue exitosa
            $base_de_datos->rollBack();
            echo "Error al crear la cuenta en la tabla cuenta: " . $stmt_cuenta->errorInfo()[2];
        }
    } else {
        // Manejar errores si la inserción en ctascliente no fue exitosa
        $base_de_datos->rollBack();
        echo "Error al crear la cuenta en la tabla ctascliente: " . $stmt_ctascliente->errorInfo()[2];
    }
}
?>