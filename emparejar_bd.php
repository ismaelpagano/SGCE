<?php 

    include 'funcs/funcs.php';

    $sql = sql_con('gestor_compras_estatales');

    $q = $sql->query("SELECT * FROM gestion_compras");

    $compras_db1 = Array();


    if($q){

        while($r = $q->fetch_object()){
            $compras_db1[$r->id_compra] = $r->estado_interno ;
        }

    }

    mysqli_close($sql);

    $sql = sql_con('gestion_bd_sandbox');

    $q = $sql->query("SELECT * FROM gestion_compras");

    $compras_db2 = Array();


    if($q){

        while($r = $q->fetch_object()){
            $compras_db2[$r->id_compra] = $r;
        }

    }

    mysqli_close($sql);

    $sql = sql_con('gestion_bd_sandbox');

    foreach($compras_db2 as $nuevo){

        if(isset($compras_db1[$nuevo->id_compra])){

            $estado = $compras_db1[$nuevo->id_compra];

            $q = $sql->query("UPDATE gestion_compras SET estado_interno = '".$estado."' WHERE id_compra = '".$nuevo->id_compra."'");

        }

    }

    mysqli_close($sql);


?>