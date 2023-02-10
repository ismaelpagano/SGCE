<?php

    include "funcs/funcs.php";

    $_SESSION['user']->cerrar_sesion(true);
    $_SESSION['user'] = NULL;

?>