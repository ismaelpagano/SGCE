<?php

    include "funcs/funcs.php";

    if($_POST['lista_subc'] != ''){
        
        $_SESSION['user']->oferta_actual->agregar_subc($_POST['lista_subc']);
        $_SESSION['user']->oferta_actual->mostrar_subc();

    }
    
?>