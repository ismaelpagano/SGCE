<?php

    include 'funcs/funcs.php';

    $compra = $_SESSION['compra_actual'];


    if($compra->estado_compra == 4 && strtotime($compra->fecha_hora_tope_entrega) > time()){

        $return = '
            <ul id="opciones_header">';

        if($compra->estado_interno != 2){
            $return .= '
                <li id="guardar_llamado"><p>Guardar llamado</p></li>';
        }
        $return .= '
                <li id="crear_oferta"><p>Crear oferta</p></li>
                <li id="descartar_llamado"><p>Descartar llamado</p></li>
            </ul>';
    }


    echo $return;


?>