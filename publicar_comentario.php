<?php

    include 'funcs/funcs.php';

    $id_contexto = $_POST['id_contexto'];

    $comentario = $_POST['comentario'];

    $sql = sql_con(DATABASE_GESTION);

    $q = "SELECT MAX(id_comentario) as max FROM comentarios WHERE contexto = 0 AND id_contexto = '".$id_contexto."'";

    $q = $sql->query($q);

    $num_com = 0;

    if($q){
        $r = $q->fetch_object();
        if($r->max != ''){
            $num_com = $r->max + 1;
        }
    }

    $fecha = date('Y-m-d H:i:s');

    $q = "INSERT INTO comentarios ( id_comentario , funcionario, fecha_publicacion , contexto , id_contexto , fecha_ult_mod , contenido ) VALUES ( '".$num_com."' , ".$_SESSION['user']->id_usuario." , '".$fecha."' , 0 , '".$id_contexto."' , '".$fecha."' , '".$comentario."' )";

    $q = $sql->query($q);

    mysqli_close($slq);

?>