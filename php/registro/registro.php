<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../conexionBD.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nombre = $_POST["nombre"] ??  '';
    $correo = $_POST["correo"] ??  '';
    $usuario = $_POST["usuario"] ??  '';
    $clave = $_POST["clave"] ??  '';
    $confirmar = $_POST["confirmar"] ??  '';
    $fecha = $_POST["fecha"] ??  '';
    $genero = $_POST["genero"] ??  '';

    if (!$nombre || !$usuario || !$clave || !$confirmar || !$fecha || !$genero) {
        echo "error:Debe completar todos los campos.";
        exit();
    }

    if ($clave !== $confirmar) {
        echo "error:Las contraseñas no coinciden.";
        exit();
    }

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    $conexion = abrirConexion();

    $sql = "INSERT INTO usuarios (nombre, correo, usuario, clave, fecha_nacimiento, genero) VALUES(?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $correo, $usuario, $claveHash, $fecha, $genero);

    if($stmt->execute()){
        echo "ok";
    }else{
        echo "error: ".$conexion->error;
    }

    $stmt->close();
    cerrarConexion($conexion);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="p-4">

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4">Crear Cuenta Nueva</h3>

    <form id="frmRegistro">
        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" id="correo" name="correo" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" id="usuario" name="usuario" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" id="fecha" name="fecha" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Género</label><br>
            <input type="radio" name="genero" value="Masculino"> Masculino
            <input type="radio" name="genero" value="Femenino" class="ms-3"> Femenino
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" id="clave" name="clave" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" id="confirmar" name="confirmar" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>

        <p class="mt-3 text-center">
            <a href="../../index.php">Volver al login</a>
        </p>
    </form>
</div>

<script src="registro.js"></script>

</body>
</html>
