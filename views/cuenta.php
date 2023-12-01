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
 //Codigo para el listado
 if (isset($_SESSION['userId'])) {
    $idUsuario = $_SESSION['userId'];

    // Realiza la consulta para obtener las cuentas del cliente que inició sesión
    $sql = "SELECT cc.idCtasCliente, cc.nombre, cc.fechaPago, cc.fechaCorte, tc.nombre AS nombreTipoCuenta, cc.saldo
            FROM ctascliente cc
            INNER JOIN cuenta c ON cc.idCtasCliente = c.idCtaCliente
            INNER JOIN tipocuenta tc ON cc.idTipoCuenta = tc.idTipoCuenta
            INNER JOIN cliente cl ON c.idCliente = cl.idCliente
            WHERE cl.idUsuario = :idUsuario";

    $stmt = $base_de_datos->prepare($sql);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    $cta = $stmt->fetchAll(PDO::FETCH_OBJ);
} else {
    // Maneja el caso en que el ID de usuario no esté disponible en la sesión
    echo "Error: No se pudo obtener el ID de usuario desde la sesión.";
}
  ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/usuario.css">

</head>
<body>
<div class ="conteiner">
    <div class="contenedor1">
        <div class="centrado">
    <div class="contendorFormulario">
    
    <form id="formCuenta" action="../controller/crearcta.php" method="POST">
    <h1>Crea una cuenta para tener movimientos en ella</h1>
    <h2>Toma en cuenta: </h2><p>Las fechas de Pago y Corte solo serán necesarias <br>en caso de un Tarjeta de Crédito, no es obligatorio.</p> 
        <label for="nombre">Nombre de la Cuenta:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>

        <label for="fechaPago">Fecha de Pago:</label>
        <input type="date" id="fechaPago" name="fechaPago" >
        <br>

        <label for="fechaCorte">Fecha de Corte:</label>
        <input type="date" id="fechaCorte" name="fechaCorte" >
        <br>

        <label for="idTipoCuenta">Tipo de Cuenta:</label>
        <select id="idTipoCuenta" name="idTipoCuenta" required>
            <option value="">Seleccione un tipo de cuenta</option>
            <!-- Aquí deberías cargar las opciones desde la tabla tipocuenta en la base de datos -->
            <!-- Puedes usar PHP para generar las opciones dinámicamente -->
            <?php
            // Conecta a la base de datos
            include "../model/conexion.php";
            $sql = "SELECT idTipoCuenta, nombre FROM tipocuenta";
            $stmt = $base_de_datos->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['idTipoCuenta']}'>{$row['nombre']}</option>";
            }
            ?>
        </select>
        <br>

        <label for="saldo">Saldo:</label>
        <input type="text" id="saldo" name="saldo" required>
        <br>

        <input id="button" type="submit" value="Crear Cuenta">
    </form>
    </div>
    </div>
    </div>
    
    <div class="contenedorListado">
    <div class="row">
        <div class="col-12">
            <h1>Listado de Cuentas</h1>
            <br>
            <div class="table-responsive">
                <table>
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre/Alias</th>
                            <th>Fecha de Pago</th>
                            <th>Fecha de Corte</th>
                            <th>Tipo de Cuenta</th>
                            <th>Saldo</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cta as $cuenta): ?>
                            <tr>
                                <td><?php echo $cuenta->idCtasCliente; ?></td>
                                <td><?php echo $cuenta->nombre; ?></td>
                                <td><?php echo $cuenta->fechaPago; ?></td>
                                <td><?php echo $cuenta->fechaCorte; ?></td>
                                <td><?php echo $cuenta->nombreTipoCuenta; ?></td>
                                <td><?php echo 'Q. ' . number_format($cuenta->saldo, 2); ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
                     </tbody>
                 </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
