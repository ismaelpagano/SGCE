<?php 

    function actualizador_final(){

        $fecha_actual = fecha_hora_actual();

        $fecha_ult_act = fecha_ult_act_bd();

        if((strtotime($fecha_actual) - strtotime($fecha_ult_act)) <= 864000){

            $url = formar_URL($fecha_ult_act, $fecha_actual);

            actualizar_compras($url);
    
        } else {

            $fecha_intermedia = $fecha_ult_act;

            while(strtotime($fecha_ult_act) < strtotime($fecha_actual)){

                $fecha_intermedia = date('Y-m-d H:i', strtotime($fecha_ult_act.'+ 863999 SECONDS'));

                $url = formar_URL($fecha_ult_act, $fecha_intermedia);

                actualizar_compras($url);

                $fecha_ult_act = $fecha_intermedia;
            }
        }

        $sql = sql_con(DATABASE_ACTUALIZADOR);

        $q = $sql->query("INSERT INTO fecha_ult_actualizacion (fecha_ult_act) VALUES ('".$fecha_actual."')");

        mysqli_close($sql);

    }

    function actualizar_compras_v1($url){

        echo $url.'<br>';

        $fecha_ult_act = fecha_ult_act_bd();
        $sql = sql_con(DATABASE_COMPRAS);

        echo 'Fecha ult actualizacion :'.$fecha_ult_act.'<br>';

        $xml = simplexml_load_file($url);

        $attributes = $xml->reporte_cabezal->attributes();
        $fecha_generado = $attributes['fecha-generado'];
        $cant_compras =  $attributes['cant-compras'];

        $compras = $xml->reporte_dato->children();

        $ids_compras = Array();

        $q ="
            SELECT * 
            FROM gestion_compras INNER JOIN compras 
            ON gestion_compras.id_compra = compras.id_compra 
            WHERE compras.fecha_hora_apertura > '".$fecha_ult_act."'
            AND gestion_compras.estado_interno = 2
            ORDER BY fecha_hora_tope_entrega ASC
        ";

        $q = $sql->query($q);
        
        $ids_compras = Array();
            
        if($q){
            while($r = $q->fetch_object()){
                $ids_compras[$r->id_compra] = $r->id_compra;
            }
        }

        foreach($compras as $compra){

            $attributes = $compra->attributes();

            $id_compra = $attributes['id_compra'];

            $fecha_publicacion = formatear_fecha_hora($attributes->fecha_publicacion);

            if($fecha_publicacion > $fecha_ult_act){

                //OBTIENE UNA NUEVA COMPRA, LO SABEMOS PORQUE SU FECHA DE PUBLICACION ES MAYOR A LA FECHA DE ULT ACT DE LA TABLA COMPRAS

                $ids_compras[(string)$id_compra] = $id_compra;
                $query = "INSERT INTO compras ( id_compra";
                $values = " ) VALUES ( '".$id_compra."'";

                // SE GENERA EL QUERY CON LOS ATRIBUTOS Y SUS RESPECTIVOS VALORES

                foreach($attributes as $a => $b){

                    if($a != 'id_compra'){

                        $query = $query.' , '.$a;

                        if(in_array($a, NUMERICO)){
                                $values = $values.' , '.$b;
                            
                        } else {
                            if (in_array($a, FECHAHORA)){
                                $b = formatear_fecha_hora($b);
                            }
                            $values = $values.' , \''.$b.'\'';
                        }

                    }
                }

                $values = $values." )";
                $query = $query.$values;

                $estadoInterno = 0;
                $valoracionInterna = 0;

                $q = $sql->query($query);

                echo $query;

                //A LA MISMA VEZ SE INGRESA UN REGISTRO EN LA TABLA GESTION COMPRAS, CORRESPONDIENTE A LA COMPRA  
          
                $q = $sql->query('INSERT INTO gestion_compras (id_compra, estado_interno, valoracion_interna) VALUES (\''.$id_compra.'\', '.$estadoInterno.', '.$valoracionInterna.')');
                

            } else {

                if(array_key_exists((string)$id_compra, $ids_compras)){
                    $q = $sql->query("DELETE FROM compras WHERE id_compra = '".$id_compra."'");

                    $query = "INSERT INTO compras ( id_compra";
                    $values = " ) VALUES ( '".$id_compra."'";

                    foreach($attributes as $a => $b){

                        if($a != 'id_compra'){

                            $query = $query.' , '.$a;

                            if(in_array($a, NUMERICO)){
                                    $values = $values.' , '.$b;
                                
                            } else {
                                if (in_array($a, FECHAHORA)){
                                    $b = formatear_fecha_hora($b);
                                }
                                $values = $values.' , \''.$b.'\'';
                            }

                        }
                    }

                    $values = $values." )";
                    $query = $query.$values;
                    
                    $q = $sql->query($query);
                }

            }
            //ingresar_item_compra($compra, $id_compra);

        }

        mysqli_close($sql);

    }

    function formatear_UE($ue, $inciso){

        $sql = sql_con(DATABASE_CODIGUERAS);

        $q = $sql->query('SELECT nom_ue FROM unidades_ejecutoras WHERE id_ue = '.$ue.' AND id_inciso = '.$inciso);

        if($q->num_rows > 0){
            $r = $q->fetch_object();
            return $r->nom_ue;
        }

        mysqli_close($sql);

    }

    function formatear_UCC($ucc){

        $sql = sql_con(DATABASE_CODIGUERAS);

        $q = $sql->query('SELECT nom_ucc FROM unidades_compra_centralizadas WHERE id_ucc = '.$ucc);

        if($q->num_rows > 0){
            $r = $q->fetch_object();
            return $r->nom_ucc;
        }

        mysqli_close($sql);

    }

    function formatear_inciso($inciso){

        $sql = sql_con(DATABASE_CODIGUERAS);

        $query = 'SELECT nom_inciso
        FROM incisos 
        WHERE id_inciso = '.$inciso;

        if($inciso != NULL){

            $q = $sql->query($query);

            $r = $q->fetch_object();
    
            $inciso = $r->nom_inciso;

            return $inciso;

        }

        mysqli_close($sql);

    }

    function ingresar_item_compra($compra, $id_compra){

        $sql = sql_con(DATABASE_COMPRAS);
        $items = $compra->items->children();

        if($items != NULL){            
            
            foreach($items as $item){

                $attributes = $item->attributes();

                $nro_item = $attributes['nro_item'];

                $query = 'SELECT * FROM items_compra WHERE id_compra = \''.$id_compra.'\' AND nro_item = '.$nro_item;

                $q = $sql->query($query);

                if($q->num_rows == 0){

                    $query = "INSERT INTO items_compra ( id_compra";
                    $values = ") VALUES ( '".$id_compra."'";
    
                    foreach($attributes as $att => $val){
    
                        if($val != ""){
    
                            $query = $query.' , '.$att;
    
                            if(in_array($att, NUMERICO)){
                                $values = $values.' , '.$val;
                            }else if(in_array($att, FECHAHORA)){
                                $val = formatear_fecha_hora($val);
                                $values = $values.' , '.$val;
                            }else{
                                $values = $values.' , \''.$val.'\'';
                            }
    
                        }
                    }
    
                    $query = $query.$values.' )';

                    echo $query;
    
                    $q = $sql->query($query);
                }

                ingresar_atributos_item_compra($item, $nro_item, $id_compra);
            }
        }

        mysqli_close($sql);
    }

    function ingresar_atributos_item_compra($item, $nro_item, $id_compra){

        $sql = sql_con(DATABASE_COMPRAS);

        if(isset($item->atributos_item)){

            $atributos_item = $item->atributos_item->children();

            foreach($atributos_item as $atributo_item){

                $attributes = $atributo_item->attributes();

                $q = $sql->query("SELECT * FROM atributos_items_compra WHERE id_compra = '".$id_compra."' AND nro_item = ".$nro_item." AND id_prop_atributo = ".$attributes['id_prop_atributo']);

                if($q->num_rows == 0){

                    $query = "INSERT INTO atributos_items_compra ( id_compra , nro_item";
                    $values = ") VALUES ( '".$id_compra."' , ".$nro_item;

                    foreach($attributes as $att => $val){

                        if($val != ""){

                            $query = $query.' , '.$att;

                            if(in_array($att, NUMERICO)){
                                $values = $values.' , '.$val;
                            }else {
                                if(in_array($att, FECHAHORA)){
                                    $val = formatear_fecha_hora($val);
                                }
                                $values = $values.' , \''.$val.'\'';
                            }
                        }

                    }

                    $query = $query.$values.' )';

                } else {

                    $query = "UPDATE atributos_items_compra SET id_prop_atributo = ".$attributes['id_prop_atributo']." ";

                    foreach($attributes as $att => $val){

                        if($val != "" && $att != 'id_prop_atributo'){

                            if(in_array($att, NUMERICO)){
                                $query = $query.', '.$att.' = '.$val;
                            }else {
                                if(in_array($att, FECHAHORA)){
                                    $val = formatear_fecha_hora($val);
                                }
                                $query = $query.', '.$att.' = \''.$val.'\'';
                            }
                        }

                    }

                    $query = $query.' WHERE id_compra = \''.$id_compra.'\' AND nro_item = '.$nro_item.' AND id_prop_atributo = '.$attributes['id_prop_atributo'];
                }

                $q = $sql->query($query);

            }
        }

        mysqli_close($sql);
    };

    function ingresar_modificaciones_llamado($id_compra, $modificaciones){
        $sql = sql_con(DATABASE_COMPRAS);

        $q = $sql->query("DELETE FROM historial_modificaciones_llamado WHERE id_compra = '".$id_compra."'");

        foreach($modificaciones as $modificacion){
            $query = "INSERT INTO historial_modificaciones_llamado (id_compra, fecha, campo, valor_anterior, valor_nuevo) VALUES ('".$id_compra."', '".$modificacion->fecha."', '".$modificacion->campo."', '".$modificacion->valor_anterior."', '".$modificacion->valor_nuevo."')";
            $q = $sql->query($query);
        };
    }

?>