<?php

    function actualizar_catalogo(){

        $archivos_bd = Array(
            "bd/catalogo/ART_ATRIBUTO.sql",
            "bd/catalogo/ART_COLOR.sql",
            "bd/catalogo/ART_IMPUESTO.sql",
            "bd/catalogo/ART_SERV_OBRA.sql",
            "bd/catalogo/ART_UNIDAD_MED.sql",
            "bd/catalogo/CLASE.sql",
            "bd/catalogo/COLOR.sql",
            "bd/catalogo/DET_VARIANTE.sql",
            "bd/catalogo/FAMILIA.sql",
            "bd/catalogo/IMPUESTO.sql",
            "bd/catalogo/MARCA.sql",
            "bd/catalogo/MED_VARIANTE.sql",
            "bd/catalogo/MEDIDA.sql",
            "bd/catalogo/ODG.sql",
            "bd/catalogo/PORC_IMPUESTO.sql",
            "bd/catalogo/PRESENTACION.sql",
            "bd/catalogo/PROP_UNIDAD_MED.sql",
            "bd/catalogo/PROPIEDAD.sql",
            "bd/catalogo/SINONIMO.sql",
            "bd/catalogo/SUBCLASE.sql",
            "bd/catalogo/SUBFAMILIA.sql",
            "bd/catalogo/UNIDAD_MED.sql"
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

?>