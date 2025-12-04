<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$response = ['status' => 'error', 'mensaje' => 'Error inesperado'];

try {
    include_once '../conexionBD.php';

    $raw = file_get_contents("php://input");
    $datos = json_decode($raw, true);

    if (!$datos) {
        $response['mensaje'] = 'Los datos no pudieron ser procesados.';
        echo json_encode($response);
        exit();
    }

    $usuario = trim($datos['usuario'] ?? '');
    $clave = trim($datos['contrasenna'] ?? '');

    if (!$usuario || !$clave) {
        $response['mensaje'] = 'Usuario o contraseña vacíos.';
        echo json_encode($response);
        exit();
    }

    $mysqli = abrirConexion();
    $sql = "SELECT id, nombre, usuario, clave FROM usuarios WHERE usuario = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($clave, $fila['clave'])) {
            $_SESSION['id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['usuario'] = $fila['usuario'];

            $response = ['status' => 'ok', 'nombre' => $fila['nombre']];
        } else {
            $response['mensaje'] = 'Contraseña incorrecta';
        }
    } else {
        $response['mensaje'] = 'Usuario no encontrado';
    }

    cerrarConexion($mysqli);
} catch (Exception $e) {
    $response['mensaje'] = 'Sucedió un error al realizar el login: ' . $e->getMessage();
}

echo json_encode($response);
exit();
?>