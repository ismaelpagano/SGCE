<?php

    include 'funcs.php';

    $_SESSION['no_vistos'] = Array();
    $_SESSION['vistos'] = Array();
    $_SESSION['guardados'] = Array();
    $_SESSION['ofertados'] = Array();
    $_SESSION['en_proceso'] = Array();
    $_SESSION['rechazados'] = Array();
    $_SESSION['cerrados'] = Array();

    $fecha = date('Y-m-d H:i:s');

    $sql = sql_con(DATABASE_COMPRAS);

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 0) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['no_vistos'][$r->id_compra] = new Compras($r);
        }
    }

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 1) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['vistos'][$r->id_compra] = new Compras($r);;
        }
    }

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 2) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['guardados'][$r->id_compra] = new Compras($r);;
        }
    }

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 3) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['ofertados'][$r->id_compra] = new Compras($r);;
        }
    }

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 5) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['en_proceso'][$r->id_compra] = new Compras($r);;
        }
    }

    $q = $sql->query("SELECT id_compra FROM compras WHERE id_compra IN (SELECT id_compra FROM gestion_compras WHERE estado_interno = 4) AND fecha_publicacion > '2022-06-01 00:00:00' AND estado_compra = 4 AND fecha_hora_tope_entrega > '".$fecha."'");

    if($q){
        while($r = $q->fetch_object()){
            $_SESSION['rechazados'][$r->id_compra] = new Compras($r);;
        }
    }

?>
