<?php

    include 'listas.php';
    include 'clases.php';

    date_default_timezone_set("America/Montevideo");

    define('LOG_IN', 'ingreso.php');

    define('DATABASE_COMPRAS', 'gestor_compras_estatales_sandbox');
    define('DATABASE_GESTION', 'gestion_bd_sandbox');
    define('DATABASE_CODIGUERAS', 'codigueras_arce');
    define('DATABASE_ACTUALIZADOR', 'gestion_bd_sandbox');
    define('GESTION_USUARIOS', 'gestion_usuarios');
    
    define('DIRECCION', '/gestor_compras/');
    define('URL_WS', 'https://www.comprasestatales.gub.uy/comprasenlinea/jboss/generarReporte?');
    define('URL_COMPRA', 'https://www.comprasestatales.gub.uy/consultas/detalle/mostrar-llamado/1/id/');
    define('URL_PLIEGO', 'https://www.comprasestatales.gub.uy/Pliegos/');
    define('URL_ACLARACION', 'https://www.comprasestatales.gub.uy/Aclaraciones/');
    
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
    
    session_start();

    function sesion_sistema(){
        $_SESSION['sistema'] = new Sistema();
    }

    if(!isset($_SESSION['sistema'])){
        codigueras_filtro();
        sesion_sistema();
    } else {
        // $_SESSION['sistema']->show_ip();
    }

    if(isset($_SESSION['user'])){
		$_SESSION['user']->marca_reloj();
		if(!$_SESSION['user']->bool_session && ($_SERVER['REQUEST_URI'] !== '/gestor_compras/ingreso.php' || $_SERVER['REQUEST_URI'] !== '/GESTOR_COMPRAS/ingreso.php')){
			$_SESSION['user'] = NULL;
			header("Location: ".LOG_IN);
		}
	} else if($_SERVER['REQUEST_URI'] !== '/gestor_compras/ingreso.php') {
        header("Location: ".LOG_IN);
    }

    ///////////////////////////
    //Funciones de fecha y hora

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

    ////////////////////////////
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

    ////////////////////////////////////////////////////
    // Funciones referidas a la gestion del actualizador

    function formar_URL($tipo_publicacion, $fecha_inicio, $fecha_fin){

        $fecha_fin = date('Y-m-d', strtotime($fecha_fin.' + 1 day'));

        $tipo_publicacion = '&tipo_publicacion='.$tipo_publicacion;
        
        $tipo_compra = '&tipo_compra=';

        $rango_fecha = '&rango-fecha='.date('d' , strtotime($fecha_inicio)).'%2F'.date('m' , strtotime($fecha_inicio)).'%2F'.date('Y' , strtotime($fecha_inicio)).'+-+'.date('d' , strtotime($fecha_fin)).'%2F'.date('m' , strtotime($fecha_fin)).'%2F'.date('Y' , strtotime($fecha_fin)).'&';

        $url = URL_WS.$tipo_publicacion.$tipo_compra.$rango_fecha.'&dia_inicial='.date('j' , strtotime($fecha_inicio)).'&mes_inicial='.date('n' , strtotime($fecha_inicio)).'&anio_inicial='.date('Y' , strtotime($fecha_inicio)).'&hora_inicial=0&dia_final='.date('j' , strtotime($fecha_fin)).'&mes_final='.date('n' , strtotime($fecha_fin)).'&anio_final='.date('Y' , strtotime($fecha_fin)).'&hora_final=0';

        return $url;
    }

    function actualizador($fecha_inicial, $fecha_final){

        if((strtotime($fecha_final.' 00:00:00') - strtotime($fecha_inicial.' 00:00:00')) > 604800){
            
            $fecha_intermedia = date('Y-m-d H:i:s', strtotime($fecha_inicial.' + 604799 SECONDS' ));

            actualizador_compras($fecha_inicial, $fecha_intermedia);

            actualizador_adj($fecha_inicial, $fecha_intermedia);
            
            $fecha_intermedia = date('Y-m-d', strtotime($fecha_intermedia.' + 1 SECONDS'));

            actualizador($fecha_intermedia, $fecha_final);

        } else {

            actualizador_compras($fecha_inicial, $fecha_final);

            actualizador_adj($fecha_inicial, $fecha_final);
        }

        set_fecha_ult_act_bd(date('Y-m-d H:i:s'));

    }

    function set_fecha_ult_act_bd($fecha){
        
        $sql = sql_con(DATABASE_GESTION);

        do {
            $q = $sql->query("INSERT INTO fecha_ult_actualizacion (fecha_ult_act) VALUES ('".$fecha."')");
        } while (!$q);


        mysqli_close($sql);

    }

    function get_fecha_ult_act_bd(){

        $sql = sql_con(DATABASE_ACTUALIZADOR);

        $q = $sql->query('SELECT fecha_ult_act FROM fecha_ult_actualizacion WHERE contador = (SELECT MAX(contador) FROM fecha_ult_actualizacion)');
    
        $fecha_ult_act;
    
        while($r = $q->fetch_object()){
            $fecha_ult_act = $r->fecha_ult_act;
        }

        mysqli_close($sql);

        return $fecha_ult_act;
    }
    
    function actualizador_compras($fecha_inicial, $fecha_fin){

        // 1) obtener todos los identificadores de compra de la base de datos

        $url = formar_URL('l', $fecha_inicial, $fecha_fin);

        echo $url.'<br>';

        $sql = sql_con(DATABASE_GESTION);

        $query = "SELECT id_compra FROM compras_adjudicadas";

        $q = $sql->query($query);  

        $query = "SELECT id_compra FROM gestion_compras";

        $g = $sql->query($query);  
 
        mysqli_close($sql);  

        $id_compras_bd = Array();
        $id_compras_adj = Array();

        if($q){
            while($r = $q->fetch_object()){
                $id = $r->id_compra;
                $id_compras_bd[$id] = $id; 
            }
        }

        if($g){
            while($r = $g->fetch_object()){
                $id = $r->id_compra;
                $id_compras_adj[$id] = $id; 
            }
        }

        // 2) Obtener todas las compras agregadas o actualizadas en el periodo de tiempo predeterminado en el portal de ARCE

        $xml = simplexml_load_file($url);

        $compras = $xml->reporte_dato->children();

        foreach($compras as $compra){

            // 3) Por cada compra obtenida, desglosar el identificador
            $attributes = $compra->attributes();

            $id_compra = $attributes['id_compra'];

            if((!array_key_exists((string)$id_compra, $id_compras_bd)) && (!array_key_exists((string)$id_compra, $id_compras_adj))){
                // 4.1) Si no existe la id en la base de datos se ingresa la compra y sus items

                insert_compra_bd($compra, $id_compra);
                
                $items_compra = $compra->items->children();

                if($items_compra != NULL){
                    insert_items_compra_bd($items_compra, $id_compra);
                }

                // 4.1.1) Aquí se ingresa el registro de gestión de compra también

                insert_gestion_compra_bd($id_compra);

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
    }

    function botones_compra($id, $bool){

        $estructura = '';

        $item = $_SESSION['sistema']->seleccion_llamados[$id];
        
        if($item->estado_compra==4){

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
    
    ////////////
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

    //////////////////////////
    // Actualizador de compras 
    
    
    function insert_bd($query, $db, $string){

        $sql = sql_con($db);

        $q = $sql->query($query);

        $return = '';

        if($q){
            $return = 'Se ha ingresado exitosamente: '.$string.'<br>';
        } else {
            $return = 'Ha ocurrido un problema.<br>';
        }

        mysqli_close($sql);

    }

    function insert_compra_bd($compra, $id_compra){

        $atributos_compra = $compra->attributes();     

        $query = "INSERT INTO compras ( id_compra";
        $values = " ) VALUES ( '".$id_compra."'";

        foreach($atributos_compra as $a => $b){

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

        echo $query.'<br>';
        
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

        echo $query.'<br>';

        insert_bd($query, DATABASE_COMPRAS, "'SE HA ACTUALIZADO UNA COMPRA'");

        $query = "UPDATE gestion_compras SET estado_compra = ".$compra->estado_compra." , fecha_ult_mod = '".$compra->fecha_ult_mod_llamado."' WHERE id_compra = '".$id_compra."'";

        insert_bd($query, DATABASE_GESTION, "'SE HA ACTUALIZADO UNA COMPRA'");

    }

    function insert_gestion_compra_bd($id_compra){

        $query = "INSERT INTO gestion_compras ( id_compra , estado_interno, valoracion_interna ) VALUES ( '".$id_compra."' , 0 , 0 )";

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

    ////////////////////////////////
    //Actualizador de adjudicaciones

    function actualizador_adj($fecha_inicio, $fecha_fin){

        $sql = sql_con(DATABASE_GESTION);

        $query = "SELECT id_compra FROM compras_adjudicadas";

        $q = $sql->query($query);  

        $query = "SELECT id_compra FROM gestion_compras";

        $g = $sql->query($query);  
 
        mysqli_close($sql);

        $url = formar_URL('a', $fecha_inicio, $fecha_fin);

        $id_compras_bd = Array();
        $id_compras_adj = Array();

        if($q){
            while($r = $q->fetch_object()){
                $id = $r->id_compra;
                $id_compras_adj[$id] = $id; 
            }
        }

        if($g){
            while($r = $g->fetch_object()){
                $id = $r->id_compra;
                $id_compras_bd[$id] = $id; 
            }
        }

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

                insert_compra_adj($id_compra, $attributes->id_tipo_resol, $attributes->fecha_pub_adj);

                if($oferentes != NULL){
                    insert_oferentes_adj($oferentes, $id_compra);
                }
    
                if($adjudicaciones != NULL){
                    insert_item_adj($adjudicaciones, $id_compra);
                }

            } else if(!array_key_exists((string)$id_compra, $id_compras_adj)){
                
                update_compra_bd($compra, $id_compra);

                insert_compra_adj($id_compra, $attributes->id_tipo_resol, $attributes->fecha_pub_adj);

                if($oferentes != NULL){
                    insert_oferentes_adj($oferentes, $id_compra);
                }
    
                if($adjudicaciones != NULL){
                    insert_item_adj($adjudicaciones, $id_compra);
                }
            
            } else {

                update_compra_bd($compra, $id_compra);
            
            }

            if($aclaraciones_adj != NULL){
                insert_aclaraciones_adj($aclaraciones_adj, $id_compra);
            }
        }
    }

    function insert_compra_adj($id_compra, $id_tipo_resol, $fecha_pub_adj){

        $fecha_pub_adj = cadena_fecha_hora($fecha_pub_adj);

        $query = "INSERT INTO compras_adjudicadas ( id_compra , id_tipo_resol, fecha_pub_adj ) VALUES ( '".$id_compra."' , ".$id_tipo_resol." , '".$fecha_pub_adj."' )";
        
        insert_bd($query, DATABASE_GESTION, "'se agrego una adjudicacion de compra en gestion.'");

    }

    function insert_item_adj($items_adj, $id_compra){

        foreach($items_adj as $item_adj){

            $attributos_item = $item_adj->attributes();

            $nro_item = $attributos_item->nro_item;
                
            $query = 'INSERT INTO items_adjudicacion ( id_compra';

            $values = ' ) VALUES ( "'.$id_compra.'"';

            foreach($attributos_item as $a => $b){
                
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

    //FUNCIONES DE CACHE 


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
        $_SESSION['monedas'] = Array();

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

        $q = $sql->query("SELECT * FROM codigueras_arce.monedas ORDER BY id_moneda ASC");

        if($q){
            while($r = $q->fetch_object()){
                $_SESSION['monedas'][$r->id_moneda] = $r;
            }
        }

        $_SESSION['magnitudes'] = Array( "UNIDAD" , "METRO LINEAL" , "LITRO" , "METRO CUADRADO" , "KILOGRAMO" );

        mysqli_close($sql);

    }
 

    // OFERTAR

    function get_rubros_oferta($id_oferta){
       
        $sql = sql_con();
        $q = $sql->query("SELECT * FROM gestion_bd_sandbox.rubros_oferta WHERE id_oferta = '".$id_oferta."'");
        mysqli_close($sql);
        $result = Array();

        if($q){
            while($r = $q->fetch_object()){
                $result[$r->nro_item] = $r;
            }
        }

        return $result;
    }

    function show_rubros_oferta($rubros){

        $return = '';
        
        if( $rubros != NULL && count($rubros) > '0'){
            
            $return .= "<table id='tabla_rubros'>
                <tr>
                    <th>Ítem N°</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Magnitud</th>
                    <th>Costo unitario s/IVA</th>
                    <th>Costo unitario c/IVA</th>
                    <th>Costo total IVA incluído</th>
                </tr>";
            
            foreach($rubros as $rubro){


                $moneda = $_SESSION['monedas'][$rubro->id_moneda]->sigla_moneda;
                $costo_rubro = $moneda.' '.$rubro->costo_s_iva;
                $costo_impuestos = (int)$rubro->costo_s_iva * 1.22;
                $costo_impuestos_txt = $moneda.$costo_impuestos;
                $costo_total = $costo_impuestos * $rubro->cantidad;
                $total = $moneda.$costo_total;
                $magnitud = $_SESSION['magnitudes'][$rubro->magnitud];

            $return .= "<tr>
                    <th>".$rubro->nro_item."</th>
                    <th>".$rubro->descripcion."</th>
                    <th>".$rubro->cantidad."</th>
                    <th>".$magnitud."</th>
                    <th>".$costo_rubro."</th>
                    <th>".$costo_impuestos_txt."</th>
                    <th>".$total."</th>
                </tr>";
            }
            
            $return .= "</table>";
        }

        return $return;
    }

?>