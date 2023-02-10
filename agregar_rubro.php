<?php

    include 'funcs/funcs.php';

    if(isset($_SESSION['oferta_actual'])){
        $id_oferta = $_SESSION['oferta_actual'];
    }

    $rubros = get_rubros_oferta($id_oferta);
    $cant_rubros = 0;

    if($rubros != NULL){

        $cant_rubros = count($rubros);

    } 

    $nro_item = $cant_rubros + 1;

    if(isset($_POST['nuevo_rubro_desc'])){
        $nuevo_rubro_desc = $_POST['nuevo_rubro_desc'];
    } else {
        $nuevo_rubro_desc = NULL;
    }

    if(isset($_POST['nuevo_rubro_cant'])){
        $nuevo_rubro_cant = $_POST['nuevo_rubro_cant'];
    } else {
        $nuevo_rubro_cant = NULL;
    }

    if(isset($_POST['nuevo_rubro_magn'])){
        $nuevo_rubro_magn = $_POST['nuevo_rubro_magn'];
    } else {
        $nuevo_rubro_magn = NULL;
    }

    if(isset($_POST['nuevo_rubro_moneda'])){
        $nuevo_rubro_moneda = $_POST['nuevo_rubro_moneda'];
    } else {
        $nuevo_rubro_moneda = NULL;
    }

    if(isset($_POST['nuevo_rubro_costo'])){
        $nuevo_rubro_costo = $_POST['nuevo_rubro_costo'];
    } else {
        $nuevo_rubro_costo = NULL;
    }

    $rubros = get_rubros_oferta($_SESSION['oferta_actual']);

    $sql = sql_con();

    $q = $sql->query('
        INSERT INTO gestion_bd_sandbox.rubros_oferta 
            ( id_oferta , nro_item , nom_rubro , descripcion , cantidad , magnitud , costo_s_iva , id_moneda )
        VALUES 
            ( "'.$id_oferta.'" , "'.$nro_item.'" , NULL , "'.$nuevo_rubro_desc.'" , "'.$nuevo_rubro_cant.'" , "'.$nuevo_rubro_magn.'" , "'.$nuevo_rubro_costo.'" , "'.$nuevo_rubro_moneda.'" )
        ');

    $rubros = get_rubros_oferta($id_oferta);

    echo show_rubros_oferta($rubros);

?>