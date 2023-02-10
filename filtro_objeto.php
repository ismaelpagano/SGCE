<?php

    session_start();
    $incisos = $_SESSION['incisos'];
    $tipos = $_SESSION['tipos_compra'];

?>

<div id="buscador_pers">
    <form id="buscador_objeto" method='POST' action="javascript:void(0);">
        <div>
            <label for="input_objeto"></label>
            <input type="text" name="input_objeto" placeholder="Ej. 'pintura'" autocomplete="off">
        </div>
        <div>
            <input type="submit" value="Buscar" onclick="nueva_busqueda('obj', 0)">
        </div>
    </form>
</div>
