<?php

    include('funcs/funcs.php');

    $user = NULL;
    $password = NULL;

    if(isset($_POST['username'])){
        $user = $_POST['username'];
        $password = $_POST['password'];
        $_SESSION['user'] = new Sesion_usuario($user, $password);
        if($_SESSION['user']->bool_session){
            $_SESSION['sistema']->limpiar_error();
            header("Location: index.php");
        } else {
            $_SESSION['sistema']->registrar_error('Usuario o contraseña equivocada.');
            $_SESSION['user'] = NULL;
            header("Location: ingreso.php");
        }
    }
    

?>