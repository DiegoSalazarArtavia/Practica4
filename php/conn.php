<?php
function openConection(){
$host = 'localhost';
$user = 'UsuarioDeUno';
$pass = 'ContraseñaDeUno';
$db   = 'tareaDB';


$mysqli = new mysqli($host, $user, $pass, $db);
if($mysqli->connect_errno){
    
}
$mysqli->set_charset("utf8");
return $mysqli;
}
function closeConection($mysqli){
    $mysqli->close();
}
?>