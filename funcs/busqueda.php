<?php

    include 'funcs.php';
    $clave = NULL;

    if(isset($_POST['busqueda'])){
        $clave = $_POST['busqueda'];
    }

    $_SESSION['busqueda'] = Array();

    $nombre = Array();
    $objeto = Array();

    $nombre = buscar_por_nombre($clave);
    $objeto = buscar_por_objeto($clave);

    $_SESSION['busqueda'] = array_merge($nombre, $objeto);
    
?>