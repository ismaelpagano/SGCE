<?php

    include 'funcs/funcs.php';

    $cliente = new Cliente('ipagano', '49738131');

    print_r($cliente);

    if($cliente->estado != 'ERROR'){
        $_SESSION['CLIENTE'] = $cliente; 
    }

?>