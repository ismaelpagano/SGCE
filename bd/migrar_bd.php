<?php

    include '../consola/funcs.php';

    $sql = sql_con('gestion_bd_sandbox');

    $q = $sql->query('SELECT * FROM gestion_compras');

    $compras = Array();

    if($q){

        while($r = $q->fetch_object()){
            $compras[$r->id_compra] = $r;
        }

    }

    



?>
