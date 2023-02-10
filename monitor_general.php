<?php

    include 'funcs/funcs.php';

    $fecha_hora_actual = date('Y-m-d H:i');
    $fecha_ult_act_bd = '';

    $total_compras_bd = 0;
    $llamados_vigentes = 0;
    $llamados_nuevos = 0;
    $llamados_vistos = 0;

?>

<div id="cont_ext">
    <div id="pestaña_arce_general">
        <p>Total de compras en sistema: </p>
        <p>Llamados vigentes: </p>
        <p>Llamados cerrados: </p>
        <p>Compras adjudicadas: </p>
    </div>
    <div id="pestaña_gestion_interna">
        <p>Fecha de última actualización del sistema: </p>
        <p>Llamados nuevos: </p>
        <p>Llamados vistos desde (fecha de instauración del sistema): </p>
        <p>Llamados guardados: </p>
        <p>Llamados vigentes (de los guardados): </p>
        <p>Llamados desestimados</p>
        <p>Suscripciones a llamados</p>
    </div>
</div>