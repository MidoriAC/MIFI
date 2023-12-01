<?php
include "../model/conexion.php";

// Verificar si los valores de los filtros están definidos en $_GET
$fechaInicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : null;
$fechaFinal = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : null;
$idCuenta = isset($_GET['idCuenta']) ? $_GET['idCuenta'] : null;

// Obtener el ID del usuario desde la sesión
session_start();
$idUsuario = $_SESSION['userId'];

// Realizar la consulta de movimientos con los filtros
$sql = "SELECT m.idMovimiento, cc.nombre AS nombreCuenta, m.monto, tm.nombreMovimiento AS nombreTipoMovimiento, m.descripcion, m.fechaMov
        FROM movimiento m
        INNER JOIN ctascliente cc ON m.idCtaCliente = cc.idCtasCliente
        INNER JOIN tipomovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
        INNER JOIN cuenta c ON cc.idCtasCliente = c.idCtaCliente
        INNER JOIN cliente cl ON c.idCliente = cl.idCliente
        WHERE cl.idUsuario = :idUsuario";

// Agregar condiciones de filtro según los valores proporcionados
if (!empty($fechaInicial)) {
    $sql .= " AND m.fechaMov >= :fechaInicial";
}

if (!empty($fechaFinal)) {
    $sql .= " AND m.fechaMov <= :fechaFinal";
}

if (!empty($idCuenta)) {
    $sql .= " AND cc.idCtasCliente = :idCuenta";
}

$sql .= " ORDER BY m.fechaMov DESC";

// Preparar y ejecutar la consulta
$stmt = $base_de_datos->prepare($sql);
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

if (!empty($fechaInicial)) {
    $stmt->bindParam(':fechaInicial', $fechaInicial, PDO::PARAM_STR);
}

if (!empty($fechaFinal)) {
    $stmt->bindParam(':fechaFinal', $fechaFinal, PDO::PARAM_STR);
}

if (!empty($idCuenta)) {
    $stmt->bindParam(':idCuenta', $idCuenta, PDO::PARAM_INT);
}

$stmt->execute();

// Obtener los resultados y devolverlos como HTML
$movimientos = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($movimientos as $movimiento) {
    echo "<tr>";
    echo "<td>{$movimiento->idMovimiento}</td>";
    echo "<td>{$movimiento->nombreCuenta}</td>";
    echo "<td>{$movimiento->monto}</td>";
    echo "<td>{$movimiento->nombreTipoMovimiento}</td>";
    echo "<td>{$movimiento->descripcion}</td>";
    echo "<td>{$movimiento->fechaMov}</td>";
    echo "</tr>";
}
?>