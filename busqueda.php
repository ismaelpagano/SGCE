<?php 
include 'funcs/funcs.php';

    if($_POST['key'] != ''){
        if($_POST['key'] == 'obj'){
            $_SESSION['sistema']->objeto = $_POST['input_objeto'];
        }
        $busqueda = new Busqueda($_POST['key']);
        $_SESSION['sistema']->clave_busqueda = $_POST['key'];
    } else {
        $busqueda = new Busqueda('no_cat');
        $_SESSION['sistema']->clave_busqueda = 'no_cat';
    }

    if(isset($_POST['pag'])){
        $_SESSION['sistema']->pagina_actual = $_POST['pag']; 
    } else {
        $_SESSION['sistema']->pagina_actual = 0;
    }

?>