<?php

    include 'funcs/funcs.php';

    $sql = sql_con();

    $q = "SELECT id_compra , estado_compra FROM gestor_compras_estatales_sandbox.compras";

    $q = $sql->query($q);

    $compras = Array();
    
    if($q){

        while($r = $q->fetch_object()){

            $compra = Array();
            $compra['id'] = $r->id_compra;
            $compra['estado'] = $r->estado_compra;
            $compras[$r->id_compra] = $compra;

        }

    }

    foreach($compras as $compra){
        
        $q = "UPDATE gestion_bd_sandbox.gestion_compras SET estado_compra = ".$compra['estado']." WHERE id_compra = '".$compra['id']."'";

        $q = $sql->query($q);

    }


?>
