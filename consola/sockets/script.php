<?php

    include 'funcs/funcs.php';
    include 'clases.php';

    $cliente = new Cliente('wlopez', '14331211');

    // $cliente->llamados();

    // print_r($cliente);
    // echo '<br>';

    // foreach($cliente->llamados->coleccion['OFERTADOS']['COLECCION'] as $compra){
    // }

    // foreach($cliente->llamados->coleccion['GUARDADOS']['COLECCION'] as $compra){
    //     $mensaje = $cliente->llamados->armar_mensaje('ESTADO_INTERNO', $compra->id_compra, $compra->estado_interno, 'OFERTADOS', $cliente->id);
    //     $mensaje = $cliente->llamados->actualizar_compra($mensaje, true);
    //     $mensaje->puerto_origen = $cliente->socket->port;
    //     $mensaje->puerto_destino = 5353;
    //     $cliente->socket->enviar($mensaje);
    // }

?>