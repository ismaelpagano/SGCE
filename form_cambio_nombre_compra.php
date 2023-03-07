<?php

    include 'funcs/clases.php';

    session_start();

    if(isset($_POST['id_compra'])){

        $id_compra = $_POST['id_compra'];

    }

    $compra =& $_SESSION['sistema']->seleccion_llamados[$id_compra];

    if($compra->nombre_interno != NULL){
        $nombre = $compra->nombre_interno;
    } else {
        $nombre = $_SESSION['sistema']->tipos_compra[$compra->id_tipocompra]['nom']." ".(string)$compra->num_compra."/".(string)$compra->anio_compra;
    }

    $objeto = new Atributo('compra');

    $objeto->compra = $compra;

    echo 
        '<div>
            <p>¿Quieres cambiar la denominación del llamado "'.$nombre.'"?</p>
            <form id="form_cambio_nombre_compra">
                <input id="campo_nombre" type="text" placeholder="'.$nombre.'">
                <input type="checkbox" name="checkbox_autoguardado"/>
            </form>
            <button onclick="cambiar_nombre(\''.$objeto->hash.'\')">Cambiar</button>
            <button>Cancelar</button>
            <button onclick="nombre_predeterminado(\''.$objeto->hash.'\')">Predet.</button>
        </div>';

?>