<?php
function abrirConexion(){
    $host = "127.0.0.1";
    $user = "root";
    $password = ""; 
    $db = "BD_AWCS_III25";

    $mysqli = new mysqli($host, $user, $password, $db);

    if($mysqli->connect_errno){
        throw new Exception("Error de conexión: " . $mysqli->connect_error);
    }

    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}

function cerrarConexion($mysqli){
    $mysqli->close();
}
?>
