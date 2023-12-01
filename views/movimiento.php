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



//Para listado de los movimientos del usuario
// Asegúrate de que el usuario haya iniciado sesión antes de realizar la consulta
if (isset($_SESSION['userId'])) {
    $idUsuario = $_SESSION['userId'];

    // Realiza la consulta para obtener los movimientos del cliente que inició sesión
    $sql_movimientos_usuario = "SELECT m.idMovimiento, cc.nombre AS nombreCuenta, m.monto, tm.nombreMovimiento AS nombreTipoMovimiento, m.descripcion, m.fechaMov
                                FROM movimiento m
                                INNER JOIN ctascliente cc ON m.idCtaCliente = cc.idCtasCliente
                                INNER JOIN tipomovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
                                INNER JOIN cuenta c ON cc.idCtasCliente = c.idCtaCliente
                                INNER JOIN cliente cl ON c.idCliente = cl.idCliente
                                WHERE cl.idUsuario = :idUsuario
                                ORDER BY m.fechaMov DESC";

    $stmt_movimientos_usuario = $base_de_datos->prepare($sql_movimientos_usuario);
    $stmt_movimientos_usuario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt_movimientos_usuario->execute();

    $movimientos = $stmt_movimientos_usuario->fetchAll(PDO::FETCH_OBJ);
} else {
    // Maneja el caso en que el ID de usuario no esté disponible en la sesión
    echo "Error: No se pudo obtener el ID de usuario desde la sesión.";
}
  ?>

<!DOCTYPE html>
<html lang="es">
<head>
</head>
<body>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/usuario.css">


    <script>
    function filtrar() {
        // Obtener los valores de los campos de filtro
        var fechaInicial = document.getElementById('fecha_inicial').value;
        var fechaFinal = document.getElementById('fecha_final').value;
        var idCuenta = document.getElementById('filtro_cuenta').value;

        // Realizar la solicitud AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Actualizar la tabla con los datos recibidos
                document.getElementById('tabla_movimientos').innerHTML = xhr.responseText;
            }
        };

        // Construir la URL de la solicitud AJAX
        var url = '../controller/filtros.php?fecha_inicial=' + fechaInicial + '&fecha_final=' + fechaFinal + '&idCuenta=' + idCuenta;
        xhr.open('GET', url, true);
        xhr.send();
    }
</script>

</head>
<body>
    <div class="conteiner">
        <div class="contenedor1">
            <div class="centrado">
                <div class="contendorFormulario">
                    <form action="../controller/procesar_movimiento.php" method="POST">
                        <h1>Registrar Movimiento</h1>
                        <label for="idCuenta">Selecciona la cuenta:</label>
                        <select id="idCuenta" name="idCuenta" required>
                            <!-- Aquí puedes obtener las cuentas del usuario desde la base de datos -->
                            <?php
                            // Realiza la consulta para obtener las cuentas del cliente que inició sesión
                            $sql_cuentas_usuario = "SELECT cc.idCtasCliente, cc.nombre
                                                    FROM ctascliente cc
                                                    INNER JOIN cuenta c ON cc.idCtasCliente = c.idCtaCliente
                                                    INNER JOIN cliente cl ON c.idCliente = cl.idCliente
                                                    WHERE cl.idUsuario = :idUsuario";

                            $stmt_cuentas_usuario = $base_de_datos->prepare($sql_cuentas_usuario);
                            $stmt_cuentas_usuario->bindParam(':idUsuario', $_SESSION['userId'], PDO::PARAM_INT);
                            $stmt_cuentas_usuario->execute();

                            while ($cuenta_usuario = $stmt_cuentas_usuario->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$cuenta_usuario['idCtasCliente']}'>{$cuenta_usuario['nombre']}</option>";
                            }
                            ?>
                        </select>
                        <label for="descripcion">Descripción:</label>
                        <input type="text" name="descripcion" required>

                        <label for="idTipoMovimiento">Tipo de Movimiento:</label>
                        <select id="idTipoMovimiento" name="idTipoMovimiento" required>
                            <!-- Aquí puedes obtener los tipos de movimiento desde la base de datos -->
                            <?php
                            // Realiza la consulta para obtener los tipos de movimiento desde la base de datos
                            $sql_tipos_movimiento = "SELECT * FROM tipomovimiento";
                            $stmt_tipos_movimiento = $base_de_datos->query($sql_tipos_movimiento);

                            // Itera sobre los resultados y crea opciones para el select
                            while ($tipo_movimiento = $stmt_tipos_movimiento->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$tipo_movimiento['idTipoMovimiento']}'>{$tipo_movimiento['nombreMovimiento']}</option>";
                            }
                            ?>
                        </select>

                        <label for="monto">Monto:</label>
                        <input type="number" name="monto" step="0.01" required>

                        <button id="button" type="submit">Registrar Movimiento</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="contenedorListado" id="contenedorListado">
            <!--<form id="filtroForm" onsubmit="filtrar(); return false;"  method="POST">
                ... Otros campos ... 

                <label for="fecha_inicial">Fecha Inicial:</label>
                <input id="fecha_inicial" type="date" name="fecha_inicial">

                <label for="fecha_final">Fecha Final:</label>
                <input id="fecha_final" type="date" name="fecha_final">

                <label for="filtro_cuenta">Filtrar por Cuenta:</label>
                <select id="filtro_cuenta" name="filtro_cuenta">
                    <option value="">Todas las cuentas</option>
                    <?php
                    // Realiza la consulta para obtener las cuentas del cliente que inició sesión
                    /*$sql_cuentas_usuario = "SELECT cc.idCtasCliente, cc.nombre
                                            FROM ctascliente cc
                                            INNER JOIN cuenta c ON cc.idCtasCliente = c.idCtaCliente
                                            INNER JOIN cliente cl ON c.idCliente = cl.idCliente
                                            WHERE cl.idUsuario = :idUsuario";

                    $stmt_cuentas_usuario = $base_de_datos->prepare($sql_cuentas_usuario);
                    $stmt_cuentas_usuario->bindParam(':idUsuario', $_SESSION['userId'], PDO::PARAM_INT);
                    $stmt_cuentas_usuario->execute();

                    while ($cuenta_usuario = $stmt_cuentas_usuario->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$cuenta_usuario['idCtasCliente']}'>{$cuenta_usuario['nombre']}</option>";
                    }*/
                    ?>
                </select>

                <button id="button" type="submit">Filtrar</button>
            </form>-->
            <h2>Listado de Movimientos</h2>
            <div id="tabla_movimientos" >
            <table border="1">
                <tr>
                    <th>Correlativo</th>
                    <th>Nombre de la Cuenta</th>
                    <th>Monto</th>
                    <th>Tipo de Movimiento</th>
                    <th>Descripción</th>
                    <th>Fecha de Movimiento</th>
                </tr>
                <?php
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
            </table>
            </div>
        </div>
    </div>
</body>
</html>


