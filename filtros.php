<?php

    include 'funcs/funcs.php';


    if(isset($_POST['inciso'])){
        
        $inciso = $_POST['inciso'];

        $ues = $_SESSION['ues'][$inciso];

        echo "<option value='0'>Todos las unidades ejecutoras</option>";

        if($inciso != 0){
            foreach($ues as $ue){

                echo "<option value='".$ue->id_ue."'>".$ue->id_ue." - ".$ue->nom_ue."</option>";
    
            }
        }
    
    } else if(isset($_POST['tipo'])){

        $tipo = $_POST['tipo'];

        $subtipos = $_SESSION['subtipos_compra'][$tipo];

        echo "<option value='0'>Todos los subtipos</option>";

        if($tipo != 0){
            
            foreach($subtipos as $subtipo){

                echo "<option value='".$subtipo->id_subtipo."'>".$subtipo->resumen."</option>";
    
            }
        }

    } else if(isset($_POST['familia'])){

        $familia = $_POST['familia'];

        echo "<option value='0'>Todas las subfamilias</option>";

        if($familia != 0){

            $subfamilias = $_SESSION['subfamilias'][$familia];
    
            foreach($subfamilias as $subfamilia){
    
                echo "<option value='".$subfamilia->cod."'>".$subfamilia->descripcion."</option>";
        
            }
        }
    
    } else if(isset($_POST['subfamilia'])){

        $subfamilia = $_POST['subfamilia'];
        $familia = $_POST['familia_sub'];

        echo "<option value='0'>Todas las clases</option>";

        if($subfamilia != 0 && $familia != 0){       
        
            $clases = $_SESSION['clases'][$familia][$subfamilia];
            
            foreach($clases as $clase){

                echo "<option value='".$clase->cod."'>".$clase->descripcion."</option>";
        
            }
        }


    } else if(isset($_POST['clase'])){

        $clase = $_POST['clase'];
        $familia = $_POST['familia_sub'];
        $subfamilia = $_POST['subfamilia_sub'];
        
        echo "<option value='0'>Todas las subclases</option>";

        if($clase != 0 && $familia != 0 && $subfamilia != 0){

            $subclases = $_SESSION['subclases'][$familia][$subfamilia][$clase];

            foreach($subclases as $subclase){

                echo "<option value='".$subclase->cod."'>".$subclase->descripcion."</option>";
        
            }

        }


    } else if(isset($_POST['adj'])){

        $tipos = $_SESSION['tipos_doc'];

        $valor = $_POST['adj'];

        echo "<label>Proveedor:</label><br>";
        echo "<label class='label_prov' for='tipo_doc_prov'>Tipo documento:</label>";
        echo "<select class='filtros_select' name='tipo_doc_prov' id='tipo_doc_prov'>";

        foreach($tipos as $tipo){

            if($tipo->tipo == 'R'){
                echo "<option value='".$tipo->tipo."' selected>".$tipo->descripcion."</option>";
            } else {
                echo "<option value='".$tipo->tipo."'>".$tipo->descripcion."</option>";
            }
        }

        echo "</select>";

        echo "<label class='label_prov' for='nro_doc_prov'>Nro. documento:</label>";
        echo "<input type='text' class='filtros_input' name='nro_doc_prov' id='nro_doc_prov'>";

    } else if(isset($_POST['adj_off'])){
        echo '';
    }

    unset($_POST);

?>