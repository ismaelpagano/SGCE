<?php
    
    include 'funcs.php';

    $compra = $_SESSION['compra_actual'];

    $compra->actualizar_estado_compra($_POST['estado']);

?>