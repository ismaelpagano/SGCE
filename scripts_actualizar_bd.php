<?php

    function insert_compra_bd($compra, $id_compra){

        $atributos_compra = $compra->attributes();     

        $query = "INSERT INTO compras ( id_compra";
        $values = " ) VALUES ( '".$id_compra."'";

        foreach($atributos_compra as $a => $b){

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
        
        echo insert_bd($query, DATABASE_COMPRAS, "'compra'");
    }

    function insert_compra_bd_gestion($id_compra){
        $query = "INSERT INTO gestion_bd ( id_compra , ) VALUES (  )";
    }

    function insert_items_compra_bd($items_compra, $id_compra){

        foreach($items_compra as $item){

            $atributos_item = $item->attributes();
            $nro_item = $atributos_item->nro_item;

            if($atributos_item->fecha_hora_puja != NULL){
                $atributos_item->fecha_hora_puja = cadena_fecha_hora($aclaracion->fecha);
            }

            $query = "INSERT INTO items_compra ( id_compra";
            
            $values = " ) VALUES ( '".$id_compra."'";

            foreach($atributos_item as $a => $b){
                $query = $query." , ".$a;
                $values = $values." , '".$b."'";
            }

            $query = $query.$values." )";

            echo insert_bd($query, DATABASE_COMPRAS, "'ítem'");

            $atributos_item_compra = $item->atributos_item->children();

            if($atributos_item_compra != NULL){
                insert_atributos_item_compra_bd($atributos_item_compra, $id_compra, $nro_item);
            }

        }
    }

    function insert_atributos_item_compra_bd($atributos_item_compra, $id_compra, $nro_item){

        $nro_atributo_item = 0;

        foreach($atributos_item_compra as $atributo_item_compra){
            
            $atributos_atributo_item_compra = $atributo_item_compra->attributes();

            $query = "INSERT INTO atributos_items_compra ( id_compra , nro_item , nro_atributo_item";
        
            $values = " ) VALUES ( '".$id_compra."' , ".$nro_item." , ".$nro_atributo_item;

            foreach($atributos_atributo_item_compra as $a => $b){
    
                    $query = $query.' , '.$a;
    
                    if(in_array($a, NUMERICO)){
                            $values = $values.' , '.$b;
                    } else {
                        if (in_array($a, FECHAHORA)){
                            $b = formatear_fecha_hora($b);
                        }
                        $values = $values." , '".$b."'";
                    }
            }

            $query = $query.$values." )";

            echo insert_bd($query, DATABASE_COMPRAS, "'atributo de ítem'");

            $valores_atributos_item = $atributo_item_compra->valores_atributos_item->children();

            if($valores_atributos_item != NULL){
                insert_valores_atributo_item_compra_bd($valores_atributos_item, $id_compra, $nro_item, $nro_atributo_item);
            }

            $nro_atributo_item++;

        }
    }

    function insert_valores_atributo_item_compra_bd($valores_atributos_item, $id_compra, $nro_item, $nro_atributo_item){

        $nro_valor_atributo_item = 0;

        foreach($valores_atributo_item as $valor_atributo_item){

            $atributos_valor_atributo_item = $valor_atributo_item->attributes();
            $query = "INSERT INTO valores_atributos_items_compra ( id_compra , nro_item , nro_atributo_item , nro_valor_atributo_item";
            $values = " ) VALUES ( '".$id_compra."' , ".$nro_item." , ".$nro_atributo_item." , ".$nro_valor_atributo_item;

            foreach($atributos_valor_atributo_item as $a => $b){
                $query = $query." , ".$a;

                if(in_array($a, NUMERICO)){
                        $values = $values." , ".$b;
                } else {
                    if (in_array($a, FECHAHORA)){
                        $b = cadena_fecha_hora($b);
                    }
                    $values = $values." , '".$b."'";
                }
            }

            $query = $query.$values.' )';
            
            echo insert_bd($query, DATABASE_COMPRAS, "'valor de atributo de ítem'");

            $nro_valor_atributo_item++;

        };
    }

    function insert_mod_compra_bd($modificaciones_compra, $id_compra){

        $q = insert_bd("DELETE FROM historial_modificaciones_llamado WHERE id_compra = '".$id_compra."'", DATABASE_COMPRAS, 'Se han eliminado el historial de modificacion de llamados de la compra: '.$id_compra.'.<br>');

        foreach($modificaciones_compra as $modificacion){
                    
            $atributos_modificacion = $modificacion->attributes();
            $atributos_modificacion->fecha = cadena_fecha_hora($atributos_modificacion->fecha);

            if(in_array($atributos_modificacion->campo, CAMPO_FECHA)){
                $atributos_modificacion->valor_anterior = cadena_fecha($atributos_modificacion->valor_anterior);
                $atributos_modificacion->valor_nuevo = cadena_fecha($atributos_modificacion->valor_nuevo);
            } else if(in_array($atributos_modificacion->campo, CAMPO_FECHAHORA)){
                $atributos_modificacion->valor_anterior = cadena_fecha_hora($atributos_modificacion->valor_anterior);
                $atributos_modificacion->valor_nuevo = cadena_fecha_hora($atributos_modificacion->valor_nuevo);
            }

            $query = "INSERT INTO historial_modificaciones_llamado ( id_compra";
            
            $values = " ) VALUES ( '".$id_compra."'";

            foreach($atributos_modificacion as $a => $b){
                $query = $query." , ".$a;
                $values = $values." , '".$b."'";
            }

            $query = $query.$values.' )';

            echo insert_bd($query, DATABASE_COMPRAS, "'modificación de compra'");

            //Hasta acá las modificaciones de una compra
        }

    }

    function insert_aclar_compra_bd($aclaraciones_compra, $id_compra){

        $q = insert_bd("DELETE FROM aclaraciones_llamado WHERE id_compra = '".$id_compra."'", DATABASE_COMPRAS, 'Se han eliminado las aclaraciones de la compra: '.$id_compra.'.<br>');
        
        foreach($aclaraciones_compra as $aclaracion){
            $atributos_aclaracion = $aclaracion->attributes();
            
            $atributos_aclaracion->fecha = cadena_fecha_hora($atributos_aclaracion->fecha);

            $query = "INSERT INTO aclaraciones_llamado ( id_compra";
            
            $values = " ) VALUES ( '".$id_compra."'";

            foreach($atributos_aclaracion as $a => $b){
                $query = $query." , ".$a;
                $values = $values." , '".$b."'";
            }

            $query = $query.$values.' )';

            echo insert_bd($query, DATABASE_COMPRAS, "'aclaración de compra'");
        }

    }

    function update_compra_bd($compra, $id_compra){

        $attributes = $compra->attributes();

        $query = "UPDATE compras SET id_compra = '".$id_compra."'";

        foreach($attributes as $a => $b){

            if($a != 'id_compra'){

                $query = $query." , ".$a;

                if(in_array($a, NUMERICO)){
                    $query = $query." = ".$b;
                } else {
                    if (in_array($a, FECHAHORA)){
                        $b = formatear_fecha_hora($b);
                    }
                    $query = $query." = '".$b."'";
                }
            }
        }

        $query = $query." WHERE id_compra = '".$id_compra."'";

        echo $query.'<br>';

        echo insert_bd($query, DATABASE_COMPRAS, "'SE HA ACTUALIZADO UNA COMPRA'");

    }

    function insert_gestion_compra_bd($id_compra){

        $query = "INSERT INTO gestion_compras ( id_compra , estado_interno, valoracion_interna ) VALUES ( '".$id_compra."' , 0 , 0 )";

        echo insert_bd($query, DATABASE_GESTION, "'se ha ingresado un registro de gestión de compra'");

    }

?>