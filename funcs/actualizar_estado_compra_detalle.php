<?php

    include 'funcs.php';

    $_SESSION['sistema']->seleccion_llamados[$_POST['id_compra']]->actualizar_estado_compra(estado_interno($_POST['estado'])[2]);

?>
