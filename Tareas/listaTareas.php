<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

include_once '../php/conexionBD.php';
$mysqli = abrirConexion();
$usuarioID = intval($_SESSION['id']);

$stmt = $mysqli->prepare("
    SELECT t.id, t.tareaNombre, t.descripcion, t.fechaCreacion, t.fechaActualizacion, t.urlImagen, e.nombre AS estado
    FROM tarea_usuario t
    JOIN estado_tarea e ON t.estado_id = e.id
    WHERE t.usuarioID = ?
    ORDER BY t.fechaCreacion DESC
");
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();
cerrarConexion($mysqli);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background:#e9eef5;">

<?php include '../navbar.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Tus Tareas</h3>
        <a href="agregaTarea.php" class="btn btn-success">+ Nueva tarea</a>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th>
                <th>Creada</th><th>Actualizada</th><th>Imagen</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($t = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['tareaNombre']) ?></td>
                <td><?= htmlspecialchars($t['descripcion']) ?></td>
                <td><?= $t['estado'] ?></td>
                <td><?= $t['fechaCreacion'] ?></td>
                <td><?= $t['fechaActualizacion'] ?></td>
                <td>
                    <?php if ($t['urlImagen']): ?>
                        <img src="../<?= $t['urlImagen'] ?>" style="width:80px; height:60px; object-fit:cover;">
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="editaTarea.php?id=<?= $t['id'] ?>">Editar</a>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $t['id'] ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Eliminar tarea',
        text: '¿Seguro que deseas eliminarla?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((r)=>{
        if(r.isConfirmed){
            window.location = 'eliminaTarea.php?id=' + id;
        }
    });
}
</script>

</body>
</html>
