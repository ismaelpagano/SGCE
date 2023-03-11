<?php

    class Actualizador {


        public string $database_gestion = 'gestion_bd';
        public $database_anios = Array();
        public $anios = Array();
        public $fecha_primer_act_bd = '';
        public $fecha_ult_act_bd = '';

        public function __construct(){

            $this->get_databases_compras_anio();

        }

        public function sql_con($db = ''){
            $sql = new mysqli('localhost', 'root', '', $db);
            $sql->set_charset('utf8');
        
            return $sql;
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
    
            $scripts[] = "CREATE TABLE `compras` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `id_inciso` INT(2) NULL DEFAULT NULL , `id_ue` INT(3) NULL DEFAULT NULL , `id_ucc` INT(2) NULL DEFAULT NULL , `num_compra` VARCHAR(50) NULL DEFAULT NULL , `anio_compra` INT(4) NULL DEFAULT NULL , `nro_ampliacion` INT NULL DEFAULT NULL , `estado_compra` INT NULL DEFAULT NULL , `nombre_pliego` VARCHAR(200) NULL DEFAULT NULL , `fecha_publicacion` DATETIME NULL DEFAULT NULL , `fecha_ult_mod_llamado` DATETIME NULL DEFAULT NULL , `id_tipocompra` CHAR(2) NULL DEFAULT NULL , `subtipo_compra` CHAR(3) NULL DEFAULT NULL , `objeto` VARCHAR(2000) NULL DEFAULT NULL , `fecha_hora_apertura` DATETIME NULL DEFAULT NULL , `lugar_apertura` VARCHAR(200) NULL DEFAULT NULL , `fecha_sol_prorr` DATE NULL DEFAULT NULL , `fecha_sol_aclar` DATE NULL DEFAULT NULL , `fecha_hora_tope_entrega` DATETIME NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `lugar_entrega` VARCHAR(200) NULL DEFAULT NULL , `precio_pliego` FLOAT(15) NULL DEFAULT NULL , `id_moneda_pliego` INT(2) NULL DEFAULT NULL , `lugar_compra_pliego` VARCHAR(200) NULL DEFAULT NULL , `nombre_contacto` VARCHAR(200) NULL DEFAULT NULL , `fax_contacto` VARCHAR(50) NULL DEFAULT NULL , `email_contacto` VARCHAR(50) NULL DEFAULT NULL , `fecha_pub_adj` DATETIME NULL DEFAULT NULL , `fecha_compra` DATE NULL DEFAULT NULL , `fecha_vigencia_adj` DATE NULL DEFAULT NULL , `fondos_rotatorios` CHAR(1) NOT NULL DEFAULT 'N' , `apel` CHAR(1) NOT NULL DEFAULT 'N' , `arch_adj` VARCHAR(200) NULL DEFAULT NULL , `monto_adj` FLOAT(15) NULL DEFAULT NULL , `id_moneda_monto_adj` INT(2) NULL DEFAULT NULL , `id_tipo_resol` INT(3) NULL DEFAULT NULL , `num_resol` INT(9) NULL DEFAULT NULL , `es_reiteracion` CHAR(1) NOT NULL DEFAULT 'N' , `arch_reiteracion` VARCHAR(200) NULL DEFAULT NULL, PRIMARY KEY (`id_compra`)) ENGINE = InnoDB";

            $scripts[] = "CREATE TABLE `items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `cant_pedida` FLOAT(15) NULL DEFAULT NULL , `id_moneda_cotiz` INT(2) NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `duracion_puja` INT(2) NULL DEFAULT NULL , `tipo_margen_puja` VARCHAR(1) NULL DEFAULT NULL , `margen_puja` INT(13) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT 'NO' , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` , `variante` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `requerido` VARCHAR(1) NOT NULL DEFAULT 'N' , `cod_condicion` VARCHAR(2) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' , `variante` VARCHAR(60) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` , `variante` , `nro_atributo_item` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `valores_atributos_items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `nro_valor_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_item` , `variante` , `nro_atributo_item` , `nro_valor_atributo_item` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `oferentes` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `nombre_comercial` VARCHAR(255) NULL DEFAULT NULL , `cant_adj` FLOAT(15) NULL DEFAULT NULL , `precio_unit` FLOAT(15) NULL DEFAULT NULL , `precio_tot_imp` FLOAT(15) NULL DEFAULT NULL , `id_moneda` INT(2) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT 'NO' , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL , `variacion` VARCHAR(600) NULL DEFAULT 'NO' , PRIMARY KEY ( `id_compra` , `nro_item` , `variante` , `variacion` , `tipo_doc_prov` , `nro_doc_prov` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `atributos_items_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `nro_atributo_item` INT NULL DEFAULT NULL , `id_prop_atributo` INT(4) NULL DEFAULT NULL , `desc_prop_atributo` VARCHAR(300) NULL DEFAULT NULL , `id_unidad_med_prop_atributo` INT(3) NULL DEFAULT NULL , `desc_unidad_med_prop_atributo` VARCHAR(25) NULL DEFAULT NULL , `valor_numerico` FLOAT(17) NULL DEFAULT NULL , `valor_texto` VARCHAR(4000) NULL DEFAULT NULL , `valor_fecha` DATE NULL DEFAULT NULL , `valor_booleano` VARCHAR(1) NOT NULL DEFAULT 'N' , `variacion` VARCHAR(600) NOT NULL DEFAULT 'no' , `tipo_doc_prov` CHAR(1) NULL DEFAULT NULL , `nro_doc_prov` VARCHAR(12) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , PRIMARY KEY  ( `id_compra` , `nro_item` , `nro_atributo_item` , `variante` , `variacion` , `tipo_doc_prov` , `nro_doc_prov` )) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `historial_modificaciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `campo` VARCHAR(30) NULL DEFAULT NULL , `valor_anterior` VARCHAR(200) NULL DEFAULT NULL , `valor_nuevo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `aclaraciones_llamado` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` VARCHAR(1000) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `aclaraciones_adjudicacion` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `texto` VARCHAR(1000) NULL DEFAULT NULL , `fecha` DATETIME NULL DEFAULT NULL , `nom_archivo` VARCHAR(200) NULL DEFAULT NULL ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `requerimientos_compra` ( `id_compra` VARCHAR(9) NOT NULL , `nro_requerimiento` INT(3) NOT NULL , `fecha_alta` DATETIME NOT NULL , `funcionario_alta` INT(5) NOT NULL , `obligatorio` BOOLEAN NULL DEFAULT NULL , `tipo` INT(3) NOT NULL , `estado` INT(3) NULL DEFAULT NULL , `funcionario_cumplido` INT(5) NULL DEFAULT NULL , `fecha_cumplido` DATETIME NULL DEFAULT NULL , `descripcion` VARCHAR(500) NULL DEFAULT NULL , `archivo_adjunto` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB";
            
            $scripts[] = "CREATE TABLE `visitas_obligatorias_compra` ( `id_compra` VARCHAR(10) NOT NULL , `nro_requerimiento` INT(3) NOT NULL , `fecha_inicio` DATETIME NOT NULL , `fecha_fin` DATETIME NOT NULL , `lugar_visita` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY ( `id_compra` , `nro_requerimiento` ) ) ENGINE = InnoDB";

            foreach($scripts as $script){
    
                $q = $sql->query($script);
    
            }

            $this->anios[$anio] = '';
    
            mysqli_close($sql);
        }

        private function get_fechas_actualizacion(){

            $sql = $this->sql_con('gestion_bd');

            $q = "SELECT fecha_desde FROM actualizaciones_bd WHERE instancia = (SELECT MIN(instancia) FROM actualizaciones_bd)";

            $q = $sql->query($q);

                while($r = $q->fetch_object()){
                    $this->fecha_primer_act_bd = $r->fecha_desde;
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

            $q = $sql->query('SELECT fecha_hasta FROM actualizaciones_bd WHERE instancia = (SELECT MAX(instancia) FROM actualizaciones_bd)');
        
            $fecha_hasta = '';
 
            if($r = $q->fetch_object()){
                    $fecha_hasta = $r->fecha_hasta;
            } else {
                $fecha_hasta = '2023-01-01 00:00:00';
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
                $fecha_ult_respaldo = '2023-01-01 00:00:00';
            }
    
            mysqli_close($sql);
    
            return $fecha_ult_respaldo;
        }

        public function main(){

            while(true){

                //echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

                $this->launcher_actualizador();
        
                sleep(600);
        
            }

        }

        public function launcher_actualizador(){

            $fecha_actual = date('Y-m-d H:i:s');
    
            // $fecha_ult_respaldo = $this->get_fecha_ultimo_respaldo();
    
            if((int)date('H', strtotime($fecha_actual)) >= 7){

                $this->get_fechas_actualizacion();
    
                if($this->fecha_ult_act_bd == '' && $this->fecha_primer_act_bd == ''){
                    $this->fecha_ult_act_bd = '2023-03-01 00:00:00';
                    $this->fecha_primer_act_bd = date('Y-m-d H:i:s');
                }
    
                $fecha_final = date('Y-m-d H:i:s');
    
                if((strtotime($fecha_final) - strtotime($this->fecha_ult_act_bd)) > 604800){
                
                    $fecha_intermedia = date('Y-m-d H:i:s', strtotime($this->fecha_ult_act_bd.' + 604799 SECONDS' ));
        
                    $this->actualizador_compras($this->fecha_ult_act_bd, $fecha_intermedia);
        
                    $this->launcher_actualizador(); 
        
                } else {
        
                    $this->actualizador_compras($this->fecha_ult_act_bd, $fecha_final);
        
                }
    
            } else if ((strtotime($fecha_actual)) - strtotime($fecha_ult_respaldo) > 172800) {

                // Acá se haría el respaldo cuando la hora es menor a las 7 AM y la fecha actual tiene una diferencia de mas de 2 días con respecto al respaldo anterior
    
            }
        }

        private function actualizador_compras($fecha_inicial, $fecha_fin){

            $fecha_actual = date('Y-m-d H:i:s');

            $fecha_inicial = date('Y-m-d H:i', strtotime($fecha_inicial.' - 1 HOUR'));

            $fecha_final = date('Y-m-d H:i', strtotime($fecha_fin.' + 1 HOUR'));

            print_r("Se actualizará la base de datos. ".$fecha_actual.".\n");

            // 2) Obtener todas las compras agregadas o actualizadas en el periodo de tiempo predeterminado en el portal de ARCE
    
            $this->ordenar_compras_anio($fecha_inicial, $fecha_final);

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
              
                    $query = '';

                    if(isset($compra['compra'])){

                        $id_compra = (string)$compra['compra']['atributos']['id_compra'];
                        $claves = Array('id_compra' => $id_compra);
                        $anio = (string)$compra['compra']['atributos']['anio_compra'];
                        $update = Array( 'estado_compra' , 'fecha_hora_tope_entrega' , 'fecha_publicacion' , 'fecha_ult_mod_llamado' , 'fecha_sol_prorr' , 'fecha_sol_aclar' , 'fecha_hora_puja' , 'fecha_hora_tope_entrega' , 'fecha_pub_adj' , 'fecha_vigencia_adj' , 'fecha_compra' , 'arch_adj' , 'monto_adj' , 'id_moneda_monto_adj' , 'id_tipo_resol' , 'nro_resol' );
                        $query .= $this->insert_update_registro_bd_anio( $compra['compra']['atributos'] , 'gestor_compras_estatales_'.$anio.'.compras' , $claves , $update , $anio );
                        
                        if($compra['compra']['items_compra'] != NULL){
    
                            foreach($compra['compra']['items_compra'] as $item){
    
                                $atributos = $item->attributes();
                                $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'variante' => $item['variante']);
                                $update = Array();
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.items_compra', $claves, $update, $anio);
                                
                            }
                        }

                        if($compra['compra']['modificaciones_compra'] != NULL){
    
                            $query .= "DELETE FROM gestor_compras_estatales_".$anio.".historial_modificaciones_llamado WHERE id_compra = '".$id_compra."' ; ";
    
                            foreach($compra['compra']['modificaciones_compra'] as $modificaciones){
    
                                $atributos = $modificaciones->attributes(); 
                                $update = Array();
                                $claves = Array( 'id_compra' => $id_compra);
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.historial_modificaciones_llamado', $claves, $update, $anio);
 
                            }
                        }

                        if($compra['compra']['aclaraciones_llamado'] != NULL){
    
                            $query .= "DELETE FROM gestor_compras_estatales_".$anio.".aclaraciones_llamado WHERE id_compra = '".$id_compra."' ; ";
                
                            foreach($compra['compra']['aclaraciones_llamado'] as $aclaraciones){
    
                                $atributos = $aclaraciones->attributes(); 
                                $update = Array();
                                $claves = Array( 'id_compra' => $id_compra );
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.aclaraciones_llamado', $claves, $update, $anio);
                            }
                        }

                        $update = " ON DUPLICATE KEY UPDATE ";

                        $fecha_publicacion_query = '';
                        $fecha_publicacion = '';

                        $fecha_ult_mod_llamado_query = '';
                        $fecha_ult_mod_ = '';

                        $fecha_pub_adj_query = '';
                        $fecha_pub_adj = '';

                        $fecha_hora_tope_entrega = '';
                        $fecha_hora_tope_entrega_query = '';

                        if(isset($compra['compra']['atributos']['fecha_publicacion'])){

                            $fecha_publicacion_query = ' fecha_publicacion ,';
                            $fecha_publicacion = "'".$compra['compra']['atributos']['fecha_publicacion']."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';

                            if(isset($compra['compra']['atributos']['fecha_ult_mod_llamado'])){
                                $fecha_ult_mod_arce = "'".$compra['compra']['atributos']['fecha_ult_mod_llamado']."' ,";
                            } else {
                                $fecha_ult_mod_arce = $fecha_publicacion;
                            }

                            
                            $update .= " fecha_publicacion = ".$fecha_publicacion." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";

                            if(isset($compra['compra']['atributos']['fecha_hora_tope_entrega'])){

                                $fecha_hora_tope_entrega_query = ' fecha_hora_tope_entrega ,';
                                $fecha_hora_tope_entrega = "'".$compra['compra']['atributos']['fecha_hora_tope_entrega']."' ,";
                                $update .= " fecha_hora_tope_entrega = ".$fecha_hora_tope_entrega." ";

                            }

                        } else if (isset($compra['compra']['atributos']['fecha_pub_adj'])){

                            $fecha_pub_adj_query = ' fecha_publicacion ,';
                            $fecha_pub_adj = "'".$compra['compra']['atributos']['fecha_pub_adj']."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';
                            $fecha_ult_mod_arce = $fecha_pub_adj;
                            $update .= " fecha_pub_adj = ".$fecha_pub_adj." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";
                        }

                        $update .= " estado_arce = ".$compra['compra']['atributos']['estado_compra']." ;";

                        $sql = $this->sql_con();

                        $gestion = "INSERT INTO gestion_bd.gestion_compras ( id_compra , anio_compra ,".$fecha_publicacion_query.$fecha_ult_mod_llamado_query.$fecha_pub_adj_query.$fecha_hora_tope_entrega_query." fecha_ult_mod_sgce , estado_arce ) VALUES ( '".$id_compra."' , '".$compra['compra']['atributos']['anio_compra']."' ,".$fecha_publicacion.$fecha_ult_mod_arce.$fecha_pub_adj.$fecha_hora_tope_entrega." '".date('Y-m-d H:i:s')."' , '".$compra['compra']['atributos']['estado_compra']."' )".$update;
                        
                        $q = $sql->query($gestion);

                        if(!$q){

                            print_r("Ha fallado la solicitud para: ".$gestion)."\n";

                        }
    
                        mysqli_close($sql);

                        $actualizacion = "INSERT INTO gestion_bd.actualizacion_estado_llamado ( id_compra , fecha_actualizacion ) VALUES ( '".$id_compra."' , '".date('Y-m-d H:i:s')."' ) ON DUPLICATE KEY UPDATE id_compra = '".$id_compra."'";

                        $sql = $this->sql_con();

                        $q = $sql->query($actualizacion);

                        if(!$q){

                            print_r("Ha fallado la solicitud para: ".$actualizacion)."\n";

                        }
    
                        mysqli_close($sql);

                    }

                    if(isset($compra['adjudicacion'])){

                        $id_compra = (string)$compra['adjudicacion']['atributos']['id_compra'];
                        
                        $claves = Array('id_compra' => $id_compra);
                        $anio = (string)$compra['adjudicacion']['atributos']['anio_compra'];
                        $update = Array( 'estado_compra' , 'fecha_hora_tope_entrega' , 'fecha_publicacion' , 'fecha_ult_mod_llamado' , 'fecha_sol_prorr' , 'fecha_sol_aclar' , 'fecha_hora_puja' , 'fecha_hora_tope_entrega' , 'fecha_pub_adj' , 'fecha_vigencia_adj' , 'fecha_compra' , 'arch_adj' , 'monto_adj' , 'id_moneda_monto_adj' , 'id_tipo_resol' , 'nro_resol' );
                        $query .= $this->insert_update_registro_bd_anio( $compra['adjudicacion']['atributos'] , 'gestor_compras_estatales_'.$anio.'.compras' , $claves , $update , $anio );
    

                        if($compra['adjudicacion']['items_adjudicacion'] != NULL){

                            foreach($compra['adjudicacion']['items_adjudicacion'] as $item){
    
                                $atributos = $item->attributes();
                                $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'variante' => $item['variante'], 'variacion' => $item['variacion'] , 'tipo_doc_prov' => $item['tipo_doc_prov'] , 'nro_doc_prov' => $item['nro_doc_prov']);
                                $update = Array();
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.items_adjudicacion', $claves, $update, $anio);
                                $atributos_item = $item->children();
    
                                if($atributos_item != NULL){
    
                                    $nro_atributo_item = 1;
    
                                    foreach($atributos_item as $atributo_item){
    
                                        $atributos = $atributo_item->attributes();
                                        $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'nro_atributo_item' => $nro_atributo_item , 'variante' => $item['variante'] , 'tipo_doc_prov' => $item['tipo_doc_prov'] , 'nro_doc_prov' => $item['nro_doc_prov']);
                                        $update = Array();
                                        $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.atributos_items_adjudicacion', $claves, $update, $anio);

                                        $nro_atributo_item++;
                                    }
    
                                }
    
                            }
    
                        }
    
                        if($compra['adjudicacion']['aclaraciones_adjudicacion'] != NULL){
    
                            $query .= "DELETE FROM gestor_compras_estatales_".$anio.".aclaraciones_adjudicacion WHERE id_compra = '".$id_compra."' ; ";
    
                            
                            foreach($compra['adjudicacion']['aclaraciones_adjudicacion'] as $modificaciones){
    
                                $atributos = $modificaciones->attributes(); 
                                $update = Array();
                                $claves = Array( 'id_compra' => $id_compra);
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.aclaraciones_adjudicacion', $claves, $update, $anio);
                            

                            
                            }
    
                        }
    
                        if($compra['adjudicacion']['oferentes'] != NULL){
    
                            $query .= "DELETE FROM gestor_compras_estatales_".$anio.".oferentes WHERE id_compra = '".$id_compra."' ; ";
    
                            
                            foreach($compra['adjudicacion']['oferentes'] as $aclaraciones){
    
                                $atributos = $aclaraciones->attributes(); 
                                $update = Array();
                                $claves = Array( 'id_compra' => $id_compra );
                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.oferentes', $claves, $update, $anio);
                           

                            }
                        }

                        $update = " ON DUPLICATE KEY UPDATE ";

                        $fecha_publicacion_query = '';
                        $fecha_publicacion = '';

                        $fecha_ult_mod_llamado_query = '';
                        $fecha_ult_mod_ = '';

                        $fecha_pub_adj_query = '';
                        $fecha_pub_adj = '';

                        if(isset($compra['adjudicacion']['atributos']['fecha_publicacion'])){

                            $fecha_publicacion_query = ' fecha_publicacion ,';
                            $fecha_publicacion = "'".$compra['adjudicacion']['atributos']['fecha_publicacion']."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';

                            if(isset($compra['adjudicacion']['atributos']['fecha_ult_mod_llamado'])){
                                $fecha_ult_mod_arce = "'".$compra['adjudicacion']['atributos']['fecha_ult_mod_llamado']."' ,";
                            } else {
                                $fecha_ult_mod_arce = $fecha_publicacion;
                            }

                            $update .= " fecha_publicacion = ".$fecha_publicacion." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";

                        } else if (isset($compra['adjudicacion']['atributos']['fecha_pub_adj'])){

                            $fecha_pub_adj_query = ' fecha_publicacion_adj ,';
                            $fecha_pub_adj = "'".$compra['adjudicacion']['atributos']['fecha_pub_adj']."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';
                            $fecha_ult_mod_arce = $fecha_pub_adj;
                            $update .= " fecha_publicacion_adj = ".$fecha_pub_adj." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";
                        }

                        $update .= " estado_arce = '".$compra['adjudicacion']['atributos']['estado_compra']."';";

                        $gestion = "INSERT INTO gestion_bd.gestion_compras ( id_compra , anio_compra ,".$fecha_publicacion_query.$fecha_ult_mod_llamado_query.$fecha_pub_adj_query." fecha_ult_mod_sgce , estado_arce ) VALUES ( '".$id_compra."' , '".$compra['adjudicacion']['atributos']['anio_compra']."' ,".$fecha_publicacion.$fecha_ult_mod_arce.$fecha_pub_adj." '".date('Y-m-d H:i:s')."' , '".$compra['adjudicacion']['atributos']['estado_compra']."' )".$update;
                    
                        $sql = $this->sql_con();
                    
                        $sql->query($gestion);
    
                        mysqli_close($sql);

                        $actualizacion = "INSERT INTO gestion_bd.actualizacion_estado_llamado ( id_compra , fecha_actualizacion ) VALUES ( '".$id_compra."' , '".date('Y-m-d H:i:s')."' ) ON DUPLICATE KEY UPDATE id_compra = '".$id_compra."'";

                        $sql = $this->sql_con();

                        $sql->query($actualizacion);
    
                        mysqli_close($sql);

                    } 

                    $sql = $this->sql_con();
                    
                    $q = $sql->multi_query($query);

                    mysqli_close($sql);

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

        private function xml_request($tipo_publicacion, $fecha_inicio, $fecha_fin){

            $fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio.' - 1 HOUR'));

            $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin.' + 1 HOUR'));

            $tipo_publicacion = '&tipo_publicacion='.$tipo_publicacion;
            
            $tipo_compra = '&tipo_compra=';
    
            $rango_fecha = '&rango-fecha='.date('d' , strtotime($fecha_inicio)).'%2F'.date('m' , strtotime($fecha_inicio)).'%2F'.date('Y' , strtotime($fecha_inicio)).'+-+'.date('d' , strtotime($fecha_fin)).'%2F'.date('m' , strtotime($fecha_fin)).'%2F'.date('Y' , strtotime($fecha_fin)).'&';

            $hora_fin = date('G', strtotime($fecha_fin));
    
            $url = URL_WS.$tipo_publicacion.$tipo_compra.$rango_fecha.'&dia_inicial='.date('j' , strtotime($fecha_inicio)).'&mes_inicial='.date('n' , strtotime($fecha_inicio)).'&anio_inicial='.date('Y' , strtotime($fecha_inicio)).'&hora_inicial='.date('G', strtotime($fecha_inicio)).'&dia_final='.date('j' , strtotime($fecha_fin)).'&mes_final='.date('n' , strtotime($fecha_fin)).'&anio_final='.date('Y' , strtotime($fecha_fin)).'&hora_final='.$hora_fin;

            $xml = simplexml_load_file($url);

            print_r($url."\n");

            $return = $xml->reporte_dato->children();

            return $return;
        }

        public function ordenar_compras_anio($fecha_inicio, $fecha_fin){

            $compras = $this->xml_request('l', $fecha_inicio, $fecha_fin);

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

                    $anio = strval($objetos_compra['atributos']['anio_compra']);

                    if(!isset($this->database_anios[$anio] )){
                        $this->database_anios[$anio] = Array();
                    }

                    $this->database_anios[$anio][$id_compra]['compra'] = $objetos_compra;
                }
            }

            $compras = $this->xml_request('a', $fecha_inicio, $fecha_fin);

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

                    $objetos_compra['items_adjudicacion'] = $compra->adjudicaciones->children();
                    $objetos_compra['aclaraciones_adjudicacion'] = $compra->aclaraciones_adj->children();
                    $objetos_compra['oferentes'] = $compra->oferentes->children();

                    $anio = strval($objetos_compra['atributos']['anio_compra']);

                    if(!isset($this->database_anios[$anio] )){
                        $this->database_anios[$anio] = Array();
                    }

                    $this->database_anios[$anio][$id_compra]['adjudicacion'] = $objetos_compra;
                }
            }
        }

        public function insert_update_registro_bd_anio($objeto, $tabla, $claves, $update, $anio){ 
    
            //$sql = sql_con("gestor_compras_estatales_".$objeto['atributos']->anio_compra);

            foreach(FECHAHORA as $atributo){

                if(isset($objeto[$atributo])){
                    $objeto[$atributo] = formatear_fecha_hora($objeto[$atributo]);
                }

            } 

            $query = "INSERT INTO ".$tabla." ( ";
            $values = " ) VALUES ( ";
            $update_query = '';

            if(count($claves) > 0){

                foreach($claves as $clave => $valor){
    
                    $query .= $clave." , ";
                    $values .= "'".$valor."' , ";
                }

            }

            if(count($update) > 0){

                $update_query = " ON DUPLICATE KEY UPDATE ";

                foreach($update as $atributo){

                    if($objeto[$atributo] != ''){
                        $update_query .= $atributo." = '".$objeto[$atributo]."' , ";
                    }

                }
                
                $update_query = substr($update_query, 0, -2);

            }
    
            foreach($objeto as $a => $b){
    
                if(!array_key_exists($a, $claves)){
    
                    $query .= $a.' , ';
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

            $query = substr($query, 0, -2);
            $values = substr($values, 0, -2);
    
            $values .= " )";
            $query .= $values.$update_query." ; ";

            // $sql = $this->sql_con('gestor_compras_estatales_'.$anio);

            //echo $query.'<br>';

            //$q = $sql->query($query);

            // mysqli_close($sql);

            // echo $query;

            return $query;
        }

    }



?>
