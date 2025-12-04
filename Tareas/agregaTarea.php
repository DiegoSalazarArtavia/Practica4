<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../php/conexionBD.php';

$errores = [];
$tareaNombre = '';
$descripcion = '';
$estado_id = 0;
$urlImagen = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tareaNombre = trim($_POST['tareaNombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado_id = intval($_POST['estado_id'] ?? 0);
    $usuarioID = intval($_SESSION['id']);

//Validaciones
    if ($tareaNombre === '' || mb_strlen($tareaNombre) > 150)
        $errores[] = "El nombre de la tarea es obligatorio y debe tener máximo 150 caracteres.";

    if (mb_strlen($descripcion) > 2000)
        $errores[] = "La descripción excede 2000 caracteres.";

    if ($estado_id <= 0)
        $errores[] = "Debe seleccionar un estado.";

    if (!empty($_FILES['imagen']['name'])) {

        $f = $_FILES['imagen'];

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($f['type'], $allowed)) {
            $errores[] = "Formato inválido.";
        } else {
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $name = 'uploads/tarea_' . time() . '_' . rand(1000, 9999) . "." . $ext;
            $dest = __DIR__ . "/../" . $name;

            if (move_uploaded_file($f['tmp_name'], $dest)) {
                $urlImagen = $name;
            } else {
                $errores[] = "Error al guardar imagen.";
            }
        }
    }

    if (empty($errores)) {
        $cn = abrirConexion();
        $stmt = $cn->prepare("
            INSERT INTO tarea_usuario (tareaNombre, descripcion, estado_id, usuarioID, urlImagen)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssiss", $tareaNombre, $descripcion, $estado_id, $usuarioID, $urlImagen);

        if ($stmt->execute()) {
            cerrarConexion($cn);
            header("Location: listaTareas.php");
            exit();
        } else {
            $errores[] = "Error al insertar: " . $cn->error;
        }

        cerrarConexion($cn);
    }
}

$cn = abrirConexion();
$estados = $cn->query("SELECT id, nombre FROM estado_tarea");
cerrarConexion($cn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Tarea</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="background:#e9eef5;">

    <?php include '../navbar.php'; ?>

    <div class="container my-4">

        <h3 style="color:#333333;">Agregar Nueva Tarea</h3>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="mt-3">

            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="tareaNombre" maxlength="150"
                value="<?= htmlspecialchars($tareaNombre) ?>">

            <label class="form-label mt-3">Descripción</label>
            <textarea class="form-control" name="descripcion"
                maxlength="2000"><?= htmlspecialchars($descripcion) ?></textarea>

            <label class="form-label mt-3">Estado</label>
            <select class="form-select" name="estado_id">
                <option value="0">-- Seleccione --</option>
                <?php while ($e = $estados->fetch_assoc()): ?>
                    <option value="<?= $e['id'] ?>" <?= ($estado_id == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="form-label mt-3">Imagen (opcional)</label>
            <input type="file" class="form-control" name="imagen">

            <button class="btn btn-primary mt-4">Crear tarea</button>
            <a href="listaTareas.php" class="btn btn-secondary mt-4">Cancelar</a>

        </form>

    </div>

</body>

</html>