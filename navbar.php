<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base === '/') $base = ''; 

$base = preg_replace('#/Tareas$#', '', $base);
$base = preg_replace('#/php/login$#', '', $base);
$base = preg_replace('#/php/registro$#', '', $base);
?>
<nav class="navbar navbar-expand-lg" style="background:#0d6efd;">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="<?= $base ?>/home.php">Administrador de Tareas</a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navMain" class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav gap-3">
                <li class="nav-item">
                    <a href="<?= $base ?>/home.php" class="nav-link text-white">Inicio</a>
                </li>
                <li class="nav-item">
                    <a href="<?= $base ?>/Tareas/listaTareas.php" class="nav-link" style="color:#0dcaf0; font-weight:bold;">Tareas</a>
                </li>
                <li class="nav-item">
                    <a href="<?= $base ?>/php/login/logout.php" class="nav-link text-white">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
