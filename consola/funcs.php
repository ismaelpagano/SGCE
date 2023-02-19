<?php

    include '../funcs/listas.php';
    include '../funcs/clases.php';

    date_default_timezone_set("America/Montevideo");

    define('DATABASE_COMPRAS', 'gestor_compras_estatales_sandbox');
    define('DATABASE_GESTION', 'gestion_bd_sandbox');
    define('DATABASE_CODIGUERAS', 'codigueras_arce');
    define('DATABASE_ACTUALIZADOR', 'gestion_bd_sandbox');
    define('GESTION_USUARIOS', 'gestion_usuarios');
    
    define('DIRECCION', '/gestor_compras_estatales_sandbox/');
    define('URL_WS', 'https://www.comprasestatales.gub.uy/comprasenlinea/jboss/generarReporte?');
    define('URL_PLIEGO', 'https://www.comprasestatales.gub.uy/Pliegos/');
    
    define('FECHAHORA', Array('fecha', 'fecha_publicacion', 'fecha_ult_mod_llamado', 'fecha_hora_apertura', 'fecha_hora_tope_entrega', 'fecha_hora_puja', 'fecha_pub_adj', 'fecha_sol_prorr', 'fecha_sol_aclar', 'fecha_compra', 'fecha_vigencia_adj', 'valor_fecha'));
    define('CAMPO_FECHAHORA', Array('FECHA_HORA_TOPE_ENTREGA', 'FECHA_HORA_APERTURA', 'FECHA_HORA_PUJA'));
    define('CAMPO_FECHA', Array('FECHA_SOL_PRORR', 'FECHA_SOL_ACLAR'));
    define('NUMERICO', Array(
        'id_inciso', 
        'id_ue', 
        'id_ucc', 
        'anio_compra', 
        'nro_ampliacion', 
        'estado_compra', 
        'id_moneda_pliego', 
        'id_moneda_monto_adj',
        'id_tipo_resol',
        'num_resol',
        'nro_item',
        'cant_pedida',
        'id_moneda',
        'id_moneda_cotiz',
        'durac_puja',
        'margen_puja',
        'id_articulo',
        'id_color',
        'id_unidad',
        'id_detalle_variante',
        'id_marca',
        'precio_pliego', 
        'monto_adj',
        'cant_adj',
        'precio_unit',
        'precio_tot_imp',
        'id_prop_atributo',
        'id_unidad_med_prop_atributo',
        'valor_numerico',
        'nro_atributo_item_compra',
        'nro_valor_atributo_item_compra'
    ));

    // Funciones de Sesion

    /////////////////////////////////////////
    //Funciones de fecha y hora

    function strtodate($format, $str){

        $str = strtotime($str);

        $return = date($format, $str);
        
        return $return; 

    };

    function fecha_hora_cadena($fecha){
        return (string)$fecha;
    }

    function cadena_fecha_hora($cadena){
        $dia = strchr($cadena, '/', true);
        $mes = strchr(substr(strchr($cadena, '/'), 1), '/', true);
        $anio = strchr(substr(strchr(substr(strchr($cadena, '/'), 1), '/'), 1), ' ', true);

        $hora = strchr(substr(strchr($cadena, ' '), 1), ':', true);
        $minutos = substr(strchr($cadena, ':'), 1);

        return $anio.'-'.$mes.'-'.$dia.' '.$hora.':'.$minutos.':00';
    }

    function fecha_cadena($fecha){
        return (string)$fecha;
    }

    function cadena_fecha($cadena){
        $dia = strchr($cadena, '/', true);
        $mes = strchr(substr(strchr($cadena, '/'), 1), '/', true);
        $anio = substr(strchr(substr(strchr($cadena, '/'), 1), '/'), 1);

        return $anio.'-'.$mes.'-'.$dia;
    }

    function fecha_actual(){
        return date('Y-m-d');
    }

    function fecha_hora_actual(){
        return date('Y-m-d H:i:s');
    }

    function formatear_fecha_hora($fecha){

        if($fecha == ''){

            return(NULL);

        } else {

            $fecha_formateada;

            $diaFecha = strchr($fecha, '/', true);
            
            $mesFecha = strchr(substr(strchr($fecha, '/'), 1), '/', true);
        
            $anioFecha = strchr(substr(strchr(substr(strchr($fecha, '/'), 1), '/'), 1), ' ', true);

            if(strpos($fecha, " ")){
                
                $horaFecha = strchr(substr(strchr($fecha, ' '), 1), ':', true);

                $minutosfecha = substr(strchr($fecha, ':'), 1);

                $fecha_formateada = date('Y-m-d H:i', strtotime($anioFecha.'-'.$mesFecha.'-'.$diaFecha.' '.$horaFecha.':'.$minutosfecha));
            } else {
                $fecha_formateada = date('Y-m-d H:i', strtotime($anioFecha.'-'.$mesFecha.'-'.$diaFecha));
            }

            return $fecha_formateada;

        }
    }

    ////////////////////////////////////////////////
    //Funciones de base de datos

    function sql_con($db = ''){
        $sql = new mysqli('localhost', 'root', '', $db);
        $sql->set_charset('utf8');
    
        return $sql;
    }

    function sql_query($database, $query){

        $q;

        $sql = sql_con($database);

        $q = $sql->query($query);

        mysqli_close($sql);

        return $q;
    }

    ////////////////////////////////////////////////
    // Funciones referidas a la gestion del actualizador

    function formar_URL($tipo_publicacion, $fecha_inicio, $fecha_fin){

        $fecha_inicio = strtodate('Y-m-d H:i:s', $fecha_inicio.' - 1 HOUR');

        $fecha_fin = strtodate('Y-m-d H:i:s', $fecha_fin.' + 1 HOUR');

        $tipo_publicacion = '&tipo_publicacion='.$tipo_publicacion;
        
        $tipo_compra = '&tipo_compra=';

        strtodate('Y', $fecha_inicio);

        $dia_inicio = strtodate('d', $fecha_inicio);
        $mes_inicio = strtodate('m', $fecha_inicio);
        $anio_inicio = strtodate('Y', $fecha_inicio);
        $hora_inicio = strtodate('G', $fecha_inicio);

        $dia_fin = strtodate('d', $fecha_fin);
        $mes_fin = strtodate('m', $fecha_fin);
        $anio_fin = strtodate('Y', $fecha_fin);
        $hora_fin = strtodate('G', $fecha_fin);

        $rango_fecha = 
            '&rango-fecha='.$dia_inicio.
            '%2F'.$mes_inicio.
            '%2F'.$anio_inicio.
            '+-+'.$dia_fin.
            '%2F'.$mes_fin.
            '%2F'.$anio_fin.'&';

        $dia_inicio = strtodate('j', $fecha_inicio);
        $mes_inicio = strtodate('n', $fecha_inicio);
        $dia_fin = strtodate('j', $fecha_fin);
        $mes_fin = strtodate('n', $fecha_fin);
        
        $url = 
            URL_WS.$tipo_publicacion.$tipo_compra.$rango_fecha.
            '&dia_inicial='.$dia_inicio.
            '&mes_inicial='.$mes_inicio.
            '&anio_inicial='.$anio_inicio.
            '&hora_inicial='.$hora_inicio.
            '&dia_final='.$dia_fin.
            '&mes_final='.$mes_fin.
            '&anio_final='.$anio_fin.
            '&hora_final='.$hora_fin;

        echo $url."\n";

        return $url;
    }

    function actualizador(){

        $fecha_actual = date('Y-m-d H:i:s');

        $fecha_ult_respaldo = get_fecha_ultimo_respaldo();

        if((int)date('H', strtotime($fecha_actual)) >= 7){

            $fecha_inicial = date('Y-m-d H:i:s', strtotime(get_fecha_ult_act_bd('compras').' - 1 HOUR'));

            $fecha_final = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' + 1 HOUR'));

            if((strtotime($fecha_final) - strtotime($fecha_inicial)) > 604800){
            
                $fecha_intermedia = date('Y-m-d H:i:s', strtotime($fecha_inicial.' + 608400 SECONDS' ));
    
                actualizador_compras($fecha_inicial, $fecha_intermedia);
    
                actualizador_adj($fecha_inicial, $fecha_intermedia);
    
                actualizador();
    
            } else {
    
                actualizador_compras($fecha_inicial, $fecha_final);
    
                actualizador_adj($fecha_inicial, $fecha_final);
            }

        } else if ($fecha_ult_respaldo != date('Y-m-d', strtotime($fecha_actual.' - 1 DAYS'))) {

            get_registros_fecha();

        
        }

    }

    function actualizador_amplio($fecha_inicial, $fecha_final){

        //$fecha_actual = date('Y-m-d H:i:s');

        //$fecha_ult_respaldo = get_fecha_ultimo_respaldo();

        //if((int)date('H', strtotime($fecha_actual)) >= 7){

            $fecha_inicial = date('Y-m-d H:i:s', strtotime($fecha_inicial.' - 1 HOUR'));

            if((strtotime($fecha_final) - strtotime($fecha_inicial)) > 604800){
            
                $fecha_intermedia = date('Y-m-d H:i:s', strtotime($fecha_inicial.' + 604800 SECONDS' ));
    
                actualizador_compras($fecha_inicial, $fecha_intermedia);
    
                actualizador_adj($fecha_inicial, $fecha_intermedia);

                echo $fecha_intermedia.' - '.$fecha_final."\n";
    
                actualizador_amplio($fecha_intermedia, $fecha_final);
    
            } else {
    
                actualizador_compras($fecha_inicial, $fecha_final);
    
                actualizador_adj($fecha_inicial, $fecha_final);
            }

        //} else if ($fecha_ult_respaldo != date('Y-m-d', strtotime($fecha_actual.' - 1 DAYS'))) {

            // get_registros_fecha();

        
        //}

    }

    function set_fecha_ult_act_bd($fecha, $bd){
        
        $sql = sql_con(DATABASE_GESTION);

        do {
            $q = $sql->query("INSERT INTO fecha_ult_actualizacion_".$bd." (fecha_ult_act) VALUES ('".$fecha."')");
        } while (!$q);


        mysqli_close($sql);

    }

    function get_fecha_ult_act_bd($bd){

        $sql = sql_con(DATABASE_ACTUALIZADOR);

        $q = $sql->query('SELECT fecha_ult_act FROM fecha_ult_actualizacion_'.$bd.' WHERE contador = (SELECT MAX(contador) FROM fecha_ult_actualizacion_'.$bd.')');
    
        while($r = $q->fetch_object()){
            $fecha_ult_act = $r->fecha_ult_act;
        }

        mysqli_close($sql);

        return $fecha_ult_act;
    }
    
    function botones_compra($item, $bool){

        $estructura = '';
        
        if($item->estado_compra==4){

            $ref1 =& $item;

            if( $item->estado_interno != 4){
                $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$item->id_compra.'", 4)\'>Desestimar</button></br>';
            }
            
            if($item->estado_interno != 2){
                $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$item->id_compra.'", 2)\'>Guardar</button></br>';
            }
            
            if($item->estado_interno < 3 && $bool == 'true'){
                $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$item->id_compra.'", 3)\'>Ofertar</button></br>';
            }		
        }	
        
        return $estructura;
    }
    
    /////////////////////////////////////
    // Búsqueda

    function buscar_por_objeto($clave){

        if($clave != ''){
            $sql = sql_con(DATABASE_COMPRAS);
            $resultados = Array();
            $query = "
                SELECT * 
                FROM compras INNER JOIN gestion_compras 
                ON compras.id_compra = gestion_compras.id_compra
                WHERE compras.objeto LIKE '%$clave%' ORDER BY fecha_hora_apertura
            ";
            $q = $sql->query($query);
            if($q){
                while($r = $q->fetch_object()){
                    $compra = new Compras($r);
                    $resultados[$compra->id_compra] = $compra;       
                }
            }

            mysqli_close($sql);
            return $resultados;
        } else {
            return $array = Array();
        }
    }

    function buscar_por_nombre($clave){

        if($clave != ''){

            $numero = strchr($clave, '/', true);
            $anio = substr(strchr($clave, '/'), 1);
            
            $sql = sql_con(DATABASE_COMPRAS);
            $query = "
                SELECT * 
                FROM compras INNER JOIN gestion_compras 
                ON compras.id_compra = gestion_compras.id_compra
                WHERE compras.num_compra = '".$numero."'
                AND compras.anio_compra = '".$anio."'
                ORDER BY fecha_hora_apertura
            ";
            $resultados = Array();
            $q = $sql->query($query);
    
            if($q){
                while($r = $q->fetch_object()){
                    $compra = new Compras($r);
                    $resultados[$compra->id_compra] = $compra;       
                }
            }
    
            mysqli_close($sql);
    
            return $resultados;

        } else {
            
            return $array = Array();

        }

    }

    ////////////////////////////////////////////////////////////////////////////////////////
    // Actualizador de compras 
    
    function insert_bd($query, $db, $string){

        $sql = sql_con($db);

        //echo $query."\n";

        $q = $sql->query($query);

        $return = '';

        if($q){
            $return = 'Se ha ingresado exitosamente: '.$string."\n";
        } else {
            $return = "Ha ocurrido un problema.\n";
        }

        mysqli_close($sql);

    }

    function actualizador_compras($fecha_inicial, $fecha_fin){

        print_r("Se actualizará la lista de compras. ".date('Y-m-d H:i:s', time()).".\n");

        $fecha_actualizacion = date('Y-m-d H:i:s');

        // 1) obtener todos los identificadores de compra de la base de datos

        $sql = sql_con(DATABASE_GESTION);

        $query = "SELECT id_compra FROM gestion_compras WHERE estado_compra != '7' AND estado_compra != '17' AND estado_compra != '27'";

        $g = $sql->query($query); 

        $query = "SELECT id_compra FROM gestion_compras WHERE estado_compra = '7' OR estado_compra = '17' OR estado_compra = '27'";

        $q = $sql->query($query); 
 
        mysqli_close($sql);  

        $id_compras_bd = Array();
        $id_compras_adj = Array();

        if($g){
            while($r = $g->fetch_object()){
                $id = $r->id_compra;
                $id_compras_bd[$id] = $id; 
            }
        }

        if($q){
            while($r = $q->fetch_object()){
                $id = $r->id_compra;
                $id_compras_adj[$id] = $id; 
            }
        }

        // 2) Obtener todas las compras agregadas o actualizadas en el periodo de tiempo predeterminado en el portal de ARCE

        $url = formar_URL('l', $fecha_inicial, $fecha_fin);
        $xml = simplexml_load_file($url);

        $compras = $xml->reporte_dato->children();

        foreach($compras as $compra){

            // 3) Por cada compra obtenida, desglosar el identificador
            $attributes = $compra->attributes();

            $id_compra = $attributes['id_compra'];

            if((!array_key_exists((string)$id_compra, $id_compras_bd)) && (!array_key_exists((string)$id_compra, $id_compras_adj)) ){
                // 4.1) Si no existe la id en la base de datos se ingresa la compra y sus items
                
                $items_compra = $compra->items->children();

                if($items_compra != NULL){
                    insert_items_compra_bd($items_compra, $id_compra);
                }

                insert_compra_bd($compra, $id_compra);

                insert_gestion_compra_bd($id_compra, $attributes['estado_compra'], $attributes['fecha_ult_mod_llamado']);

                // 4.1.2) Aquí se debe ingresar la información referida al contacto del llamado.

                // insert_contacto_compra_bd($id_compra, $compra);

            } else if(!array_key_exists((string)$id_compra, $id_compras_bd)) {
                
                $items_compra = $compra->items->children();

                if($items_compra != NULL){
                    
                    insert_items_compra_bd($items_compra, $id_compra);
                }

                update_compra_bd($compra, $id_compra);
            
            } else {
                
                // 4.2) Falta deliberar qué ocurre cuando la compra ya está en la base de datos

                // Ocurre que de una compra solo se actualizan algunos datos por ejemplo el estado de la compra, se actualizan las fechas etc, los items nunca se actualizan, 

                // deberia ocurrir un update

                update_compra_bd($compra, $id_compra);
            
            }

            // Lo siguiente siempre ocurre 

            $modificaciones_compra = $compra->hist_mod_llamado->children();

            if($modificaciones_compra != NULL){
                insert_mod_compra_bd($modificaciones_compra, $id_compra);
            }

            $aclaraciones_compra = $compra->aclaraciones_lla->children();

            if($aclaraciones_compra != NULL){
                insert_aclar_compra_bd($aclaraciones_compra, $id_compra);
            }
        }

        set_fecha_ult_act_bd($fecha_actualizacion, 'compras');
        print_r("Se actualizó la lista de compras. ".date('Y-m-d H:i:s').".\n");
    }

    function insert_compra_bd($compra, $id_compra){

        $attributes = $compra->attributes();     

        $query = "INSERT INTO compras ( id_compra";
        $values = " ) VALUES ( '".$id_compra."'";

        foreach($attributes as $a => $b){

            if($a != 'id_compra'){

                $query = $query.' , '.$a;
                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
            }
        }

        $values = $values." )";
        $query = $query.$values;
        
        insert_bd($query, DATABASE_COMPRAS, "'compra'");
    }

    function insert_compra_bd_anio($compra, $id_compra){

        $attributes = $compra->attributes();

        $sql = sql_con("gestor_compras_estatales_".$compra->anio_compra);

        $query = "INSERT INTO compras ( id_compra";
        $values = " ) VALUES ( '".$id_compra."'";

        foreach($attributes as $a => $b){

            if($a != 'id_compra'){

                $query = $query.' , '.$a;
                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
            }
        }

        $values = $values." )";
        $query = $query.$values;

        $anio = database_exists('gestor_compras_estatales_'.$compra->anio_compra);

        if(!$anio){

        }
        
        insert_bd($query, DATABASE_COMPRAS, "'compra'");
    }
    
    function update_compra_bd($compra, $id_compra){

        $attributes = $compra->attributes();

        $query = "UPDATE compras SET id_compra = '".$id_compra."'";

        foreach($attributes as $a => $b){

            if($a != 'id_compra'){
                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $query = $query.' , '.$a.' =  "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $query = $query." , ".$a." = '".$b."'";
                    } else {
                        $query = $query.' , '.$a.' = "'.$b.'"';
                    }
                }
            }
        }

        $query = $query." WHERE id_compra = '".$id_compra."'";

        insert_bd($query, DATABASE_COMPRAS, "'SE HA ACTUALIZADO UNA COMPRA'");

        if(!isset($attributes->fecha_ult_mod_llamado)){
            $fecha_mod = formatear_fecha_hora($attributes->fecha_publicacion);
        } else {
            $fecha_mod = formatear_fecha_hora($attributes->fecha_ult_mod_llamado);
        }
        
        $query = "UPDATE gestion_compras SET estado_compra = ".$attributes->estado_compra." , fecha_ult_mod = '".$fecha_mod."' WHERE id_compra = '".$id_compra."'";

        insert_bd($query, DATABASE_GESTION, "'SE HA ACTUALIZADO UNA COMPRA'");

    }

    function insert_gestion_compra_bd($id_compra , $estado_compra , $fecha_ult_mod_llamado){

        $fecha_ult_mod_llamado = cadena_fecha_hora($fecha_ult_mod_llamado);

        $query = "INSERT INTO gestion_compras ( id_compra , estado_compra , estado_interno , valoracion_interna , fecha_ult_mod ) VALUES ( '".$id_compra."' , '".$estado_compra."' , 0 , 0 , '".$fecha_ult_mod_llamado."')";

        insert_bd($query, DATABASE_GESTION, "'se ha ingresado un registro de gestión de compra'");

    }

    function update_gestion_compra_bd($id_compra, $estado_compra, $fecha_ult_mod_llamado){

        $fecha_ult_mod_llamado = cadena_fecha_hora($fecha_ult_mod_llamado);

        $query = "
            UPDATE gestion_compras 
            SET
            estado_compra = '".$estado_compra."' ,
            estado_interno =  '0' ,
            valoracion_interna = '0' ,
            valoracion_interna = '".$fecha_ult_mod_llamado."' , 
            WHERE id_compra = '".$id_compra."'";

        insert_bd($query, DATABASE_GESTION, "'se ha ingresado un registro de gestión de compra'");

    }

    function insert_items_compra_bd($items_compra, $id_compra){

        foreach($items_compra as $item){

            $atributos_item = $item->attributes();
            $nro_item = $atributos_item->nro_item;

            if($atributos_item->fecha_hora_puja != NULL){
                $atributos_item->fecha_hora_puja = cadena_fecha_hora($atributos_item->fecha_hora_puja);
            }

            $query = "INSERT INTO items_compra ( id_compra";
            
            $values = " ) VALUES ( '".$id_compra."'";

            foreach($atributos_item as $a => $b){
                
                $query = $query." , ".$a;

                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
            }

            $query = $query.$values." )";

            insert_bd($query, DATABASE_COMPRAS, "'ítem'");

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
    
                $query = $query." , ".$a;

                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
                
            }

            $query = $query.$values." )";

            insert_bd($query, DATABASE_COMPRAS, "'atributo de ítem'");

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


                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
            }

            $query = $query.$values.' )';
            
            insert_bd($query, DATABASE_COMPRAS, "'valor de atributo de ítem'");

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

                if(strpos($b, '"') !== FALSE){
                    if(strpos($b, "'") !== FALSE){
                        $b = str_replace("'", " ", $b);
                    }
                    $values = $values." , '".$b."'";
                } else {
                    $values = $values.' , "'.$b.'"';
                }
            }

            $query = $query.$values.' )';

            insert_bd($query, DATABASE_COMPRAS, "'modificación de compra'");

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
                if(strpos($b, '"') !== FALSE){
                    if(strpos($b, "'") !== FALSE){
                        $b = str_replace("'", " ", $b);
                    }
                    $values = $values." , '".$b."'";
                } else {
                    $values = $values.' , "'.$b.'"';
                }
            }

            $query = $query.$values.' )';

            insert_bd($query, DATABASE_COMPRAS, "'aclaración de compra'");
        }

    }

    /////////////////////////////////////////////////////////////////////////////////////
    //Actualizador de adjudicaciones

    function actualizador_adj($fecha_inicio, $fecha_fin){

        print_r("Se actualizará la lista de adjudicaciones. ".date('Y-m-d H:i:s', time()).".\n");

        $fecha_actualizacion = date('Y-m-d H:i:s');

        $sql = sql_con(DATABASE_GESTION);

        $query = "SELECT id_compra FROM gestion_compras WHERE estado_compra != '7' AND estado_compra != '17' AND estado_compra != '27'";

        $g = $sql->query($query); 

        $query = "SELECT id_compra FROM gestion_compras WHERE estado_compra = '7' OR estado_compra = '17' OR estado_compra = '27'";

        $q = $sql->query($query); 
 
        mysqli_close($sql);  

        $id_compras_bd = Array();
        $id_compras_adj = Array();

        if($g){
            while($r = $g->fetch_object()){
                $id = $r->id_compra;
                $id_compras_bd[$id] = $id; 
            }
        }

        if($q){
            while($r = $q->fetch_object()){
                $id = $r->id_compra;
                $id_compras_adj[$id] = $id; 
            }
        }

        $url = formar_URL('a', $fecha_inicio, $fecha_fin);

        $xml = simplexml_load_file($url);

        $compras = $xml->reporte_dato->children();

        foreach($compras as $compra){

            $attributes = $compra->attributes();
            $oferentes = $compra->oferentes->children();
            $adjudicaciones = $compra->adjudicaciones->children();
            $aclaraciones_adj = $compra->aclaraciones_adj->children();

            $id_compra = $attributes['id_compra']; //optimizar esto

            if((!array_key_exists((string)$id_compra, $id_compras_adj)) && (!array_key_exists((string)$id_compra, $id_compras_bd))){

                insert_compra_bd($compra, $id_compra);

                insert_compra_adj($id_compra, $attributes->estado_compra, $attributes->fecha_pub_adj);

                if($oferentes != NULL){
                    insert_oferentes_adj($oferentes, $id_compra);
                }
    
                if($adjudicaciones != NULL){
                    insert_item_adj($adjudicaciones, $id_compra);
                }

            } else if(!array_key_exists((string)$id_compra, $id_compras_adj)){
                
                update_compra_bd($compra, $id_compra);

                update_compra_adj($id_compra, $attributes->estado_compra, $attributes->fecha_pub_adj);

                if($oferentes != NULL){
                    insert_oferentes_adj($oferentes, $id_compra);
                }
    
                if($adjudicaciones != NULL){
                    insert_item_adj($adjudicaciones, $id_compra);
                }
            
            } else {

                update_compra_adj($id_compra, $attributes->estado_compra, $attributes->fecha_pub_adj);
                update_compra_bd($compra, $id_compra);
            
            }

            if($aclaraciones_adj != NULL){
                insert_aclaraciones_adj($aclaraciones_adj, $id_compra);
            }
        }

        set_fecha_ult_act_bd($fecha_actualizacion, 'adjs');
        print_r("Se actualizó la lista de adjudicaciones. ".date('Y-m-d H:i:s', time()).".\n");
    }

    function insert_compra_adj($id_compra, $estado_compra, $fecha_ult_mod){

        $fecha_ult_mod = cadena_fecha_hora($fecha_ult_mod);

        $query = "
            INSERT INTO gestion_compras 
            ( id_compra , estado_compra , fecha_ult_mod , estado_interno , valoracion_interna)
            VALUES ( '".$id_compra."' , '".$estado_compra."' , '".$fecha_ult_mod."' , '0' , '0' ) ";
        
        insert_bd($query, DATABASE_GESTION, "'se agrego una adjudicacion de compra en gestion.'");

    }

    function update_compra_adj($id_compra, $estado_compra, $fecha_ult_mod){

        $fecha_ult_mod = cadena_fecha_hora($fecha_ult_mod);

        $query = "
            UPDATE gestion_compras 
            SET 
                estado_compra = '".$estado_compra."' ,
                fecha_ult_mod = '".$fecha_ult_mod."' 
            WHERE 
                id_compra = '".$id_compra."'";
        
        insert_bd($query, DATABASE_GESTION, "'se agrego una adjudicacion de compra en gestion.'");

    }

    function insert_item_adj($items_adj, $id_compra){

        $adjudicacion = false;

        foreach($items_adj as $item_adj){

            $item_attributes = $item_adj->attributes();

            $nro_item = $item_attributes->nro_item;
                
            $query = 'INSERT INTO items_adjudicacion ( id_compra';

            $values = ' ) VALUES ( "'.$id_compra.'"';

            foreach($item_attributes as $a => $b){
                
                $query = $query." , ".$a;
                if(strpos($b, '"') !== FALSE){
                    if(strpos($b, "'") !== FALSE){
                        $b = str_replace("'", " ", $b);
                    }
                    $values = $values." , '".$b."'";
                } else {
                    $values = $values.' , "'.$b.'"';
                }
    

            }

            $query = $query.$values.' )';

            insert_bd($query, DATABASE_COMPRAS, "'de ítem de adjudicación'");

            $atributos_item_adj = $item_adj->atributos_adjudicacion->children();

            if($atributos_item_adj != NULL){
                insert_att_item_adj($atributos_item_adj, $id_compra, $nro_item);
            }

            if( $item_adj->tipo_doc_prov == 'R' && $item_attributes->nro_doc_prov == '215036060012' ){
                $adjudicacion = true;
            }

        }

        if($adjudicacion){

            $query = "UPDATE gestion_compras SET estado_interno = 5 WHERE id_compra = '".$id_compra."'";

            insert_bd($query, DATABASE_GESTION, "'de ítem de adjudicación'");

        }

    }

    function insert_att_item_adj($atributos_item_adj, $id_compra, $nro_item){
        
        $nro_atributo_item = 0;
        
        foreach($atributos_item_adj as $atributo){

            $attributes = $atributo->attributes();

            $query = 'INSERT INTO atributos_items_adjudicacion ( id_compra , nro_item , nro_atributo_item';

            $values = ' ) VALUES ( "'.$id_compra.'" , '.$nro_item.' , '.$nro_atributo_item;

            foreach($attributes as $a => $b){

                $query = $query.' , '.$a;
                    
                if($a == 'valor_fecha'){
                    $b = cadena_fecha($b);
                }

                if(strpos($b, '"') !== FALSE){
                    if(strpos($b, "'") !== FALSE){
                        $b = str_replace("'", " ", $b);
                    }
                    $values = $values." , '".$b."'";
                } else {
                    $values = $values.' , "'.$b.'"';
                }
            }

            $query = $query.$values.' )';

            insert_bd($query, DATABASE_COMPRAS, "'Atributo de ítem de adjudicación'");

            $nro_atributo_item++;
        }
    }

    function insert_oferentes_adj($oferentes, $id_compra){

        foreach($oferentes as $oferente){

            $attributes = $oferente->attributes();

            $query = 'INSERT INTO oferentes ( id_compra';
            $values = ' ) VALUES ( "'.$id_compra.'"';  
            
            foreach($attributes as $a => $b){

                $query = $query." , ".$a;

                if(strpos($b, '"') !== FALSE){
                    if(strpos($b, "'") !== FALSE){
                        $b = str_replace("'", " ", $b);
                    }
                    $values = $values." , '".$b."'";
                } else {
                    $values = $values.' , "'.$b.'"';
                }
            
            }

            $query = $query.$values.' )';

            insert_bd($query, DATABASE_COMPRAS, "'Atributo de oferentes de adjudicación'");
        }
    }

    function insert_aclaraciones_adj($aclaraciones, $id_compra){

        $sql = sql_con(DATABASE_COMPRAS);

        $query = "DELETE FROM aclaraciones_adjudicacion WHERE id_compra = '".$id_compra."'";

        foreach($aclaraciones as $aclaracion){

            $attributes = $aclaracion->attributes();

            $query = 'INSERT INTO aclaraciones_adjudicacion ( id_compra';
            $values = ' ) VALUES ( "'.$id_compra.'"';  
            
            foreach($attributes as $a => $b){

                $query = $query.' , '.$a;

                if (in_array($a, FECHAHORA)){
                    $b = formatear_fecha_hora($b);
                    $values = $values.' , "'.$b.'"';
                } else {
                    if(strpos($b, '"') !== FALSE){
                        if(strpos($b, "'") !== FALSE){
                            $b = str_replace("'", " ", $b);
                        }
                        $values = $values." , '".$b."'";
                    } else {
                        $values = $values.' , "'.$b.'"';
                    }
                }
            }
            
            $query = $query.$values." )";

            insert_bd($query, DATABASE_COMPRAS, "'Atributo de aclaracion de adjudicación'");
        }
    }

    function format_insert_query($attributes, $query, $values){

        $query = "INSERT INTO";
        $values = $values;
                        
        foreach($attributes as $a => $b){

            $query = $query." , ".$a;

            if(in_array($a, NUMERICO)){
                $values = $values." , ".$b;
            } else {
                if(in_array($a, FECHAHORA)){
                    $b = cadena_fecha($b);
                }
                $values = $values." , '".$b."'";

            }
        }

    }

    function codigueras_filtro(){

        $sql = new mysqli('localhost', 'root', '', '');

        $_SESSION['incisos'] = Array();
        $_SESSION['ues'] = Array();
        $_SESSION['tipos_compra'] = Array();
        $_SESSION['subtipos_compra'] = Array();
        $_SESSION['familias'] = Array();
        $_SESSION['subfamilias'] = Array();
        $_SESSION['clases'] = Array();
        $_SESSION['subclases'] = Array();
        $_SESSION['tipos_doc'] = Array();

        $q = $sql->query("SELECT * FROM codigueras_arce.incisos as incisos");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['incisos'][$r->id_inciso] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM codigueras_arce.unidades_ejecutoras");
        
        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['ues'][$r->id_inciso][$r->id_ue] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM codigueras_arce.tipos_compra");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['tipos_compra'][$r->id_tipo_compra] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM codigueras_arce.subtipos_compra");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['subtipos_compra'][$r->id_tipocompra][$r->id_subtipo] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM catalogo_arce.familias");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['familias'][$r->comprable][$r->cod] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM catalogo_arce.subflias");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['subfamilias'][$r->fami_cod][$r->cod] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM catalogo_arce.clases");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['clases'][$r->fami_cod][$r->subf_cod][$r->cod] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM catalogo_arce.subclases");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['subclases'][$r->fami_cod][$r->subf_cod][$r->clas_cod][$r->cod] = $r;
            }
        }

        $q = $sql->query("SELECT * FROM codigueras_arce.tipos_doc ORDER BY tipo ASC");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['tipos_doc'][$r->tipo] = $r;
            }
        }

        mysqli_close($sql);

    }

    // FUNCIONES DE MANEJO DE DIRECTORIOS Y ARCHIVOS

    function get_fecha_ultimo_respaldo(){

        $sql = sql_con(DATABASE_GESTION);

        $q = $sql->query('
            SELECT fecha_respaldada
            FROM respaldos 
            WHERE contador = (SELECT MAX(contador) FROM gestion_bd_sandbox.respaldos)
        ');

        $fecha_ult_respaldo = '';

        if($q){

            while($r = $q->fetch_object()){
                $fecha_ult_respaldo = $r->fecha_respaldada;
            }

        } else {
            $fecha_ult_respaldo = '1970-01-01';
        }

        mysqli_close($sql);

        return $fecha_ult_respaldo;
    } 

    function get_registros_fecha(){

        $sql = sql_con();

        $hoy = date('Y-m-d');

        $ayer = date('Y-m-d', strtotime($hoy.' - 1 DAYS'));

        $query = 'SELECT * FROM gestion_bd_sandbox.gestion_compras ';

        echo $query.'<br>';

        $q = $sql->query($query);

        $result = Array();
        $texto = '';

        if($q){
            while($r = $q->fetch_array(MYSQLI_ASSOC)){
                $cache = Array();
                foreach($r as $a => $b){
                    $cache[$a] = $b;
                }
                $result[] = $cache;
            }
        }

        foreach($result as $r){

            $nombre = Array();
            $valor = Array();

            foreach($r as $a => $b){

                $nombre[] = $a;
                $valor[] = $b;

            }

            $query = 'INSERT INTO gestion_bd_sandbox.gestion_compras ( id_compra';

            $values = ' ) VALUES ( "'.$valor[0].'"';

            for( $i=1 ; $i<count($nombre) ; $i++){

                $query .= ' , '.$nombre[$i];
                
                $values .= ' , "'.$valor[$i].'"';

            };

            $query .= $values." )\n";

            $texto .= $query;

        }

        $file = fopen('testfile.txt', 'w');

        $fwrite = fwrite($file, $texto);

        $q = $sql->query('INSERT INTO gestion_bd_sandbox.respaldos (fecha_respaldada, fecha_hora_ejecucion) VALUES ( "'.$hoy.'" , "'.date('Y-m-d H:i:s').'" )');

        echo "Se realizó el respaldo de la base de datos GESTION_COMPRAS";

        mysqli_close($sql);

    }

    function database_exists($schema){

        $sql = sql_con();
        
        $q = $sql->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE '%".$schema."%'");

        $result = Array();

        if($q){

            while($r = $q->fetch_object()){

                $result[] = $r->SCHEMA_NAME;

            }
        }

        $return = Array();

        if(count($result) > 0){

            foreach($result as $r){

                $return[] = $r;

            }

            return $return;

        } else {

            return false;

        }

    }

    function create_database_compras_anio($anio){

        $sql = sql_con();

        $script = "CREATE DATABASE gestor_compras_estatales_".$anio;

        $q = $sql->query($script);

        mysqli_close($sql);

        $sql = sql_con("gestor_compras_estatales_".$anio);

        $scripts = Array();

        $scripts[] = "CREATE TABLE `compras` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `id_inciso` INT(2) NULL DEFAULT NULL , `id_ue` INT(3) NULL DEFAULT NULL , `id_ucc` INT(2) NULL DEFAULT NULL , `num_compra` VARCHAR(50) NULL DEFAULT NULL , `anio_compra` INT(4) NULL DEFAULT NULL , `nro_ampliacion` INT NULL DEFAULT NULL , `estado_compra` INT NULL DEFAULT NULL , `nombre_pliego` VARCHAR(200) NULL DEFAULT NULL , `fecha_publicacion` DATETIME NULL DEFAULT NULL , `fecha_ult_mod_llamado` DATETIME NULL DEFAULT NULL , `id_tipocompra` CHAR(2) NULL DEFAULT NULL , `subtipo_compra` CHAR(3) NULL DEFAULT NULL , `objeto` VARCHAR(2000) NULL DEFAULT NULL , `fecha_hora_apertura` DATETIME NULL DEFAULT NULL , `lugar_apertura` VARCHAR(200) NULL DEFAULT NULL , `fecha_sol_prorr` DATE NULL DEFAULT NULL , `fecha_sol_aclar` DATE NULL DEFAULT NULL , `fecha_hora_tope_entrega` DATETIME NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `lugar_entrega` VARCHAR(200) NULL DEFAULT NULL , `precio_pliego` FLOAT(15) NULL DEFAULT NULL , `id_moneda_pliego` INT(2) NULL DEFAULT NULL , `lugar_compra_pliego` VARCHAR(200) NULL DEFAULT NULL , `nombre_contacto` VARCHAR(200) NULL DEFAULT NULL , `fax_contacto` VARCHAR(50) NULL DEFAULT NULL , `email_contacto` VARCHAR(50) NULL DEFAULT NULL , `fecha_pub_adj` DATETIME NULL DEFAULT NULL , `fecha_compra` DATE NULL DEFAULT NULL , `fecha_vigencia_adj` DATE NULL DEFAULT NULL , `fondos_rotatorios` CHAR(1) NOT NULL DEFAULT 'N' , `apel` CHAR(1) NOT NULL DEFAULT 'N' , `arch_adj` VARCHAR(200) NULL DEFAULT NULL , `monto_adj` FLOAT(15) NULL DEFAULT NULL , `id_moneda_monto_adj` INT(2) NULL DEFAULT NULL , `id_tipo_resol` INT(3) NULL DEFAULT NULL , `num_resol` INT(9) NULL DEFAULT NULL , `es_reiteracion` CHAR(1) NOT NULL DEFAULT 'N' , `arch_reiteracion` VARCHAR(200) NULL DEFAULT NULL, PRIMARY KEY (`id_compra`(9))) ENGINE = InnoDB;";

        $scripts[] = "CREATE TABLE `items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `cant_pedida` FLOAT(15) NULL DEFAULT NULL , `id_moneda_cotiz` INT(2) NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `duracion_puja` INT(2) NULL DEFAULT NULL , `tipo_margen_puja` VARCHAR(1) NULL DEFAULT NULL , `margen_puja` INT(13) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL ) ENGINE = InnoDB;";

        $scripts[] = "CREATE TABLE `atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `requerido` VARCHAR(1) NOT NULL DEFAULT 'N' , `cod_condicion` VARCHAR(2) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' ) ENGINE = InnoDB;";
        
        $scripts[] = "CREATE TABLE `valores_atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `nro_valor_atributo_item` INT NULL DEFAULT NULL ,`id_prop_atributo` INT(4) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL ) ENGINE = InnoDB;";
        
        $scripts[] = "CREATE TABLE `oferentes` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL , `cant_adj` FLOAT(15) NULL DEFAULT NULL , `precio_unit` FLOAT(15) NULL DEFAULT NULL , `precio_tot_imp` FLOAT(15) NULL DEFAULT NULL , `id_moneda` INT(2) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL , `variacion` VARCHAR(600) NULL DEFAULT NULL ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `atributos_items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `historial_modificaciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `campo` VARCHAR(30) NULL DEFAULT NULL , `valor_anterior` VARCHAR(200) NULL DEFAULT NULL , `valor_nuevo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `aclaraciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` TEXT NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `aclaraciones_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` TEXT NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
        
        $scripts[] = "CREATE TABLE `requerimientos_compra` ( `id_compra` VARCHAR(9) NOT NULL ,`nro_requerimiento` INT(3) NOT NULL ,`fecha_alta` DATETIME NOT NULL ,`funcionario_alta` INT(5) NOT NULL ,`obligatorio` BOOLEAN NULL DEFAULT NULL ,`tipo` INT(3) NOT NULL ,`estado` INT(3) NULL DEFAULT NULL ,`funcionario_cumplido` INT(5) NULL DEFAULT NULL ,`fecha_cumplido` DATETIME NULL DEFAULT NULL ,`descripcion` VARCHAR(500) NULL DEFAULT NULL ,`archivo_adjunto` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB;";
        
        $scripts[] = "CREATE TABLE `visitas_obligatorias_compra` ( `id_compra` VARCHAR(10) NOT NULL ,`nro_requerimiento` INT(3) NOT NULL ,`fecha_inicio` DATETIME NOT NULL ,`fecha_fin` DATETIME NOT NULL ,`lugar_visita` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB;";
        
        foreach($scripts as $script){

            $q = $sql->query($script);

        }

        mysqli_close($sql);
    }


?>

