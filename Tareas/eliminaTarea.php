<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../index.php"); exit();
}

if (!isset($_GET['id'])) {
    header("Location: listaTareas.php"); exit();
}

include_once '../php/conexionBD.php';
$id = intval($_GET['id']);
$uid = intval($_SESSION['id']);

$cn = abrirConexion();

$stmt = $cn->prepare("SELECT urlImagen, usuarioID FROM tarea_usuario WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t || $t["usuarioID"] != $uid) {
    cerrarConexion($cn);
    header("Location: listaTareas.php");
    exit();
}

if (!empty($t["urlImagen"]) && file_exists(__DIR__."/../".$t["urlImagen"])) {
    unlink(__DIR__."/../".$t["urlImagen"]);
}

$del = $cn->prepare("DELETE FROM tarea_usuario WHERE id=? AND usuarioID=?");
$del->bind_param("ii", $id, $uid);
$del->execute();

cerrarConexion($cn);

header("Location: listaTareas.php");
exit();
