<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="login-card">
    <h3>Iniciar Sesión</h3>
    <form id="loginForm" action="">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" name="usuario" id="usuario">
        </div>
        <div class="mb-3">
            <label for="contrasenna" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contrasenna" id="contrasenna">
        </div>
        <button type="submit" class="btn btn-login">Ingresar</button>
        <div class="enlaces mt-3">
            <p><a href="php/registro/registro.php">Crear cuenta nueva</a></p>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="php/login/login.js"></script>
</body>
</html>
