<?php


    include 'funcs/clases.php';

    session_start();
    
    echo $_SESSION['sistema']->objetos[$_POST['clave']]->id_compra;

?>