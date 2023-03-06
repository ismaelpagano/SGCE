<?php

    include 'funcs/funcs.php';

	//     actualizador('2022-07-20', '2022-07-21');

    // $_SESSION['sistema'] = '';

    // $sistema = new Sistema($_SESSION['sistema']);

    // $usuario = new Sesion_usuario("Ismael", "texto");

    // if($usuario->bool_session){
    //         echo "Ingesó exitosamente";
    // } else {
    //         echo "Error";
    // }

    // $sql = sql_con(DATABASE_COMPRAS);

    // $query = "SELECT * from items_compra as items INNER JOIN compras on items.id_compra = compras.id_compra where compras.fecha_publicacion >= '2022-07-20 00:00:00' AND fecha_publicacion <= '2022-07-20 23:59:59'";

    // $q = $sql->query($query);

    // $i = 0;

    // if($q){
    //         while($r = $q->fetch_object()){
    //                 $i++;
    //         }
    // }
	

	// get_registros_fecha();

    // $fecha = '01/01/2022 05:40';

    // echo strtotime(cadena_fecha_hora($fecha)).'<br>';

    // echo time();

        // class Compra{
            
        //     public $id_compra;

        //     public function __construct($id_compra){
        //         $this->id_compra = $id_compra;
        //     }
        // }

        // $compra = new Compra('416869');

        // $compra = new Compras($compra);

        // $compra->completar_datos();

        // $compra->enviar_objeto_socket();

        // $sql = sql_con(DATABASE_GESTION);

        // $q = "SELECT * FROM tipos_requerimiento WHERE fecha_baja IS NULL";

        // $q = $sql->query($q);

        // if($q){

        //     while($r = $q->fetch_object()){

        //         $result = Array();
        //         $result['id'] = $r->id_requerimiento;
        //         $result['descripcion'] = $r->descripcion;
        //         print_r($result);
        //         echo '<br>';

        //     }
        // }

        // mysqli_close($sql);

        // session_start();

        // $_SESSION['sistema'] = new Sistema();

        // print_r($_SESSION); 

        // $compra_actual = new Atributo('compra');

        // $_SESSION['sistema']->objetos[$compra_actual->hash]->id_compra = '10000000';

        // foreach(database_exists('gestor_compras') as $a => $b){

        //     echo $a.' = '.$b.'<br>';

        // }

        // $sql = sql_con();

        // $q = "SELECT compra.id_compra as id_compra , gestion.estado_interno as estado_interno , compra.fecha_publicacion as fecha_publicacion , compra.fecha_pub_adj as fecha_pub_adj , compra.fecha_ult_mod_llamado as fecha_ult_mod_llamado , compra.estado_compra as estado_compra , compra.anio_compra as anio_compra FROM gestor_compras_estatales_sandbox.compras as compra INNER JOIN gestion_bd_sandbox.gestion_compras as gestion ON compra.id_compra = gestion.id_compra";

        // $q = $sql->query($q);

        // $compras = Array();

        // if($q){

        //     while($r = $q->fetch_object()){

        //         $compras[$r->id_compra] = $r;

        //     }

        // }

        // foreach($compras as $compra){

        //     $fecha_publicacion = '';
        //     $fecha_publicacion_query = '';
        //     $fecha_ult_mod_llamado = '';
        //     $fecha_ult_mod_llamado_query = '';
        //     $fecha_pub_adj = '';
        //     $fecha_pub_adj_query = '';

        //     if(isset($compra->fecha_publicacion)){

        //         $fecha_publicacion = "'".$compra->fecha_publicacion."' , ";
        //         $fecha_publicacion_query = "fecha_publicacion , ";

        //         if(isset($compra->fecha_ult_mod_llamado)){
        //             $fecha_ult_mod_llamado = "'".$compra->fecha_ult_mod_llamado."' , ";
        //             $fecha_ult_mod_llamado_query = "fecha_ult_mod_arce , ";
        //         }

        //     } else if (isset($compra->fecha_pub_adj)){

        //         $fecha_pub_adj = "'".$compra->fecha_pub_adj."' , ";
        //         $fecha_pub_adj_query = "fecha_publicacion_adj , ";
        //     }

        //     $q = "INSERT INTO gestion_bd.gestion_compras ( id_compra , estado_interno , ".$fecha_publicacion_query.$fecha_pub_adj_query.$fecha_ult_mod_llamado_query."estado_arce , anio_compra , fecha_ult_mod_sgce ) VALUES ( '".$compra->id_compra."' , '".$compra->estado_interno."' , ".$fecha_publicacion.$fecha_pub_adj.$fecha_ult_mod_llamado." '".$compra->estado_compra."' , '".$compra->anio_compra."' , '".date('Y-m-d H:i:s')."' )";

        //     $sql->query($q);

        // }

        //get_registros_fecha();

        // $sql = sql_con();

        // $query = "SELECT id_compra , estado_interno FROM gestion_bd_sandbox.gestion_compras WHERE id_compra IN (SELECT id_compra FROM gestion_bd.gestion_compras)";

        // $q = $sql->query($query);

        // if($q){

        //     while($r = $q->fetch_object()) {

        //         $id = $r->id_compra;
        //         $estado = $r->estado_interno;

        //         $query = "UPDATE gestion_bd.gestion_compras SET estado_interno = ".$estado." WHERE id_compra = '".$id."'";

        //         $sql->query($query);

        //     }

        // }

        // mysqli_close($sql);

        function xml_request($tipo_publicacion, $fecha_inicio, $fecha_fin){

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

        // $compras = xml_request('l' , '2023-02-27 00:00:00', '2023-03-04 23:59:00');

        // $identificadores = Array();

        // foreach($compras as $compra){

        //     $atributos = $compra->attributes();

        //     $id_compra = strval($atributos->id_compra);

        //     $identificadores[$id_compra] = "";

        // }

        // $sql = sql_con();

        // $q = "SELECT id_compra FROM gestion_bd.gestion_compras WHERE 
        //     id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2023.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2022.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2021.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2020.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2019.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2018.compras)
        //     AND id_compra NOT IN (SELECT id_compra FROM gestor_compras_estatales_2016.compras)
        //     ";

        // $q = $sql->query($q);

        // if($q){

        //     while($r = $q->fetch_object()){

        //         $id_compra = strval($r->id_compra);

        //         if(!array_key_exists($id_compra, $identificadores)){
        //             echo "<br>id_compra = ".$r->id_compra."<br>";
        //         }

        //     }

        // }

        $database_anios = Array();

        function ordenar_compras_anio($fecha_inicio, $fecha_fin){

            $compras = xml_request('l', $fecha_inicio, $fecha_fin);

            foreach($compras as $compra){
                
                $objetos_compra = Array();
                       
                $objetos_compra['atributos'] = $compra->attributes();

                $id_compra = strval($objetos_compra['atributos']['id_compra']);

                echo $id_compra.'<br>';
                
                $objetos_compra['items_compra'] = $compra->items->children();
                $objetos_compra['aclaraciones_llamado'] = $compra->aclaraciones_lla->children();
                $objetos_compra['modificaciones_compra'] = $compra->hist_mod_llamado->children();

                $anio = (string)($objetos_compra['atributos']['anio_compra']);

                if(!isset($database_anios[$anio] )){
                    $database_anios[$anio] = Array();
                    echo '<br>año ingresado: '.$anio."<br>";
                }

                $database_anios[$anio][$id_compra]['compra'] = $id_compra;
                print_r($database_anios[$anio][$id_compra]['compra']);
            }

            print_r($database_anios);
        }

        ordenar_compras_anio('2023-02-27 00:00:00', '2023-03-04 23:59:59');

        print_r($database_anios);

        function actualizador_compras_bd($fecha_inicial, $fecha_fin){

            $fecha_actual = date('Y-m-d H:i:s');

            $fecha_inicial = date('Y-m-d H:i', strtotime($fecha_inicial.' - 1 HOUR'));

            $fecha_final = date('Y-m-d H:i', strtotime($fecha_fin.' + 1 HOUR'));

            print_r("Se actualizará la lista de compras. ".$fecha_actual.".\n");

            // 2) Obtener todas las compras agregadas o actualizadas en el periodo de tiempo predeterminado en el portal de ARCE
    
            $ordenar_compras_anio($fecha_inicial, $fecha_final);

            foreach($this->database_anios as $anio_compras => $compras){

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
                                
                                $atributos_item = $item->children();
    
                                if($atributos_item != NULL){
    
                                    $nro_atributo_item = 1;
    
                                    foreach($atributos_item as $atributo_item){
    
                                        $atributos = $atributo_item->attributes();
                                        $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'variante' => $item['variante'] , 'nro_atributo_item' => $nro_atributo_item );
                                        $update = Array();
                                        $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.atributos_items_compra', $claves, $update, $anio); 

                                        $valores_atributos_item = $atributo_item->children();
                                        
                                        if($valores_atributos_item != NULL){
            
                                            $nro_valor_atributo_item = 1;
            
                                            foreach($valores_atributos_item as $valor_atributo_item){
            
                                                $atributos = $valor_atributo_item->attributes();
                                                $claves = Array( 'id_compra' => $id_compra , 'nro_item' => $item['nro_item'] , 'nro_atributo_item' => $nro_atributo_item , 'nro_valor_atributo_item' => $nro_valor_atributo_item);
                                                $update = Array();
                                                $query .= $this->insert_update_registro_bd_anio($atributos, 'gestor_compras_estatales_'.$anio.'.valores_atributos_items_compra', $claves, $update, $anio);

                                                $nro_valor_atributo_item++;
                                            }
                                        }
        
                                        $nro_atributo_item++;

                                    }
                                }
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
                            $fecha_publicacion = "'".formatear_fecha_hora($compra['compra']['atributos']['fecha_publicacion'])."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';

                            if(isset($compra['compra']['atributos']['fecha_ult_mod_llamado'])){
                                $fecha_ult_mod_arce = "'".formatear_fecha_hora($compra['compra']['atributos']['fecha_ult_mod_llamado'])."' ,";
                            } else {
                                $fecha_ult_mod_arce = $fecha_publicacion;
                            }

                            
                            $update .= " fecha_publicacion = ".$fecha_publicacion." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";

                            if(isset($compra['compra']['atributos']['fecha_hora_tope_entrega'])){

                                $fecha_hora_tope_entrega_query = ' fecha_hora_tope_entrega ,';
                                $fecha_hora_tope_entrega = "'".formatear_fecha_hora($compra['compra']['atributos']['fecha_hora_tope_entrega'])."' ,";
                                $update .= " fecha_hora_tope_entrega = ".$fecha_hora_tope_entrega." ";

                            }

                        } else if (isset($compra['compra']['atributos']['fecha_pub_adj'])){

                            $fecha_pub_adj_query = ' fecha_publicacion ,';
                            $fecha_pub_adj = "'".formatear_fecha_hora($compra['compra']['atributos']['fecha_pub_adj'])."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';
                            $fecha_ult_mod_arce = $fecha_pub_adj;
                            $update .= " fecha_pub_adj = ".$fecha_pub_adj." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";
                        }

                        $update .= " estado_arce = ".$compra['compra']['atributos']['estado_compra']." ;";

                        $sql = $this->sql_con();

                        $gestion = "INSERT INTO gestion_bd.gestion_compras ( id_compra , anio_compra ,".$fecha_publicacion_query.$fecha_ult_mod_llamado_query.$fecha_pub_adj_query.$fecha_hora_tope_entrega_query." fecha_ult_mod_sgce , estado_arce ) VALUES ( '".$id_compra."' , '".$compra['compra']['atributos']['anio_compra']."' ,".$fecha_publicacion.$fecha_ult_mod_arce.$fecha_pub_adj.$fecha_hora_tope_entrega." '".date('Y-m-d H:i:s')."' , '".$compra['compra']['atributos']['estado_compra']."' )".$update;
                        
                        $sql->query($gestion);
    
                        mysqli_close($sql);

                        $actualizacion = "INSERT INTO gestion_bd.actualizacion_estado_llamado ( id_compra , fecha_actualizacion ) VALUES ( '".$id_compra."' , '".date('Y-m-d H:i:s')."' ) ON DUPLICATE KEY UPDATE id_compra = '".$id_compra."'";

                        $sql = $this->sql_con();

                        $sql->query($actualizacion);
    
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
                            $fecha_publicacion = "'".formatear_fecha_hora($compra['adjudicacion']['atributos']['fecha_publicacion'])."' ,";
                            $fecha_ult_mod_llamado_query = ' fecha_ult_mod_arce ,';

                            if(isset($compra['adjudicacion']['atributos']['fecha_ult_mod_llamado'])){
                                $fecha_ult_mod_arce = "'".formatear_fecha_hora($compra['adjudicacion']['atributos']['fecha_ult_mod_llamado'])."' ,";
                            } else {
                                $fecha_ult_mod_arce = $fecha_publicacion;
                            }

                            $update .= " fecha_publicacion = ".$fecha_publicacion." fecha_ult_mod_arce = ".$fecha_ult_mod_arce." ";

                        } else if (isset($compra['adjudicacion']['atributos']['fecha_pub_adj'])){

                            $fecha_pub_adj_query = ' fecha_publicacion_adj ,';
                            $fecha_pub_adj = "'".formatear_fecha_hora($compra['adjudicacion']['atributos']['fecha_pub_adj'])."' ,";
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
                    
                    $sql->multi_query($query);

                    mysqli_close($sql);

                }
            }

            foreach($this->database_anios as $a => $b){

                $this->database_anios[$a] = Array();

            }

            $sql = $sql_con('gestion_bd');

            $q = "INSERT INTO actualizaciones_bd ( fecha_ejecucion , fecha_desde, fecha_hasta ) VALUES ( '".$fecha_actual."' , '".$this->fecha_ult_act_bd."' , '".$fecha_fin."')";

            $q = $sql->query($q);

            mysqli_close($sql);    

            print_r("Se actualizó la lista de compras. ".date('Y-m-d H:i:s').".\n");
        }

?>
 
<!DOCTYPE html>
<html>
    <head>
            <meta charset="UTF-8" />
            <title>Test</title>
            <script src="js/funcs.js"></script>
    </head>
    <body>
            <?php 
                    // $sistema->mostrar();

                    // echo $i;
            ?>
            <!-- <button onclick="buscarObjeto('<?php // echo $_SESSION['sistema']->objetos[$compra_actual->hash]->hash; ?>')">BOTON</button> -->
    </body>
</html>