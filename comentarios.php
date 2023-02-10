<?php 

    if(isset($_POST['id_contexto'])){
        $id_contexto = $_POST['id_contexto'];
        include 'funcs/funcs.php';
    }

    $sql = sql_con(DATABASE_GESTION);

    $q = "SELECT * FROM comentarios WHERE contexto = 0 AND id_contexto = '".$id_contexto."'";

    $q = $sql->query($q);

    $comentarios = Array();

    if($q){

        while($r = $q->fetch_object()){
            $comentarios[] = $r;
        }

    }

    if(count($comentarios) == 0){
        echo "<p style='font-style:italic;'>Aún no hay comentarios.</p>";
    } else {

        foreach($comentarios as $comentario){

            $fecha = date('d/m/Y H:i', strtotime($comentario->fecha_ult_mod));

            $editado = '';

            if($comentario->fecha_ult_mod != $comentario->fecha_publicacion){
                $editado = 'editado';
            }

            $usuario = $_SESSION['sistema']->usuarios[$comentario->funcionario];

            echo "
                <div id='cont_comentario'>
                    <table id='tabla_comentario'>
                        <tr id='fila_encabezado'>
                            <th id='funcionario_comentario'>".$usuario->nombre." ".$usuario->apellido."</th>
                            <th id='espacio_encabezado'></th>
                            <th id='fecha_comentario'>".$fecha."</th>
                            <th id='boton_importante'>
                                <div id='bot_com_imp' onclick='fav_comentario(\"".$id_contexto."\" , ".$comentario->id_comentario.")'>
                                    <img src='img/comentario_favOFF.png'>
                                </div>
                            </th>
                        </tr>
                        <tr id='fila_comentario'>
                            <td colspan='4'>".$comentario->contenido."</td>
                        </tr>
                        <tr id='comentario_editado'>
                            <td colspan='4'>".$editado."</td>
                        </tr>   
                    </table>
                </div>";
        }
    }
?>

<div id="escribir_comentario">
    <form id="form_comentario" action="javascript:void(0);">
        <textarea id="textarea_comentario" name="comentario"></textarea>
        <button onclick="publicar_comentario('<?php echo $id_contexto; ?>')">Comentar</button>
    </form>
</div>
