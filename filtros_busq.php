<?php

    session_start();
    $incisos = $_SESSION['incisos'];
    $tipos = $_SESSION['tipos_compra'];

?>
<div id="buscador_pers">
    <form id="buscador_filtros" method='POST' action="javascript:void(0);">
        <div id="tipo_pub_div">
            <input type="radio" id="filtros_todos" name="tipo_pub" value="l" onchange="filtros('adj_off')" checked>
            <label for="todos" >Todos los llamados</label><br>
            <input type="radio" id="filtros_vigentes" name="tipo_pub" value="lv" onchange="filtros('adj_off')">
            <label for="vigentes">Llamados vigentes</label><br>
            <input type="radio" id="filtros_adj" name="tipo_pub" value="a" onchange="filtros('adj')">
            <label for="adj">Adjudicaciones</label><br>
        </div>
        <div id="org_contr_div">
            <label for="org_contr_in">Inciso:</label>
            <br>
            <select class='filtros_select' name="org_contr_in" id="org_contr_in" onchange="filtros('inciso')">
                <option value='0'>Todos los incisos</option>
                <?php 
                    foreach($incisos as $inciso){
                        echo "<option value='".$inciso->id_inciso."'>".$inciso->id_inciso." - ".$inciso->nom_inciso."</option>";
                    }				
                ?>
            </select>
            <br>
            <label for="org_contr_ue">Unidad ejecutora:</label>
            <br>
            <select class='filtros_select' name="org_contr_ue" id="org_contr_ue">
                <option value='0'>Todos las unidades ejecutoras</option>
            </select>
            <br>
        </div>
        <div id="tipo_contr_div">
            <label for="tipo_contr">Tipo contratación:</label>
            <br>
            <select class='filtros_select' name="tipo_contr" id="tipo_contr" onchange="filtros('tipo')">
                <option value='0'>Todos los tipos</option>
                <?php 
                    foreach($tipos as $tipo){
                        echo "<option value='".$tipo->id_tipo_compra."'>".$tipo->descripcion."</option>";
                    }				
                ?>
            </select>
            <br>
            <label for="subtipo_contr">Subtipo contratación:</label>
            <br>
            <select class='filtros_select' name="subtipo_contr" id="subtipo_contr">
                <option value='0'>Todos los subtipos</option>
            </select>
            <br>
        </div>
        <div id="num_llamado_div">
            <label for="numero_llamado">Número de llamado:</label><br>
            <input type="text" id="numero_llamado" name="num_llamado">
            <button id="btn_num" disabled>/</button>
            <input type="text" id="anio_llamado" name="anio_llamado"><br>
        </div>
        <div id="proveedor_div">
        </div>
        <div id="rango_fechas_div">
            <select class='filtros_select' name="tipo_fecha" id="tipo_fecha">
                <option value="rof">Recepción de ofertas</option>
                <option value="mod">Ultima modificación</option>
                <option value="pub">Publicación</option>
            </select><br>
            <label for="fecha_inicio">Fecha inicio:</label><br>
            <input class='filtros_select' type="date" id="fecha_inicio" name="fecha_inicio"><br>
            <label for="fecha_fin">Fecha fin:</label><br>
            <input class='filtros_select' type="date" id="fecha_fin" name="fecha_fin"><br>
        </div>
        <div id="buscar_catalogo_div">
            <input type="radio" id="radio_clas" name="catalogo" value="clas" checked onchange="filtro_catalogo()">
            <label for="clas" >Clasificación</label><br>
            <input type="radio" id="radio_art" name="catalogo" value="art" onchange="filtro_catalogo()">
            <label for="art">Artículo</label><br>
            <div id="div_catalogo">
            </div>
        </div>
        <div>
            <label for="input_objeto">Objeto</label><br>
            <input type="text" name="input_objeto" placeholder="Ej. 'pintura'" autocomplete="off">
        </div>
        <input type="submit" value="Filtrar" onclick='nueva_busqueda("filtros", 0)'>
    </form>
</div>