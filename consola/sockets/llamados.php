<?php

    include 'funcs/funcs.php';

    $_SESSION['CLIENTE']->llamados();

    echo $_SESSION['CLIENTE']->llamados->coleccion['GUARDADOS'];

?>