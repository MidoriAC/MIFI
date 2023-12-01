<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../public/css/styleLogin.css">
    <link rel="short cut icon" href="../public/icons/iconomarca.ico">
    <title>Login</title>
</head>
<body>
    <div class="ocean">
        <div class="wave"></div>
        <div class="wave"></div>
    </div>

    <!--Seccion de los formularios del Login-->
    <div class="conteiner" id="conteiner">
        <div class="form-container sign-up-container">
            <form action="../controller/registro.php" method="POST">
                <h1>Registrate</h1>
                <label for="">
                    <input type="text" id="username" name="username" placeholder="Usuario">
                </label>
                <label for="">
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre">
                </label>
                <label for="">
                    <input type="email" id="email" name="email" placeholder="Email">
                </label>
                <label for="">
                    <input type="password" id="password" name="password" placeholder="Contraseña">
                </label>
                <button style="margin-top: 9px">Registrate</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="../controller/login.php" method="POST">
                <h1>Inicia Sesión</h1>
                <label for="">
                    <input type="text" id="username" name="username" placeholder="Usuario">
                </label>
                <label for="">
                    <input type="password" id="password" name="password" placeholder="Contraseña">
                </label>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button>Inicia sesión</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Login</h1>
                    <p>Inicia sesión para ingresar a tu cuenta...</p>
                    <button class="ghost mt-S" id="signIn">Inicia Sesión</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Crea un cuenta</h1>
                    <p>Registrate si no tienes una cuenta</p>
                    <button class="ghost" id="signUp">Registrate</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../views/js/script.js"></script>
</body>
</html>