<?php

//Punto 2 - C
/*editaTarea.php: Edita la tarea seleccionada. Los campos del formulario deben 
ser los mismos al del formulario agregatarea.php, con diferencia de inicio de 
formulario, ya que este debe consultar la tarea seleccionada y mostrar los 
datos en los inputs correspondientes.*/

session_start();
include "../conexion.php";

// validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

//tarea que se va a editar y catch
if (!isset($_GET['id'])) {
    echo "Error: No se seleccionó ninguna tarea.";
    exit();
}

$tarea_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

//consulta tarea segun user que esta log
$sql = "SELECT * FROM tareaUsuario WHERE Id = $tarea_id AND UsuarioID = $usuario_id";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) == 0) {
    echo "No tienes permiso para editar esta tarea.";
    exit();
}

$tarea = mysqli_fetch_assoc($resultado);

// estados
$sqlEstados = "SELECT * FROM estados";
$estadosResultado = mysqli_query($conexion, $sqlEstados);

//form enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['TareaNombre']);
    $descripcion = trim($_POST['Descripcion']);
    $estadoID = intval($_POST['EstadoID']);
    $urlImagen = trim($_POST['urlImagen']);
    $fechaActualizacion = date('Y-m-d');

    //Validaciones de php NO html 5

    if (strlen($nombre) > 100) {
        echo "<script>alert('El nombre excede el máximo permitido (100 caracteres)');</script>";
    } elseif (strlen($descripcion) > 500) {
        echo "<script>alert('La descripción excede el máximo permitido (500 caracteres)');</script>";
    } elseif (strlen($urlImagen) > 500) {
        echo "<script>alert('La URL excede el máximo permitido (500 caracteres)');</script>";
    } else {

        // Actualizar
        $updateSQL = "UPDATE tareaUsuario 
                      SET TareaNombre='$nombre',
                          Descripcion='$descripcion',
                          EstadoID=$estadoID,
                          FechaActualizacion='$fechaActualizacion',
                          urlImagen='$urlImagen'
                      WHERE Id=$tarea_id";
        if (mysqli_query($conexion, $updateSQL)) {
            header("Location: listaTareas.php");
            exit();
        } else {
            echo "Error al actualizar la tarea.";
        }
    }
}
?>