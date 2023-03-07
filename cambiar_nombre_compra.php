<?php

    include 'funcs/funcs.php';

    $hash = $_POST['objeto'];

    $objeto = $_SESSION['sistema']->variables[$hash];

    $id_compra = $objeto->compra->id_compra;

    $nombre = $_POST['nombre'];

    echo $_SESSION['sistema']->seleccion_llamados[$id_compra]->cambiar_nombre($nombre);

?>