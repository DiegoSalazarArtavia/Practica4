<?php
//Documents\Github\projects\Practica4\php
//Punto 2 - C
/*editaTarea.php: Edita la tarea seleccionada. Los campos del formulario deben 
ser los mismos al del formulario agregatarea.php, con diferencia de inicio de 
formulario, ya que este debe consultar la tarea seleccionada y mostrar los 
datos en los inputs correspondientes.*/


//UPDATE: HTML en la Linea 82

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

    <?php include "../navbar.php"; ?>

    <div class="container mt-4">
        <h3>Editar Tarea</h3>

        <form method="POST" class="mt-3">

            <label>Nombre de la tarea</label>
            <input type="text" name="TareaNombre" class="form-control"
                value="<?php echo $tarea['TareaNombre']; ?>">
            <label class="mt-3">Descripción</label>
            <textarea name="Descripcion" class="form-control" rows="4"><?php echo $tarea['Descripcion']; ?></textarea>
            <label class="mt-3">Estado</label>
            <select name="EstadoID" class="form-select">
                <?php while ($estado = mysqli_fetch_assoc($estadosResultado)) { ?>
                    <option value="<?php echo $estado['id']; ?>"
                        <?php echo ($estado['id'] == $tarea['EstadoID']) ? "selected" : ""; ?>>
                        <?php echo $estado['nombre']; ?>
                    </option>
                <?php } ?>
            </select>
            <label class="mt-3">URL de imagen</label>
            <input type="text" name="urlImagen" class="form-control"
                value="<?php echo $tarea['urlImagen']; ?>">
            <button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
            <a href="listaTareas.php" class="btn btn-secondary mt-4">Cancelar</a>

        </form>
    </div>

</body>

</html>