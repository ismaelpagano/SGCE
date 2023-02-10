<?php

    include 'funcs/funcs.php';

    $tipo = NULL;

    if(isset($_POST['tipo'])){
        $tipo = $_POST['tipo'];
    }

    function visita_obligatoria(){

        $ret = "
            <div id='div_fechas_visita'>
                <label for='visita_puntual'>Visita puntal</label>
                <input name='visita_puntual' type='checkbox' id='checkbox_visita' onchange='visita()'>";

        $ret .= "<div id='div_fecha_inicio'><label id='label_visita'for='fecha_inicio'>Fecha inicio:</label><input type='date' name='fecha_inicio' id='fecha_inicio' ></div>";
        $ret .= "<div id='div_fecha_fin'><label for='fecha_fin'>Fecha fin:</label><input type='date' name='fecha_fin' id='fecha_fin' ></div>";

        $ret .= "</div>";

        return $ret;

    }

    echo visita_obligatoria();


?>