<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../php/conexionBD.php';

$usuarioID = intval($_SESSION['id']);
$errores = [];

if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listaTareas.php");
    exit();
}

$taskId = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tareaNombre = trim($_POST['tareaNombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado_id = intval($_POST['estado_id'] ?? 0);
    $id = intval($_POST['id']);

    if ($tareaNombre === '' || mb_strlen($tareaNombre) > 150) {
        $errores[] = "El nombre es obligatorio y debe tener máximo 150 caracteres.";
    }
    if (mb_strlen($descripcion) > 2000) {
        $errores[] = "La descripción no puede exceder 2000 caracteres.";
    }
    if ($estado_id <= 0) $errores[] = "Seleccione un estado válido.";

    $mysqli = abrirConexion();
    $stmt = $mysqli->prepare("SELECT usuarioID, urlImagen FROM tarea_usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if (!$res || intval($res['usuarioID']) !== $usuarioID) {
        cerrarConexion($mysqli);
        $errores[] = "Tarea no encontrada o no tiene permisos para editar.";
    } else {
        $urlImagen = $res['urlImagen'];
    }

    if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] !== 4) {
        $f = $_FILES['imagen'];
        if ($f['error'] !== 0) {
            $errores[] = "Error al subir la imagen.";
        } else {
            $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            if (!in_array($f['type'], $allowed)) {
                $errores[] = "Formato de imagen no permitido.";
            } elseif ($f['size'] > 3 * 1024 * 1024) {
                $errores[] = "La imagen no puede pesar más de 3MB.";
            } else {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                try {
                    $newName = 'uploads/tarea_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                } catch (Exception $e) {
                    $newName = 'uploads/tarea_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                }
                $destination = __DIR__ . '/../' . $newName;
                if (!move_uploaded_file($f['tmp_name'], $destination)) {
                    $errores[] = "No se pudo guardar la imagen en el servidor.";
                } else {
                    if (!empty($urlImagen) && file_exists(__DIR__ . '/../' . $urlImagen)) {
                        @unlink(__DIR__ . '/../' . $urlImagen);
                    }
                    $urlImagen = $newName;
                }
            }
        }
    }

    if (empty($errores)) {
        $sql = "UPDATE tarea_usuario SET tareaNombre = ?, descripcion = ?, estado_id = ?, urlImagen = ? WHERE id = ? AND usuarioID = ?";
        $stmt2 = $mysqli->prepare($sql);
        if (!$stmt2) {
            $errores[] = "Error en la preparación de la consulta: " . $mysqli->error;
            cerrarConexion($mysqli);
        } else {
            $stmt2->bind_param("ssissi", $tareaNombre, $descripcion, $estado_id, $urlImagen, $id, $usuarioID);
            if ($stmt2->execute()) {
                cerrarConexion($mysqli);
                header("Location: listaTareas.php");
                exit();
            } else {
                $errores[] = "Error al actualizar: " . $mysqli->error;
                cerrarConexion($mysqli);
            }
        }
    } else {
        if (isset($mysqli)) cerrarConexion($mysqli);
    }
}

$mysqli = abrirConexion();
$stmt = $mysqli->prepare("SELECT id, tareaNombre, descripcion, estado_id, urlImagen, usuarioID FROM tarea_usuario WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task || intval($task['usuarioID']) !== $usuarioID) {
    cerrarConexion($mysqli);
    header("Location: listaTareas.php");
    exit();
}

$estados = $mysqli->query("SELECT id, nombre FROM estado_tarea ORDER BY id ASC");
cerrarConexion($mysqli);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include_once('../navbar.php'); ?>

<div class="container my-4">
    <h3>Editar Tarea</h3>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errores as $err) echo "<div>" . htmlspecialchars($err) . "</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="frmEditar" novalidate>
        <input type="hidden" name="id" value="<?= intval($task['id']) ?>">
        <div class="mb-3">
            <label class="form-label">Nombre de la tarea</label>
            <input type="text" name="tareaNombre" id="tareaNombre" class="form-control" maxlength="150" value="<?= htmlspecialchars($task['tareaNombre']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" maxlength="2000"><?= htmlspecialchars($task['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado_id" id="estado_id" class="form-select">
                <option value="0">-- Seleccione --</option>
                <?php while ($row = $estados->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= ($task['estado_id'] == $row['id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <?php if (!empty($task['urlImagen'])): ?>
            <div class="mb-3">
                <label class="form-label">Imagen actual</label><br>
                <img src="<?= htmlspecialchars($task['urlImagen']) ?>" style="max-width:150px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Cambiar imagen (opcional)</label>
            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar tarea</button>
        <a href="listaTareas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.getElementById('frmEditar').addEventListener('submit', function(e) {
    let nombre = document.getElementById('tareaNombre').value.trim();
    let descripcion = document.getElementById('descripcion').value.trim();
    let estado = document.getElementById('estado_id').value;

    if (nombre === '' || nombre.length > 150) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Nombre inválido', text: 'El nombre es obligatorio y debe tener máximo 150 caracteres.'});
        return;
    }
    if (descripcion.length > 2000) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Descripción inválida', text: 'La descripción no puede exceder 2000 caracteres.'});
        return;
    }
    if (parseInt(estado) <= 0) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Estado inválido', text: 'Debe seleccionar un estado.'});
        return;
    }
});
</script>
</body>
</html>
