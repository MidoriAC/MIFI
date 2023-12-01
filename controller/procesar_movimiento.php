<?php
include "../model/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $descripcion = $_POST["descripcion"];
    $idTipoMovimiento = $_POST["idTipoMovimiento"];
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0.0;
    $idCtasCliente = $_POST["idCuenta"];

    // Consulta el saldo actual de la cuenta
    $sql_saldo_actual = "SELECT saldo FROM ctascliente WHERE idCtasCliente = :idCtasCliente";
    $stmt_saldo_actual = $base_de_datos->prepare($sql_saldo_actual);
    $stmt_saldo_actual->bindParam(':idCtasCliente', $idCtasCliente, PDO::PARAM_INT);
    $stmt_saldo_actual->execute();

    // Obtiene el saldo actual
    $saldo_actual = $stmt_saldo_actual->fetchColumn();

    // Verifica si el saldo es suficiente para un egreso
    if ($idTipoMovimiento == 2 && $monto > $saldo_actual) {
        echo "No puedes realizar el egreso. Saldo insuficiente.";
    } else {
        // El saldo es suficiente o es un ingreso, puedes proceder con la transacción
        try {
            // Insertar en la tabla movimiento
            $stmt_insert_movimiento = $base_de_datos->prepare("INSERT INTO movimiento (idCtaCliente, monto, idTipoMovimiento, descripcion) VALUES (:idCtaCliente, :monto, :idTipoMovimiento, :descripcion)");
            $stmt_insert_movimiento->bindParam(':idCtaCliente', $idCtasCliente, PDO::PARAM_INT);
            $stmt_insert_movimiento->bindParam(':monto', $monto, PDO::PARAM_STR);
            $stmt_insert_movimiento->bindParam(':idTipoMovimiento', $idTipoMovimiento, PDO::PARAM_INT);
            $stmt_insert_movimiento->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt_insert_movimiento->execute();

            // Actualizar el saldo en la tabla ctascliente
            $saldo_column = ($idTipoMovimiento == 1) ? 'saldo + :monto' : 'saldo - :monto';
            $stmt_update_saldo = $base_de_datos->prepare("UPDATE ctascliente SET saldo = $saldo_column WHERE idCtasCliente = :idCtasCliente");
            $stmt_update_saldo->bindParam(':idCtasCliente', $idCtasCliente, PDO::PARAM_INT);
            $stmt_update_saldo->bindParam(':monto', $monto, PDO::PARAM_STR);
            $stmt_update_saldo->execute();

            echo "<script language='JavaScript'>
            alert('Movimiento creado con exito');
            location.assign('../views/index.php');
            </script>";

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Acceso no permitido.";
}


?>