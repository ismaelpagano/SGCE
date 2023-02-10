<?php

    include 'funcs.php';
 
    $_SESSION['seleccion'] = Array();

    function seleccion($estado){

        $fecha_actual = fecha_hora_actual();

        $sql = new mysqli('localhost', 'root', '', '');

        $fecha = fecha_actual().' 00:00:00';

        $fecha = date("Y-m-d H:i", strtotime($fecha." - 604800 SECONDS"));

        $fechaActual = fecha_hora_actual();

        if($estado == 'busqueda'){

            $busqueda = new Busqueda();
    
            foreach($_SESSION['seleccion'] as $item){
                $_SESSION['busqueda'][] = $item->id_compra;
            }
    
        } else if ($estado == 'cerrado'){
            $q = "
            SELECT * 
            FROM gestion_bd_sandbox.gestion_compras as gestion INNER JOIN gestor_compras_estatales_sandbox.compras as compras
            ON gestion.id_compra = compras.id_compra 
            WHERE compras.fecha_hora_tope_entrega < '".$fechaActual."'
            ORDER BY compras.fecha_hora_tope_entrega ASC";

            echo $q;

            $q = $sql->query($q);

            if($q){
                while($r = $q->fetch_object()){
                    $compra = new Compras($r);
                    $_SESSION['seleccion'][$compra->id_compra] = $compra;    
                }
            }
        
        } else {

            //VOY A ELEGIR LOS LLAMADOS DE LA BASE DE DATOS QUE TENGAN UNA FECHA DE APERTURA MAYOR A LA fecha actual
            //DESPUÉS DE ESOS VOY A ELEGIR LOS QUE EN GESTIÓN INTERNA TENGAN UN INTERÉS

            if($estado < 2){
                $query_interno = "( gestion.estado_interno = 0 OR gestion.estado_interno = 1 )"; 
            } else {
                $query_interno = "( gestion.estado_interno = ".$estado." )";
            }

            $q = "
                SELECT * 
                FROM gestion_bd_sandbox.gestion_compras as gestion 
                INNER JOIN gestor_compras_estatales_sandbox.compras as compras
                ON gestion.id_compra = compras.id_compra 
                WHERE ".$query_interno." 
                AND fecha_hora_tope_entrega > '".$fecha_actual."'
                AND compras.estado_compra != '19'
                ORDER BY compras.fecha_hora_tope_entrega ASC
            ";

            echo $q.'<br>';

            $q = $sql->query($q);

            if($q){
                while($r = $q->fetch_object()){
                    $compra = new Compras($r);
                    $_SESSION['seleccion'][$compra->id_compra] = $compra;
                }
            }
        }  

        mysqli_close($sql);
    }

    if(isset($_POST['estado'])){
        $estado = $_POST['estado'];
    } else if(isset($_POST['estado_interno'])) {
        $estado = $_POST['estado_interno'];
    } else {
        $estado = 0;
    }
    
    seleccion($estado);
?>