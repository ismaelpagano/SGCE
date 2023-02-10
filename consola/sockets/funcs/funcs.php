<?php

    include 'clases.php';

    session_start();

    function sql_con($db = ''){

        $sql = new mysqli('localhost', 'root', '', $db);
        $sql->set_charset('utf8');

        return $sql;
    }

    function socket_server($host, $port, $envio){

        set_time_limit(0);

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    
        $result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
    
        $result = socket_listen($socket, 1) or die("Could not set up socket listener\n");
    
        $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
    
        $input = socket_read($spawn, 1024) or die("Could not read input\n");

        socket_write($spawn, $envio) or die("Could not send data to server\n");

        socket_close($spawn);
        socket_close($socket);

    }

    function socket_query($host, $port, $envio){

        set_time_limit(0);

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

        socket_write($socket, $envio, strlen ($envio)) or die("Could not send data to server\n");

        socket_close($socket);

    }

    function enviar_solicitud($solicitud){

        $solicitud_size = json_encode($solicitud);

        if(strlen($solicitud_size) <= 1024){

            $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        
            $result = socket_connect($socket, $solicitud->host, $solicitud->port) or die("Could not connect to server\n");
    
            socket_write($socket, $solicitud, $solicitud_size) or die("Could not send data to server\n");
    
            socket_close($socket);

        }

    }



?>