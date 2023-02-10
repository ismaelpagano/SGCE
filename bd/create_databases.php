<?php

    function crear_dbs(){

        $servername = "localhost";
        $username = "root";
        $password = "";
    
        // Create connection
        $conn = new mysqli($servername, $username, $password);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Create database
        $sql = "CREATE DATABASE gestor_compras_estatales";
        if ($conn->query($sql) === TRUE) {
            echo "Database 'gestor_compras_estatales' created successfully<br>";
        } else {
            echo "Error creating database 'gestor_compras_estatales': " . $conn->error."<br>";
        }
    
        $sql = "CREATE DATABASE codigueras_arce";
        if ($conn->query($sql) === TRUE) {
            echo "Database 'codigueras_arce' created successfully<br>";
        } else {
            echo "Error creating database 'codigueras_arce': " . $conn->error."<br>";
        }
    
        $sql = "CREATE DATABASE catalogo_arce";
        if ($conn->query($sql) === TRUE) {
            echo "Database 'catalogo_arce' created successfully<br>";
        } else {
            echo "Error creating database 'catalogo_arce': " . $conn->error."<br>";
        }
    
        $conn->close();

    }

?>