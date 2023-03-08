<?php

    function actualizar_catalogo(){

        echo "empieza el script";

        $archivos_bd = Array(
            //"catalogo/ART_ATRIBUTO.sql",
            //"catalogo/ART_COLOR.sql",
            //"catalogo/ART_IMPUESTO.sql",
            // "catalogo/ART_SERV_OBRA.sql",
            // "catalogo/ART_UNIDAD_MED.sql",
            "catalogo/CLASE.sql",
            // "catalogo/COLOR.sql",
            // "catalogo/DET_VARIANTE.sql",
            "catalogo/FAMILIA.sql",
            // "catalogo/IMPUESTO.sql",
            // "catalogo/MARCA.sql",
            // "catalogo/MED_VARIANTE.sql",
            // "catalogo/MEDIDA.sql",
            // "catalogo/ODG.sql",
            // "catalogo/PORC_IMPUESTO.sql",
            // "catalogo/PRESENTACION.sql",
            // "catalogo/PROP_UNIDAD_MED.sql",
            // "catalogo/PROPIEDAD.sql",
            // "catalogo/SINONIMO.sql",
            "catalogo/SUBCLASE.sql",
            "catalogo/SUBFAMILIA.sql",
            // "catalogo/UNIDAD_MED.sql"
        );
    
    
        foreach($archivos_bd as $url){
    
            $sql = file_get_contents($url);
    
            try {           
                $con_pdo = new PDO('mysql:host=localhost;dbname=catalogo_arce', 'root', '');
                $con_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $query = $con_pdo->exec($sql);
            } catch (PDOException $e) {
                echo 'Falló la conexión: ' . $e->getMessage().'<br>';
            }
    
        }

    }

    actualizar_catalogo();

?>