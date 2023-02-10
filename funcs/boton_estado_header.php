<?php

    include "funcs.php";

    $compra = $_SESSION['compra_actual'];

    $estado_interno = estado_interno($compra->estado_interno);

    if($compra->estado_compra == 4 && strtotime($compra->fecha_hora_tope_entrega) > time() ){
        echo '<div id="estado_interno" class="'.$estado_interno[0].'" onclick=\'opciones_llamado_desplegable("'.$compra->id_compra.'")\'><p id="botones_header_text">'.$estado_interno[1].'</p></div>';
    } else if ( $compra->estado_compra == 19) {
        echo '<div id="estado_interno" class="llamado_anulado"><p id="botones_header_text">Llamado anulado</p></div>';
    }


?>