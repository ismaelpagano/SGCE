<?php

    class Actualizador {


        public string $database_gestion = 'gestion_bd';
        public $database_anios = Array();
        public $anios = Array();
        public $fecha_primer_act_bd = '';
        public $fecha_ult_act_bd = '';

        public function __construct(){

            // $this->get_databases_compras_anio();

            $this->actualizador_master();

            // print_r($this->actualizacion_fechas());

        }

        public function main(){

        }

        public function sql_con($db = ''){
            $sql = new mysqli('localhost', 'root', '', $db);
            $sql->set_charset('utf8');
        
            return $sql;
        }

        public function actualizador_master(){

            $fecha_actual = date('Y-m-d H:i:s');
    
            // $fecha_ult_respaldo = $this->get_fecha_ultimo_respaldo();
    
            if((int)date('H', strtotime($fecha_actual)) >= 7){

                $fechas = $this->get_fechas_actualizacion();
    
                if($this->fecha_ult_act_bd == '' && $this->fecha_primer_act_bd == ''){
                    $this->fecha_ult_act_bd = '2023-02-24 00:00:00';
                    $this->fecha_primer_act_bd = date('Y-m-d H:i:s');
                }
    
                $fecha_final = date('Y-m-d H:i:s');
    
                if((strtotime($fecha_final) - strtotime($this->fecha_ult_act_bd)) > 604800){
                
                    $fecha_intermedia = date('Y-m-d H:i:s', strtotime($this->fecha_ult_act_bd.' + 604799 SECONDS' ));
        
                    $this->actualizador_compras($this->fecha_ult_act_bd, $fecha_intermedia);
        
                    $this->actualizador_master();
        
                } else {
        
                    $this->actualizador_compras($this->fecha_ult_act_bd, $fecha_final);
        
                    // actualizador_adj($fecha_inicial, $fecha_final);
                }
    
            } else if ((strtotime($fecha_actual)) - strtotime($fecha_ult_respaldo) > 172800) {

                // Acá se haría el respaldo cuando la hora es menor a las 7 AM y la fecha actual tiene una diferencia de mas de 2 días con respecto al respaldo anterior
    
            }
    
        }

        private function xml_request($tipo_publicacion, $fecha_inicio, $fecha_fin){

            $fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio.' - 1 HOUR'));

            $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin.' + 1 HOUR'));

            $tipo_publicacion = '&tipo_publicacion='.$tipo_publicacion;
            
            $tipo_compra = '&tipo_compra=';
    
            $rango_fecha = '&rango-fecha='.date('d' , strtotime($fecha_inicio)).'%2F'.date('m' , strtotime($fecha_inicio)).'%2F'.date('Y' , strtotime($fecha_inicio)).'+-+'.date('d' , strtotime($fecha_fin)).'%2F'.date('m' , strtotime($fecha_fin)).'%2F'.date('Y' , strtotime($fecha_fin)).'&';

            $hora_fin = date('G', strtotime($fecha_fin));
    
            $url = URL_WS.$tipo_publicacion.$tipo_compra.$rango_fecha.'&dia_inicial='.date('j' , strtotime($fecha_inicio)).'&mes_inicial='.date('n' , strtotime($fecha_inicio)).'&anio_inicial='.date('Y' , strtotime($fecha_inicio)).'&hora_inicial='.date('G', strtotime($fecha_inicio)).'&dia_final='.date('j' , strtotime($fecha_fin)).'&mes_final='.date('n' , strtotime($fecha_fin)).'&anio_final='.date('Y' , strtotime($fecha_fin)).'&hora_final='.$hora_fin;

            $xml = simplexml_load_file($url);

            echo $url.'<br>';

            $return = $xml->reporte_dato->children();

            return $return;
        }

        private function get_fechas_actualizacion(){

            $sql = $this->sql_con('gestion_bd');

            $q = "SELECT fecha_desde FROM actualizaciones_bd WHERE instancia = (SELECT MIN(instancia) FROM actualizaciones_bd)";

            $q = $sql->query($q);

            if($q){

                while($r = $q->fetch_object()){
                    $this->fecha_primer_act_bd = $r->fecha_desde;
                }

            }

            $q = "SELECT fecha_hasta FROM actualizaciones_bd WHERE instancia = (SELECT MAX(instancia) FROM actualizaciones_bd)";

            $q = $sql->query($q);

            if($q){

                while($r = $q->fetch_object()){
                    $this->fecha_ult_act_bd = $r->fecha_hasta;
                }

            }

            mysqli_close($sql);

        }

        private function actualizador_compras($fecha_inicial, $fecha_fin){

            $fecha_actual = date('Y-m-d H:i:s');

            $fecha_inicial = date('Y-m-d H:i', strtotime($fecha_inicial.' - 1 HOUR'));

            $fecha_final = date('Y-m-d H:i', strtotime($fecha_fin.' + 1 HOUR'));

            print_r("Se actualizará la lista de compras. ".$fecha_actual.".\n");

            $gestion_compras = Array();

            $sql = $this->sql_con('gestion_bd');

            $q = "SELECT * FROM gestion_compras";

            $q = $sql->query($q);

            if($q){

                while($r = $q->fetch_object()){
                    $gestion_compras[$r->id_compra] = $r;
                }

            }

            mysqli_close($sql);

            // 2) Obtener todas las compras agregadas o actualizadas en el periodo de tiempo predeterminado en el portal de ARCE
    
            $this->ordenar_compras_anio($this->xml_request('l', $fecha_inicial, $fecha_final));

            foreach($this->database_anios as $anio_compras => $compras){

                if(!array_key_exists($anio_compras, $this->anios)){

                    $bool = $this->database_exists('gestor_compras_estatales_'.$anio_compras);
        
                    if(!$bool){
                        $this->create_database_compras_anio($anio_compras);
                    } else {
                        $this->anios[$anio_compras] = '';
                    }
    
                }

                foreach($this->database_anios[$anio_compras] as $compra){

                    $id_compra = (string)$compra['atributos']['id_compra'];
                
                    $claves = Array('id_compra' => $id_compra);
                    $anio = (string)$compra['atributos']['anio_compra'];
                    $this->insert_update_registro_bd_anio($compra['atributos'], 'compras', $claves, $anio);

                    if($compra['items_compra'] != NULL){

                        foreach($compra['items_compra'] as $item){

                            $atributos = $item->attributes();
                            $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item']);
                            $this->insert_update_registro_bd_anio($atributos, 'items_compra', $claves, $anio);

                            $atributos_item = $item->children();

                            if($atributos_item != NULL){

                                $nro_atributo_item = 1;

                                foreach($atributos_item as $atributo_item){

                                    $atributos = $atributo_item->attributes();
                                    $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'nro_atributo_item' => $nro_atributo_item );
                                    $this->insert_update_registro_bd_anio($atributos, 'atributos_items_compra', $claves, $anio);

                                    $valores_atributos_item = $atributo_item->children();

                                    if($valores_atributos_item != NULL){
        
                                        $nro_valor_atributo_item = 1;
        
                                        foreach($valores_atributos_item as $valor_atributo_item){
        
                                            $atributos = $atributo_item->attributes();
                                            $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'nro_atributo_item' => $nro_atributo_item , 'nro_valor_atributo_item' => $nro_valor_atributo_item);
                                            $this->insert_update_registro_bd_anio($atributos, 'valores_atributos_items_compra', $claves, $anio);
            
                                            $nro_valor_atributo_item++;
                                        }
        
                                    }
    
                                    $nro_atributo_item++;
                                }

                            }

                        }

                    }

                    if($compra['modificaciones_compra'] != NULL){

                        $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

                        $q = "DELETE FROM historial_modificaciones_llamado WHERE id_compra = '".$id_compra."'";

                        $q = $sql->query($q);

                        mysqli_close($sql);

                        foreach($compra['modificaciones_compra'] as $modificaciones){

                            $atributos = $modificaciones->attributes(); 

                            $claves = Array( 'id_compra' => $id_compra);
                            $query = $this->insert_update_registro_bd_anio($atributos, 'historial_modificaciones_llamado', $claves, $anio);
                        }

                    }

                    if($compra['aclaraciones_llamado'] != NULL){

                        $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

                        $q = "DELETE FROM aclaraciones_llamado WHERE id_compra = '".$id_compra."'";

                        $q = $sql->query($q);

                        mysqli_close($sql);

                        foreach($compra['aclaraciones_llamado'] as $aclaraciones){

                            $atributos = $aclaraciones->attributes(); 

                            $claves = Array( 'id_compra' => $id_compra );
                            $query = $this->insert_update_registro_bd_anio($atributos, 'aclaraciones_llamado', $claves, $anio);
                        }

                    }

                    // Acá se ingresa o actualiza el registro de gestión de la compra

                    if(!isset($gestion_compras[$id_compra])){
                        
                        $sql = $this->sql_con('gestion_bd');

                        $fecha_publicacion_query = '';
                        $fecha_publicacion = '';

                        $fecha_ult_mod_llamado_query = '';
                        $fecha_ult_mod_llamado = '';

                        if(isset($compra['atributos']['fecha_publicacion'])){

                            $fecha_publicacion_query = ' fecha_publicacion ,';
                            $fecha_publicacion = "'".formatear_fecha_hora($compra['atributos']['fecha_publicacion'])."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';
                            $fecha_ult_mod_arce = $fecha_publicacion;
                        }

                        if(isset($compra['atributos']['fecha_ult_mod_llamado'])){

                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';
                            $fecha_ult_mod_llamado = "'".formatear_fecha_hora($compra['atributos']['fecha_ult_mod_llamado'])."' ,";
                            $fecha_ult_mod_arce = $fecha_ult_mod_llamado;
                        }

                        $q = "INSERT INTO gestion_compras ( id_compra , anio_compra ,".$fecha_publicacion_query.$fecha_ult_mod_llamado_query." fecha_ult_mod_sgce , estado_arce , estado_interno ) VALUES ( '".$id_compra."' , '".$compra['atributos']['anio_compra']."' ,".$fecha_publicacion.$fecha_ult_mod_arce." '".date('Y-m-d H:i:s')."' , '".$compra['atributos']['estado_compra']."' , 0 )";

                        echo $q;
                        
                        $q = $sql->query($q);

                        mysqli_close($sql);

                    }

                }

            }

            foreach($this->database_anios as $a => $b){

                $this->database_anios[$a] = Array();

            }

            $this->ordenar_compras_anio($this->xml_request('a', $fecha_inicial, $fecha_final));

            foreach($this->database_anios as $anio_compras => $compras){

                if(!array_key_exists($anio_compras, $this->anios)){

                    $bool = $this->database_exists('gestor_compras_estatales_'.$anio_compras);
        
                    if(!$bool){
                        $this->create_database_compras_anio($anio_compras);
                    } else {
                        $this->anios[$anio_compras] = '';
                    }
    
                }
                
                foreach($this->database_anios[$anio_compras] as $compra){

                    $id_compra = $compra['atributos']['id_compra'];
                
                    $claves = Array('id_compra' => $id_compra);
                    $anio = (string)$compra['atributos']['anio_compra'];
                    $this->insert_update_registro_bd_anio($compra['atributos'], 'compras', $claves, $anio);

                    if($compra['items_adjudicacion'] != NULL){

                        foreach($compra['items_adjudicacion'] as $item){

                            $atributos = $item->attributes();
                            $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item']);
                            $this->insert_update_registro_bd_anio($atributos, 'items_adjudicacion', $claves, $anio);

                            $atributos_item = $item->children();

                            if($atributos_item != NULL){

                                $nro_atributo_item = 1;

                                foreach($atributos_item as $atributo_item){

                                    $atributos = $atributo_item->attributes();
                                    $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'nro_atributo_item' => $nro_atributo_item );
                                    $this->insert_update_registro_bd_anio($atributos, 'atributos_items_compra', $claves, $anio);
                                    $nro_atributo_item++;
                                }

                            }

                        }

                    }

                    if($compra['aclaraciones_adjudicacion'] != NULL){

                        $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

                        $q = "DELETE FROM aclaraciones_adjudicacion WHERE id_compra = '".$id_compra."'";

                        $q = $sql->query($q);

                        mysqli_close($sql);

                        foreach($compra['aclaraciones_adjudicacion'] as $modificaciones){

                            $atributos = $modificaciones->attributes(); 

                            $claves = Array( 'id_compra' => $id_compra);
                            $this->insert_update_registro_bd_anio($atributos, 'aclaraciones_adjudicacion', $claves, $anio);
                        }

                    }

                    if($compra['oferentes'] != NULL){

                        $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

                        $q = "DELETE FROM oferentes WHERE id_compra = '".$id_compra."'";

                        $q = $sql->query($q);

                        mysqli_close($sql);

                        foreach($compra['oferentes'] as $aclaraciones){

                            $atributos = $aclaraciones->attributes(); 

                            $claves = Array( 'id_compra' => $id_compra );
                            $this->insert_update_registro_bd_anio($atributos, 'oferentes', $claves, $anio);
                        }
                    }
                }
            }

            foreach($this->database_anios as $a => $b){

                $this->database_anios[$a] = Array();

            }

            $sql = $this->sql_con('gestion_bd');

            $q = "INSERT INTO actualizaciones_bd ( fecha_ejecucion , fecha_desde, fecha_hasta ) VALUES ( '".$fecha_actual."' , '".$this->fecha_ult_act_bd."' , '".$fecha_fin."')";

            $q = $sql->query($q);

            mysqli_close($sql);    

            print_r("Se actualizó la lista de compras. ".date('Y-m-d H:i:s').".\n");
        }

        public function ordenar_compras_anio($compras){

            foreach($compras as $compra){
                
                $objetos_compra = Array();
                       
                $objetos_compra['atributos'] = $compra->attributes();
            
                $fecha_ult_mod = '';

                if(isset($objetos_compra['atributos']['fecha_ult_mod_llamado'])){

                    $fecha_ult_mod = formatear_fecha_hora($objetos_compra['atributos']['fecha_ult_mod_llamado']);

                } else {

                    $fecha_ult_mod = formatear_fecha_hora($objetos_compra['atributos']['fecha_publicacion']);
                }

                $fecha_ult_act_bd = date('Y-m-d H:i', strtotime($this->fecha_ult_act_bd));
                $fecha_primer_act_bd = date('Y-m-d H:i', strtotime($this->fecha_primer_act_bd));

                // Si la compra ingresa por "Llamados en general" puede haber sido publicado o puede haber sido modificado, si fuera modificado entonces tendría ese atributo asignado

                if((($fecha_ult_mod != '') && ($fecha_ult_mod >= $fecha_ult_act_bd || $fecha_ult_mod <= $fecha_primer_act_bd)) || (isset($objetos_compra['atributos']['fecha_pub_adj']) && (formatear_fecha_hora($objetos_compra['atributos']['fecha_pub_adj']) >= $fecha_ult_act_bd || formatear_fecha_hora($objetos_compra['atributos']['fecha_pub_adj']) <= $fecha_primer_act_bd))){
                    
                    // echo '<br>ID compra: '.$objetos_compra['atributos']['id_compra'].'<br>Última actualización BD = '.$this->fecha_ult_act_bd.'<br>Fecha ult mod llamado = '.$objetos_compra['atributos']['fecha_ult_mod_llamado'].'<br>Fecha publicacion = '.$objetos_compra['atributos']['fecha_publicacion'].'<br>'.$objetos_compra['atributos']['fecha_pub_adj'].'<br>';

                    $id_compra = (string)$objetos_compra['atributos']['id_compra'];
                    
                    $objetos_compra['items_compra'] = $compra->items->children();
                    $objetos_compra['aclaraciones_llamado'] = $compra->aclaraciones_lla->children();
                    $objetos_compra['modificaciones_compra'] = $compra->hist_mod_llamado->children();
                    $objetos_compra['items_adjudicacion'] = $compra->adjudicaciones->children();
                    $objetos_compra['aclaraciones_adjudicacion'] = $compra->aclaraciones_adj->children();
                    $objetos_compra['oferentes'] = $compra->oferentes->children();

                    $anio = strval($objetos_compra['atributos']['anio_compra']);

                    if(!isset($this->database_anios[$anio] )){
                        $this->database_anios[$anio] = Array();
                    }

                    $this->database_anios[$anio][$id_compra] = $objetos_compra;
                }
            }
        }

        public function insert_update_registro_bd_anio($objeto, $tabla, $claves, $anio){
    
            //$sql = sql_con("gestor_compras_estatales_".$objeto['atributos']->anio_compra);

            $query = "INSERT INTO ".$tabla." ( ";
            $values = " ) VALUES ( ";
            $duplicate = '';

            if(count($claves) > 0){

                $duplicate = " ON DUPLICATE KEY UPDATE ";

                foreach($claves as $clave => $valor){
    
                    $query .= $clave." , ";
                    $values .= "'".$valor."' , ";
                    $duplicate .= $clave." = '".$valor."' , ";
    
                }

                $duplicate = substr($duplicate, 0, -2);

            }
    
            foreach($objeto as $a => $b){
    
                if(!array_key_exists($a, $claves)){
    
                    $query .= $a.' , ';
                    if (in_array($a, FECHAHORA)){
                        $b = formatear_fecha_hora($b);
                        $values .= '"'.$b.'" , ';
                    } else {
                        if(strpos($b, '"') !== FALSE){
                            if(strpos($b, "'") !== FALSE){
                                $b = str_replace("'", " ", $b);
                            }
                            $values .= "'".$b."' , ";
                        } else {
                            $values .= '"'.$b.'" , ';
                        }
                    }
                }
                
            }

            $query = substr($query, 0, -2);
            $values = substr($values, 0, -2);
    
            $values .= " )";
            $query .= $values.$duplicate.";";

            $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

            //echo $query.'<br>';

            $q = $sql->query($query);

            mysqli_close($sql);
        }

        public function get_databases_compras_anio(){

            $sql = $this->sql_con();
        
            $q = $sql->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE '%gestor_compras_estatales_%'");
    
            $result = Array();
    
            if($q){
    
                while($r = $q->fetch_object()){

                    $anio = substr($r->SCHEMA_NAME, -4);
                    $this->databases_anios[$anio] = $r->SCHEMA_NAME;
    
                }
            }
        }

        public function get_fecha_ult_act_bd(){

            $sql = sql_con('gestion_bd');

            $q = $sql->query('SELECT fecha_hasta FROM actualizacion_bd WHERE instancia = (SELECT MAX(instancia) FROM actualizacion_bd)');
        
            while($r = $q->fetch_object()){
                $fecha_hasta = $r->fecha_hasta;
            }
    
            mysqli_close($sql);
    
            return $fecha_hasta;

        }

        public function get_fecha_ultimo_respaldo(){

            $sql = sql_con($this->database_gestion);
    
            $q = $sql->query('
                SELECT fecha_respaldada
                FROM respaldos 
                WHERE contador = (SELECT MAX(contador) FROM respaldos)
            ');
    
            $fecha_ult_respaldo = '';
    
            if($q){
    
                while($r = $q->fetch_object()){
                    $fecha_ult_respaldo = $r->fecha_respaldada;
                }
    
            } else {
                $fecha_ult_respaldo = '1970-01-01 00:00:00';
            }
    
            mysqli_close($sql);
    
            return $fecha_ult_respaldo;
        }

        public function database_exists($schema){

            $sql = sql_con();
            
            $q = $sql->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE '%".$schema."%'");
    
            $result = Array();
    
            if($q){
    
                while($r = $q->fetch_object()){
    
                    $result[] = $r->SCHEMA_NAME;
    
                }
            }
    
            if(count($result) > 0){

                return true;
    
            } else {
    
                return false;
    
            }
    
        }

        public function create_database_compras_anio($anio){

            $sql = sql_con();
    
            $script = "CREATE DATABASE gestor_compras_estatales_".$anio;
    
            $q = $sql->query($script);
    
            mysqli_close($sql);
    
            $sql = sql_con("gestor_compras_estatales_".$anio);
    
            $scripts = Array();
    
            $scripts[] = "CREATE TABLE `compras` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `id_inciso` INT(2) NULL DEFAULT NULL , `id_ue` INT(3) NULL DEFAULT NULL , `id_ucc` INT(2) NULL DEFAULT NULL , `num_compra` VARCHAR(50) NULL DEFAULT NULL , `anio_compra` INT(4) NULL DEFAULT NULL , `nro_ampliacion` INT NULL DEFAULT NULL , `estado_compra` INT NULL DEFAULT NULL , `nombre_pliego` VARCHAR(200) NULL DEFAULT NULL , `fecha_publicacion` DATETIME NULL DEFAULT NULL , `fecha_ult_mod_llamado` DATETIME NULL DEFAULT NULL , `id_tipocompra` CHAR(2) NULL DEFAULT NULL , `subtipo_compra` CHAR(3) NULL DEFAULT NULL , `objeto` VARCHAR(2000) NULL DEFAULT NULL , `fecha_hora_apertura` DATETIME NULL DEFAULT NULL , `lugar_apertura` VARCHAR(200) NULL DEFAULT NULL , `fecha_sol_prorr` DATE NULL DEFAULT NULL , `fecha_sol_aclar` DATE NULL DEFAULT NULL , `fecha_hora_tope_entrega` DATETIME NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `lugar_entrega` VARCHAR(200) NULL DEFAULT NULL , `precio_pliego` FLOAT(15) NULL DEFAULT NULL , `id_moneda_pliego` INT(2) NULL DEFAULT NULL , `lugar_compra_pliego` VARCHAR(200) NULL DEFAULT NULL , `nombre_contacto` VARCHAR(200) NULL DEFAULT NULL , `fax_contacto` VARCHAR(50) NULL DEFAULT NULL , `email_contacto` VARCHAR(50) NULL DEFAULT NULL , `fecha_pub_adj` DATETIME NULL DEFAULT NULL , `fecha_compra` DATE NULL DEFAULT NULL , `fecha_vigencia_adj` DATE NULL DEFAULT NULL , `fondos_rotatorios` CHAR(1) NOT NULL DEFAULT 'N' , `apel` CHAR(1) NOT NULL DEFAULT 'N' , `arch_adj` VARCHAR(200) NULL DEFAULT NULL , `monto_adj` FLOAT(15) NULL DEFAULT NULL , `id_moneda_monto_adj` INT(2) NULL DEFAULT NULL , `id_tipo_resol` INT(3) NULL DEFAULT NULL , `num_resol` INT(9) NULL DEFAULT NULL , `es_reiteracion` CHAR(1) NOT NULL DEFAULT 'N' , `arch_reiteracion` VARCHAR(200) NULL DEFAULT NULL, PRIMARY KEY (`id_compra`)) ENGINE = InnoDB;";
    
            $scripts[] = "CREATE TABLE `items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `cant_pedida` FLOAT(15) NULL DEFAULT NULL , `id_moneda_cotiz` INT(2) NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `duracion_puja` INT(2) NULL DEFAULT NULL , `tipo_margen_puja` VARCHAR(1) NULL DEFAULT NULL , `margen_puja` INT(13) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` )) ENGINE = InnoDB;";
    
            $scripts[] = "CREATE TABLE `atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `requerido` VARCHAR(1) NOT NULL DEFAULT 'N' , `cod_condicion` VARCHAR(2) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' , PRIMARY KEY ( `id_compra` , `nro_item` , `nro_atributo_item` )) ENGINE = InnoDB;";
            
            $scripts[] = "CREATE TABLE `valores_atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `nro_valor_atributo_item` INT NULL DEFAULT NULL ,`id_prop_atributo` INT(4) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` , `nro_atributo_item` , `nro_valor_atributo_item` )) ENGINE = InnoDB;";
            
            $scripts[] = "CREATE TABLE `oferentes` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL , `cant_adj` FLOAT(15) NULL DEFAULT NULL , `precio_unit` FLOAT(15) NULL DEFAULT NULL , `precio_tot_imp` FLOAT(15) NULL DEFAULT NULL , `id_moneda` INT(2) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL , `variacion` VARCHAR(600) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `atributos_items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' , PRIMARY KEY ( `id_compra` , `nro_item` , `nro_atributo_item` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `historial_modificaciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `campo` VARCHAR(30) NULL DEFAULT NULL , `valor_anterior` VARCHAR(200) NULL DEFAULT NULL , `valor_nuevo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `aclaraciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` VARCHAR(1000) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `aclaraciones_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` VARCHAR(1000) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `requerimientos_compra` ( `id_compra` VARCHAR(9) NOT NULL ,`nro_requerimiento` INT(3) NOT NULL ,`fecha_alta` DATETIME NOT NULL ,`funcionario_alta` INT(5) NOT NULL ,`obligatorio` BOOLEAN NULL DEFAULT NULL ,`tipo` INT(3) NOT NULL ,`estado` INT(3) NULL DEFAULT NULL ,`funcionario_cumplido` INT(5) NULL DEFAULT NULL ,`fecha_cumplido` DATETIME NULL DEFAULT NULL ,`descripcion` VARCHAR(500) NULL DEFAULT NULL ,`archivo_adjunto` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB;";
            
            $scripts[] = "CREATE TABLE `visitas_obligatorias_compra` ( `id_compra` VARCHAR(10) NOT NULL ,`nro_requerimiento` INT(3) NOT NULL ,`fecha_inicio` DATETIME NOT NULL ,`fecha_fin` DATETIME NOT NULL ,`lugar_visita` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB;";
            
            foreach($scripts as $script){
    
                $q = $sql->query($script);
    
            }

            $this->anios[$anio] = '';
    
            mysqli_close($sql);
        }
    }



?>