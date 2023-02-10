<?php

    include 'funcs.php';
    include '../mods/visor_llamado.php';

    if(isset($_POST['estado'])){
        $estado_llamado = $_POST['estado'];
    } else {
        $estado_llamado = 'vigente';
    }
    
    if(isset($_POST['estado_interno'])){
        $estado_interno = intval($_POST['estado_interno']);
    } else {
        $estado_interno = 0;
    }

    if(isset($_POST['pagina'])){
        $pagina = intval($_POST['pagina']);
    } else {
        $pagina = 0;
    }

    $_SESSION['seleccion'] = Array();

    if(isset($_POST['busqueda'])){
        $_SESSION['seleccion'] = $_SESSION['busqueda'];
    } else {

        if($estado_interno == 0){
            $_SESSION['seleccion'] = $_SESSION['no_vistos'];
        } else if ($estado_interno == 1){
            $_SESSION['seleccion'] = $_SESSION['vistos'];
        } else if ($estado_interno == 2){
            $_SESSION['seleccion'] = $_SESSION['guardados'];
        } else if ($estado_interno == 3){
            $_SESSION['seleccion'] = $_SESSION['ofertados'];
        } else if ($estado_interno == 4){
            $_SESSION['seleccion'] = $_SESSION['rechazados'];
        } else if ($estado_interno == 5){
            $_SESSION['seleccion'] = $_SESSION['en_proceso'];
        } else {
            $_SESSION['seleccion'] = $_SESSION['no_vistos'];
        }

    }
    
    //$cant_paginas = count($seleccion) / $resultados_por_pagina;

    //echo $cant_paginas.'<br>';

    $resultados_por_pagina = 25;
    
    $resultados = count($_SESSION['seleccion']);

    $num_paginas = $resultados / $resultados_por_pagina;


    foreach($_SESSION['seleccion'] as $compra){
        visor_llamado($compra);
    }


?>
