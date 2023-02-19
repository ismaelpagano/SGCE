<?php

    class Actualizador {


        public string $database_gestion = 'gestion_bd_sandbox';
        public Array $databases_anios;

        public function __construct(){

            // $this->get_databases_compras_anio();
            $this->actualizador_master();

        }

        public function sql_con($db = ''){
            $sql = new mysqli('localhost', 'root', '', $db);
            $sql->set_charset('utf8');
        
            return $sql;
        }

        public function actualizador_master(){

            $fecha_actual = date('Y-m-d H:i:s');
    
            $fecha_ult_respaldo = $this->get_fecha_ultimo_respaldo();
    
            if((int)date('H', strtotime($fecha_actual)) >= 7 AND false){
    
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
    
            } else if ((strtotime($fecha_actual)) - strtotime($fecha_ult_respaldo) > 172800) {

                get_registros_fecha();
    
            }
    
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
    }



?>