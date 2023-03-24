<?php

    include 'funcs/funcs.php';

    $tipo = NULL;
    $return = '';

    if(isset($_POST['tipo'])){
        $tipo = $_POST['tipo'];
    }

    if(isset($_POST['ref'])){
        $ref = json_decode($_POST['ref']);
        $id_compra = $ref->id_compra;
        print_r($_POST['ref']);
    }

    function visita_obligatoria(){

        $ret = "
            <div id='div_fechas_visita'>
                <label for='visita_puntual'>Visita puntal</label>
                <input name='visita_puntual' type='checkbox' id='checkbox_visita' onchange='visita()'>";

        $ret .= "<div id='div_fecha_inicio'><label id='label_visita'for='fecha_inicio'>Fecha inicio:</label><input type='date' name='fecha_inicio' id='fecha_inicio' ></div>";
        $ret .= "<div id='div_fecha_fin'><label for='fecha_fin'>Fecha fin:</label><input type='date' name='fecha_fin' id='fecha_fin' ></div>";

        $ret .= "<input type='submit' onclick='set_req(\"".$id_compra."\")' value='Ingresar'></div>";

        return $ret;

    }

    switch($tipo){

        case 1: $return = visita_obligatoria();
        break;

    }

    echo $return;

?>