<?php
/*
 *http://www.php.net/manual/en/ref.sockets.php
 */

$host = "127.0.0.1";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$puerto = 10000;

if (socket_connect($socket, $host, $puerto))
{
    echo "\nConexion Exitosa, puerto: " . $puerto;
}
else
{
    echo "\nLa conexion TCP no se pudo realizar, puerto: ".$puerto;
}

$msg = "\nBienvenido al Servidor De Prueba de PHP. \n"."Para salir, escriba 'quit'. Para cerrar el servidor escriba 'shutdown'.\n";

socket_write($socket, $msg);


socket_close($socket);
?>