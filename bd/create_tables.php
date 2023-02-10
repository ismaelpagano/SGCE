<?php

    function create_tables(){
       
        $servername = "localhost";
        $username = "root";
        $password = "";

        $bd_catalogo = file_get_contents('bd/scripts_bd/BD_CATALOGO.txt');
        $bd_codigueras = file_get_contents('bd/scripts_bd/BD_CODIGUERAS.txt');
        $bd_gestor_compras_estatales = file_get_contents('bd/scripts_bd/BD_GESTOR_COMPRAS_ESTATALES.txt');
    
        $conn = new mysqli($servername, $username, $password);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $conn->close();

        $conn = new mysqli($servername, $username, $password, 'catalogo_arce');
    
        if ($conn->query($bd_catalogo) === TRUE) {
            echo "Tablas de 'catalogo_arce' creadas satisfactoriamente.<br>";
        } else {
            echo "Error creando tablas de 'catalogo_arce'" . $conn->error.".<br>";
        }

        $conn->close();

        $conn = new mysqli($servername, $username, $password, 'codigueras_arce');

        if ($conn->query($bd_codigueras) === TRUE) {
            echo "Tablas de 'codigueras_arce' creadas satisfactoriamente.<br>";
        } else {
            echo "Error creando tablas de 'codigueras_arce'" . $conn->error.".<br>";
        }

        $conn->close();

        
        $conn = new mysqli($servername, $username, $password, 'gestor_compras_estatales');
    
        if ($conn->query($bd_gestor_compras_estatales) === TRUE) {
            echo "Tablas de 'gestor_compras_estatales' creadas satisfactoriamente.<br>";
        } else {
            echo "Error creando tablas de 'gestor_compras_estatales'" . $conn->error.".<br>";
        }
    
        $conn->close();

    }

?>