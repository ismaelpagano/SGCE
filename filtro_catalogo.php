<?php

    $valor = '';

    $sql = new mysqli('localhost', 'root', '', '');

    if(isset($_POST['catalogo'])){

        $valor = $_POST['catalogo'];

    }

    if($valor == 'clas'){

        $familias = Array();
        $clases = Array();

        $q = $sql->query("SELECT * FROM catalogo_arce.familias as familias WHERE familias.comprable = 'S' ORDER BY familias.cod ASC");

        if($q){
            while($r = $q->fetch_object()){
                $familias[] = $r;
            }
        }

        echo "<select class='filtros_select' name='familias_catalogo' id='familias_catalogo' onchange='filtros(\"familia\")'>";

        echo "<option value='0'>Todas las familias</option>";

        foreach($familias as $familia){
            echo "<option value='".$familia->cod."'>".$familia->descripcion."</option>";
        }

        echo "</select>";

        echo "<select class='filtros_select' name='subfamilias_catalogo' id='subfamilias_catalogo' onchange='filtros(\"subfamilia\")'>
            <option value='0'>Todas las subfamilias</option>
        </select>";

        echo "<select class='filtros_select' name='clases_catalogo' id='clases_catalogo' onload='filtros(\"clase\")' onchange='filtros(\"clase\")'>
            <option value='0'>Todas las clases</option>
        </select>";

        echo "<select class='filtros_select' name='subclases_catalogo' id='subclases_catalogo'>
            <option value='0'>Todas las subclases</option>
        </select>";
    } else if ($valor == 'art') {
        echo "<input type='text' class='filtros_select' id='cod_art' name='cod_art' placeholder='Cód. Artículo'><br>";
    }


?>