<?php

    include "clases.php";

    $host = "127.0.0.1";
    $port = 5353;
    // No Timeout 
    set_time_limit(0);

    $message = 'hola';

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

    socket_write($socket, $message) or die("Could not send data to server\n");

    $result = socket_read($socket, 1024) or die("Could not read server response\n");
    
    echo "Reply From Server : ".$result;

    socket_close($socket);

?>