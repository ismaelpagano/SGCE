<?php

    include 'funcs/funcs.php';

    $hash = $_POST['objeto'];

    $objeto = $_SESSION['sistema']->variables[$hash];

    $id_compra = $objeto->compra->id_compra;

    $nombre = $_POST['nombre'];

    if(isset($_POST['checkbox'])){

        $_SESSION['sistema']->seleccion_llamados[$id_compra]->actualizar_estado_compra('2'); 

    }

    echo $_SESSION['sistema']->seleccion_llamados[$id_compra]->cambiar_nombre($nombre);

?>