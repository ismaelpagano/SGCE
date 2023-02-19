<?php

    include 'funcs/funcs.php';

    $sql = sql_con();

    $q = 'SELECT gestion.id_compra , compra.anio_compra , compra.fecha_publicacion , gestion.fecha_ult_mod , compra.estado_compra , gestion.estado_interno FROM gestion_bd_sandbox.gestion_compras as gestion INNER JOIN gestor_compras_estatales_sandbox.compras as compra ON gestion.id_compra = compra.id_compra WHERE anio_compra = 2023';

    $q = $sql->query($q);

    $compras = Array();

    if($q){

        while($r = $q->fetch_object()){

            $compras[$r->id_compra] = $r;

        }

    }

    $cache = '';

    foreach($compras as $compra){

        $fecha = date('Y-m-d H:i:s');

        $q = "INSERT INTO gestion_bd.gestion_compras ( id_compra , anio_compra , fecha_publicacion , fecha_ult_mod_arce , fecha_ult_mod_sgce , estado_arce , estado_interno ) VALUES ( '".$compra->id_compra."' , '".$compra->anio_compra."' , '".$compra->fecha_publicacion."' , '".$compra->fecha_ult_mod."' , '".$fecha."' , ".$compra->estado_compra." , ".$compra->estado_interno." );\n";

        // $cache .= $q;
        
        $q = $sql->query($q);

    }

    // $file = fopen('migrar.txt', 'w');

    // $fwrite = fwrite($file, $cache);

    mysqli_close($sql);

?>