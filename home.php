<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "php/conexionBD.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>

    <link rel="stylesheet" href="assets/css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#e9eef5; }
        h3, h4, h5, p, span, a, td, th { color:#333333; }
    </style>
</head>

<body>

<?php include "navbar.php"; ?>

<div class="container my-5">

    <h3>Hola <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']) ?></h3>

    <section class="mt-4">
        <h4>Mis Tareas</h4>

        <?php
        $cn = abrirConexion();
        $uid = intval($_SESSION['id']);

        $stmt = $cn->prepare("
            SELECT t.id, t.tareaNombre, t.descripcion, t.urlImagen, e.nombre AS estado
            FROM tarea_usuario t
            JOIN estado_tarea e ON t.estado_id = e.id
            WHERE t.usuarioID = ?
            ORDER BY t.fechaCreacion DESC
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        ?>

        <div class="row g-3 mt-3">

            <?php if ($res->num_rows === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No tienes tareas aún.
                        <a href="Tareas/agregaTarea.php">Crear una</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php while ($t = $res->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">

                        <?php if ($t['urlImagen']): ?>
                            <img src="<?= htmlspecialchars($t['urlImagen']) ?>"
                                 class="card-img-top"
                                 style="height:160px; object-fit:cover;">
                        <?php endif; ?>

                        <div class="card-body">

                            <h5 class="card-title">
                                <?= htmlspecialchars(mb_strimwidth($t['tareaNombre'], 0, 50, "...")) ?>
                            </h5>

                            <p class="card-text">
                                <?= htmlspecialchars(mb_strimwidth($t['descripcion'], 0, 150, "...")) ?>
                            </p>

                            <p class="text-muted"><?= htmlspecialchars($t['estado']) ?></p>

                            <a href="Tareas/editaTarea.php?id=<?= $t['id'] ?>" 
                               class="btn btn-secondary btn-sm">
                                Editar
                            </a>

                            <a href="Tareas/listaTareas.php" 
                               class="btn btn-primary btn-sm">
                                Ver lista
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>

        </div>

        <?php cerrarConexion($cn); ?>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
