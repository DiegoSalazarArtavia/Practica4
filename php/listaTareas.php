<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
session_start();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $conexion = openConection();
    $sql = "SELECT   t.Id,
                t.TareaNombre,
                t.Descripcion,
                t.FechaCreacion,
                t.FechaActualizacion,
                t.urlImagen,
                e.nombre AS EstadoNombre,
                u.usuario AS UsuarioNombre
            FROM tareaUsuario t
            INNER JOIN estados e ON t.EstadoID = e.id
            INNER JOIN usuarios u ON t.UsuarioID = u.id
            ORDER BY t.FechaCreacion DESC";

    $stmt = $conexion->prepare($sql);
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $tareas = [];
           while ($row = $resultado->fetch_assoc()) {
            $tareas[] = $row;
            
        echo json_encode([
            "status" => "ok",
            "tareas" => $tareas
        ]);
        echo json_encode([
            "status" => "error",
            "mensaje" => "Error en la consulta",
            "detalle" => $conexion->error
        ]);
    }

    $stmt->close();
    closeConection($conexion);
}
}
?>
