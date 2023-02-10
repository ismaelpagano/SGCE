<?php
    
    include 'funcs.php';

    $id_compra = $_POST['id'];
    $estado_compra = $_POST['estado'];

    $compra = $_SESSION['sistema']->seleccion_llamados[$id_compra];

    $compra->actualizar_estado_compra($estado_compra);

    $estructura = botones_compra($id_compra, false);

    echo $estructura;

?>