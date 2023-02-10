<?php

    Class Compras {

        //Identificacion de la compra

        public $id_compra;
        public $num_compra;
        public $anio_compra;
        public $nro_ampliacion;

        //Organismo y UE

        public $id_inciso;
        public $id_ue;
        public $id_ucc;

        //Estado compra

        public $estado_compra;

        //Fechas ideoneas para actualizacion BD
        
        public $fecha_publicacion;
        public $fecha_ult_mod_llamado; 
        
        //Tipo de compra

        public $id_tipocompra;
        public $subtipo_compra;
        
        //Específico de la compra

        public $objeto;
        public $fecha_hora_apertura;
        public $lugar_apertura;

        //Fechas 

        public $fecha_sol_prorr;
        public $fecha_sol_aclar;
        public $fecha_hora_puja;

        //Entrega de ofertas

        public $lugar_entrega;
        public $fecha_hora_tope_entrega;

        //Pliego

        public $nombre_pliego;
        public $precio_pliego;
        public $id_moneda_pliego;
        public $lugar_compra_pliego;
        public $url_pliego;

        //Info de contacto y comunicacion

        public $contacto;

        //Adjudicacion

        public $fecha_pub_adj;
        public $fecha_compra;
        public $fecha_vigencia_adj;
        public $fondos_rotatorios;
        public $apel;
        public $arch_adj;
        public $monto_adj;
        public $id_moneda_monto_adj;

        //Resolucion

        public $id_tipo_resol;
        public $num_resol;

        //Sobre reiteracion

        public $es_reiteracion;
        public $arch_reiteracion;

        //Items

        public $items = Array();

        //Adjudicación
        public $items_adjudicacion = Array();
        public $atributos_items_adjudicacion = Array();
        public $oferentes = Array();
        public $aclaraciones_adjudicacion = Array();

        //Misc
        public $historial_modificaciones_llamado;
        public $aclaraciones_llamado;

        //Gestión interna

        public $estado_interno;
        public $ofertas = Array();
        public $ofertas_usuario = Array();
        public $requerimientos = Array();

        //Manejo en el sistema

        public int $posicion_array;

        public function __construct($compra){
            $this->id_compra = $compra->id_compra;
        } 

        public function agregar_contacto_bd(){
            $this->contacto = new Contactos($this); 
        }

        public function set_items(){

            $sql = new mysqli('localhost', 'root', '', DATABASE_COMPRAS);
            $sql->set_charset('utf8');

            $q = $sql->query("SELECT * FROM items_compra WHERE id_compra = '".$this->id_compra."' ORDER BY nro_item ASC");

            $items = Array();

            if($q && $q->num_rows > 0){

                while($r= $q->fetch_object()){
                    $items[] = $r;
                }

                foreach($items as $item)
                {
                    
                    $this->items[$item->nro_item] = new Items_compra($this, $item);
                    
                }

            }

            mysqli_close($sql);
        }

        public function completar_datos(){

            $sql = sql_con();

            $query = "
                SELECT * 
                FROM gestor_compras_estatales_sandbox.compras as compras 
                INNER JOIN gestion_bd_sandbox.gestion_compras as gestion  
                ON compras.id_compra = gestion.id_compra 
                WHERE compras.id_compra = '".$this->id_compra."'";

            $q = $sql->query($query);

            if($q){

                $atributos;

                $r = $q->fetch_object();
                $atributos = $r;

                $this->num_compra =$atributos->num_compra;
                $this->anio_compra =$atributos->anio_compra;
                $this->id_inciso =$atributos->id_inciso;
                $this->id_ue =$atributos->id_ue;
                $this->id_ucc =$atributos->id_ucc;
                $this->estado_compra =$atributos->estado_compra;
                $this->fecha_publicacion =$atributos->fecha_publicacion;
                $this->fecha_ult_mod_llamado =$atributos->fecha_ult_mod_llamado;
                $this->id_tipocompra =$atributos->id_tipocompra;
                $this->subtipo_compra =$atributos->subtipo_compra;
                $this->objeto =$atributos->objeto;
                $this->fecha_hora_tope_entrega =$atributos->fecha_hora_tope_entrega;
                $this->estado_interno =$atributos->estado_interno;
                $this->nro_ampliacion = $atributos->nro_ampliacion;
                $this->nombre_pliego = $atributos->nombre_pliego;
                $this->fecha_hora_apertura = $atributos->fecha_hora_apertura;
                $this->lugar_apertura = $atributos->lugar_apertura;
                $this->fecha_sol_prorr = $atributos->fecha_sol_prorr;
                $this->fecha_sol_aclar = $atributos->fecha_sol_aclar;
                $this->fecha_hora_puja = $atributos->fecha_hora_puja;
                $this->lugar_entrega = $atributos->lugar_entrega;
                $this->precio_pliego = $atributos->precio_pliego;
                $this->id_moneda_pliego = $atributos->id_moneda_pliego;
                $this->lugar_compra_pliego = $atributos->lugar_compra_pliego;
                $this->nombre_contacto = $atributos->nombre_contacto;
                $this->fax_contacto = $atributos->fax_contacto;
                $this->email_contacto = $atributos->email_contacto;
                $this->fecha_pub_adj = $atributos->fecha_pub_adj;
                $this->fecha_compra = $atributos->fecha_compra;
                $this->fecha_vigencia_adj = $atributos->fecha_vigencia_adj;
                $this->fondos_rotatorios = $atributos->fondos_rotatorios;
                $this->apel = $atributos->apel;
                $this->arch_adj = $atributos->arch_adj;
                $this->monto_adj = $atributos->monto_adj;
                $this->id_moneda_monto_adj = $atributos->id_moneda_monto_adj;
                $this->id_tipo_resol = $atributos->id_tipo_resol;
                $this->num_resol = $atributos->num_resol;
                $this->es_reiteracion = $atributos->es_reiteracion;
                $this->arch_reiteracion = $atributos->arch_reiteracion;
                $this->get_ofertas_compra();
            }
        }

        public function mostrar_items(){

            if(count($this->items)>0){

                $return = "";
                
                foreach($this->items as $item ){

                    $return .= "<div class='div_item'>";

                    $tabla = "";

                    $tabla .=
                        "<tr>
                            <td>
                                <p>Ítem N° ".$item->nro_item."</p>
                            </td>
                            <td>
                                <p>".$item->desc_articulo."</p>
                            </td>
                            <td>
                                <p>(Cód. artículo ".$item->id_articulo.")</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Cantidad: ".$item->cant_pedida." ".$item->unidad."</p>
                            </td>
                        </tr>
                    ";

                    if($item->id_color != 0){
                        $tabla .= 
                            "<tr>
                                <td>Color:</td>
                                <td>".$item->desc_color."</td>
                            </tr>";
                    }

                    if($item->presentacion != '' && $item->presentacion != '-----' ){
                        $tabla .= 
                            "<tr>
                                <td>Presentación:</td>
                                <td>".$item->presentacion."</td>
                            </tr>";
                    }

                    $tabla = "<table class='tabla_item'>".$tabla."</table>";

                    $return = $return.$tabla.'</div>';
                    
                }
    
                return $return;

            }
        }

        public function mostrar_contacto(){
            $retorno = '<br><br>';

            if($this->contacto->nombre_contacto != NULL){
                $retorno = $retorno.'<div><a href="tel:'.$this->contacto->nombre_contacto.'">'.$this->contacto->nombre_contacto.'</a></div>';
            }

            if($this->contacto->fax_contacto != NULL){
                $retorno = $retorno.'<div><a href="tel:'.$this->contacto->fax_contacto.'">'.$this->contacto->fax_contacto.'</a></div>';
            }

            if($this->contacto->email_contacto != NULL){
                $retorno = $retorno.'<div><a href="mailto:'.$this->contacto->email_contacto.'">'.$this->contacto->email_contacto.'</a></div>';
            }

            return $retorno.'<br>';
        }

        public function aclaraciones_llamado_bd(){
            $sql = sql_con(DATABASE_COMPRAS);

            $q = $sql->query("DELETE FROM aclaraciones_llamado WHERE id_compra = '".$this->id_compra."'");

            foreach($this->aclaraciones_llamado as $aclaracion){
                $query = "INSERT INTO aclaraciones_llamado (id_compra, texto, fecha, nom_archivo) VALUES ('".$this->id_compra."', '".$aclaracion->texto."', '".$aclaracion->fecha."', '".$aclaracion->nom_archivo."')";
                $q = $sql->query($query);
            };
        }

        public function aclaraciones_adjudicacion_bd(){
            $sql = sql_con(DATABASE_COMPRAS);

            $q = $sql->query("DELETE FROM aclaraciones_adjudicacion WHERE id_compra = '".$this->id_compra."'");

            foreach($this->aclaraciones_adjudicacion as $aclaracion){
                $query = "INSERT INTO aclaraciones_adjudicacion (id_compra, texto, fecha, nom_archivo) VALUES ('".$this->id_compra."', '".$aclaracion->texto."', '".$aclaracion->fecha."', '".$aclaracion->nom_archivo."')";
                $q = $sql->query($query);
            };
        }

        public function actualizar_estado_compra($estado_interno){

            $this->estado_interno = $estado_interno;

            $sql = sql_con(DATABASE_GESTION);

            $query = "UPDATE gestion_compras SET estado_interno = ".$estado_interno." WHERE id_compra = '".$this->id_compra."'";
            $q = $sql->query($query);

            $this->estado_interno = $estado_interno;

            mysqli_close($sql);
        }

        public function get_subtipo_compra(){

            $sql = sql_con(DATABASE_CODIGUERAS);

            $q = $sql->query("SELECT resumen FROM subtipos_compra WHERE id_tipocompra = '".$this->id_tipocompra."' AND id_subtipo = '".$this->subtipo_compra."'");
            
            if($q->num_rows > 0){
                $r = $q->fetch_object();
                return $r->resumen;
            }

        }

        public function titulo(){

            // $sql = sql_con(DATABASE_CODIGUERAS);

            // $q = $sql->query("SELECT descripcion FROM tipos_compra WHERE id_tipo_compra = '".$this->id_tipocompra."'");

            // mysqli_close($sql);
        
            // if($q){
            //     while($r = $q->fetch_object()){
            //         $tipo_compra = $r;
            //     }
            // }

            $titulo = $_SESSION['sistema']->tipos_compra[$this->id_tipocompra]['nom'].' '.$this->num_compra.'/'.$this->anio_compra;

            return $titulo;
        }

        public function mostrar_ue(){

            $retorno = '';

            if($this->id_inciso != NULL){

                $inciso = $this->id_inciso;

                $sigla = sigla_inciso($inciso);

                if( $sigla != NULL ){

                    $retorno = $sigla;
                    
                } else {
                    $retorno = $_SESSION['sistema']->incisos[$inciso]['nom_inciso'];
                };

                $retorno = $retorno.' - '.$_SESSION['sistema']->incisos[$inciso]['ues'][$this->id_ue];

            } else if($this->id_ucc != NULL) {

                $retorno = $_SESSION['sistema']->uccs[$this->id_ucc];

            }

            return $retorno;
        }

        public function get_aclaraciones(){
            
            $sql = sql_con(DATABASE_COMPRAS);

            $this->aclaraciones_llamado = Array();
            $this->historial_modificaciones_llamado = Array();

            $q = $sql->query("SELECT * FROM aclaraciones_llamado WHERE id_compra = '".$this->id_compra."' ORDER BY fecha DESC");

            if($q){
                while($r = $q->fetch_object()){
                    $this->aclaraciones_llamado[] = $r;
                }
            }

            $p = $sql->query("SELECT * FROM historial_modificaciones_llamado WHERE id_compra = '".$this->id_compra."' ORDER BY fecha DESC");

            if($p){
                while($r = $p->fetch_object()){
                    $this->historial_modificaciones_llamado[] = $r;
                }
            }

        }

        public function detalle_llamado(){

            $this->get_aclaraciones();
            $this->get_ofertas_compra();
            $this->get_ofertas_compra_usuario();

            echo $this->objeto;

            if($this->aclaraciones_llamado != NULL){

                echo "<table id='acla_tab'>
                    <tr>
                        <th colspan='3'>Aclaraciones</th>
                    </tr>
                ";

                foreach($this->aclaraciones_llamado as $aclaracion){

                    $fecha = date('d/m/Y H:i', strtotime($aclaracion->fecha));

                    echo 
                        "<tr>
                            <td> ".$fecha."</td>
                            <td> ".$aclaracion->texto."</td>
                            <td><a href='".URL_ACLARACION.$aclaracion->nom_archivo."' target='_blank'><div id='descarga'></div></a><br></td>
                        </tr>";
                }

                echo "</table>";
            }

            if($this->historial_modificaciones_llamado != NULL){
                
                echo "<table id='acla_tab'>
                    <tr>
                        <th colspan='4'>Historial de modificaciones del llamado</th>
                    </tr>
                ";

                foreach($this->historial_modificaciones_llamado as $modificacion){

                    $fecha = date('d/m/Y H:i', strtotime($modificacion->fecha));

                    if($modificacion->campo != 'ESTADO' && $modificacion->campo != 'LUGAR_APERTURA' ){

                        if(strchr($modificacion->valor_anterior, ':')){
                            $valor_anterior = date('d/m/Y H:i', strtotime($modificacion->valor_anterior));
                            $valor_nuevo = date('d/m/Y H:i', strtotime($modificacion->valor_nuevo));
                        } else {
                            $valor_anterior = date('d/m/Y', strtotime($modificacion->valor_anterior));
                            $valor_nuevo = date('d/m/Y', strtotime($modificacion->valor_nuevo));
                        };

                    } else {
                        $valor_anterior = $modificacion->valor_anterior;
                        $valor_nuevo = $modificacion->valor_nuevo;
                    }

                    echo 
                        "<tr>
                            <td> ".$fecha."</td>
                            <td> ".$modificacion->campo."</td>
                            <td> ".$valor_anterior."</td>
                            <td> ".$valor_nuevo."</td>
                        </tr>";
                }

                echo "</table><br><br><br>";
            }



        }

        public function barra_lateral(){

            $cabezal = "";

            $tabla = "";

            $estado = $this->estado_compra;

            $tabla .= 
                    "<tr>
                        <td>Publicado: </td>
                        <td>".date('d/m/y H:i', strtotime($this->fecha_publicacion))."</td>
                    </tr>";

            if( $estado == 4 || $estado == 5 ) {
                
                if( $this->apel == 'S' ){
                    $cabezal .= "<div id='apertura_electronica' onclick=\"redir_apel('".$this->id_compra."')\"><p>Ofertar en línea</p></div>";
                }

                $tabla .= 
                    "<tr>
                        <td>Ofertas hasta: </td>
                        <td>".date('d/m/y H:i', strtotime($this->fecha_hora_tope_entrega))."</td>
                    </tr>";

                $fecha_sol_aclar = $this->fecha_sol_aclar;

                $fecha_sol_prorr = $this->fecha_sol_prorr;

                $lugar_apertura = $this->lugar_apertura;

                $fecha_hora_apertura = $this->fecha_hora_apertura;

                $nombre_pliego = $this->nombre_pliego;

                if( $nombre_pliego != NULL ){
                    $tabla .= 
                        "<tr>
                            <td>Archivo adjunto: </td>
                            <td><a href='https://www.comprasestatales.gub.uy/Pliegos/".$nombre_pliego."' target='_blank'><div id='descarga'></div></a><br></td>
                        </tr>";
                }
                
                if( $fecha_sol_aclar != NULL ){
                    $tabla .= 
                        "<tr>
                            <td>Aclaraciones hasta: </td>
                            <td>".date('d/m/Y', strtotime($fecha_sol_aclar.' 00:00:00'))."</td>
                        </tr>";
                }

                if( $fecha_sol_prorr != NULL ){
                    $tabla .=  
                        "<tr>
                            <td>Prórrogas hasta: </td>
                            <td>".date('d/m/Y', strtotime($fecha_sol_prorr.' 00:00:00'))."</td>
                        </tr>";
                }
                

                if( $lugar_apertura != NULL ){

                    $tabla .=  
                        "<tr>
                            <td>Lugar de apertura: </td>
                            <td>".$lugar_apertura."</td>
                        </tr>";

                }

                if( $fecha_hora_apertura != NULL ){

                    $tabla .=  
                        "<tr>
                            <td>Fecha de apertura: </td>
                            <td>".date('d/m/Y H:i', strtotime($fecha_hora_apertura))."</td>
                        </tr>";

                }

                if( $this->lugar_entrega != NULL ){

                    $tabla .=  
                        "<tr>
                            <td>Lugar de entrega: </td>
                            <td>".$this->lugar_entrega."</td>
                        </tr>";

                }

                $tabla .= "<tr><td colspan='2'><a href='".URL_COMPRA.$this->id_compra."' target='_blank'><div id='boton_arce'><p>Abrir en ARCE</p></div></a><br></td></tr>";

                $tabla = "<table id='tabla_lateral'>".$tabla."</table>";

                echo $cabezal;

                echo $tabla;


            } else if( $estado == 11 ){

                $fecha_ult_mod_llamado = $this->fecha_ult_mod_llamado;

            }

        }

        public function llamado_visto(){

            $sql = new mysqli('localhost', 'root', '', DATABASE_GESTION);
            $sql->set_charset('utf8');

            $q = $sql->query("SELECT * FROM gestion_compras WHERE id_compra = '".$this->id_compra."'");

            $compra = NULL;

            if($q){
                while($r = $q->fetch_object()){
                    $compra = $r;
                }
            }

            if($compra->estado_interno == 0){

                $q = $sql->query("UPDATE gestion_compras SET estado_interno = 1 WHERE id_compra = '".$this->id_compra."'");

            }

            mysqli_close($sql);

        }

        public function get_ofertas_compra_usuario(){

            $usuario = $_SESSION['user']->id_usuario;
            $this->ofertas_usuario = Array();
 
            $sql = sql_con(DATABASE_GESTION);

            $q = $sql->query('SELECT * FROM ofertas_compra WHERE id_compra = "'.$this->id_compra.'" AND responsable = '.$usuario.' ORDER BY id_oferta DESC');
            
            if($q){
                while($r = $q->fetch_object()){
                    $this->ofertas_usuario[] = new Ofertas_Compra($r);
                }
            }

        }

        public function get_ofertas_compra(){

            $this->ofertas = Array();
 
            $sql = sql_con(DATABASE_GESTION);

            $q = $sql->query('SELECT * FROM ofertas_compra WHERE id_compra = "'.$this->id_compra.'" ORDER BY id_oferta DESC');
            
            if($q){
                while($r = $q->fetch_object()){
                    $this->ofertas[] = new Ofertas_Compra($r);
                }
            }

        }

        public function mostrar_ofertas_llamado(){

            $tabla = '';

            if( count($this->ofertas) > 0 ){

                $tabla = "
                <table id='tabla_ofertas_compra'>
                    <tr id='tr_head_ofertas'>   
                        <th>Id oferta</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                    </tr>
                ";

                foreach($this->ofertas as $oferta){

                    $id_oferta = ltrim(substr(strchr($oferta->id_oferta, '_'), 1), '0');

                    $tabla .= 
                        "<tr>
                            <td>".$id_oferta."</td>
                            <td>".$oferta->responsable."</td>
                            <td>".estado_oferta($oferta->estado)['nom']."</td>
                            <td><p onclick='detalle_oferta(\"".$oferta->id_oferta."\")'>Modificar</p></td>
                        </tr>";
                }

                $tabla .= "</table>";
                
            }

            return $tabla;
        }

        public function crear_oferta_llamado(){

            $fecha = date('Y-m-d H:i:s');

            if( count($_SESSION['compra_actual']->ofertas) > 0 ){

                $num = (int)substr(strchr($_SESSION['compra_actual']->ofertas[0]->id_oferta, '_'), 1);

                $num = (string)++$num;

            } else {

                $num = (string)1;

            }

            $num = str_pad($num, 4, '0', STR_PAD_LEFT);

            $id_oferta = $this->id_compra.'_'.$num;

            $sql = sql_con(DATABASE_GESTION);

            $q = $sql->query('INSERT INTO ofertas_compra ( id_oferta , id_compra , responsable , fecha_alta_oferta , fecha_ult_mod_oferta ) VALUES ( "'.$id_oferta.'" , "'.$this->id_compra.'" , "'.$_SESSION['user']->id_usuario.'" , "'.$fecha.'" , "'.$fecha.'" )');

            $this->get_ofertas_compra_usuario();

            mysqli_close($sql);

            return $id_oferta;

        }

        public function mostrar_items_oferta(){

            $sql = sql_con(DATABASE_GESTION);

            $q = $sql->query('SELECT * FROM items_ofertas WHERE id_compra = ');

            if(count($this->items)>0){

                $return = "";
                
                foreach($this->items as $item ){

                    $return .= "<div class='div_item'>";

                    $tabla = "";

                    $tabla .=
                        "<tr>
                            <td>
                                <p>Ítem N° ".$item->nro_item."</p>
                            </td>
                            <td>
                                <p>".$item->desc_articulo."</p>
                            </td>
                            <td>
                                <p>(Cód. artículo ".$item->id_articulo.")</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Cantidad: ".$item->cant_pedida." ".$item->unidad."</p>
                            </td>
                        </tr>
                    ";

                    if($item->id_color != 0){
                        $tabla .= 
                            "<tr>
                                <td>Color:</td>
                                <td>".$item->desc_color."</td>
                            </tr>";
                    }

                    if($item->presentacion != '' && $item->presentacion != '-----' ){
                        $tabla .= 
                            "<tr>
                                <td>Presentación:</td>
                                <td>".$item->presentacion."</td>
                            </tr>";
                    }

                    $tabla .=    
                        '<tr>
                            <td colspan="2"><div class="crear_opcion_item" onclick="crear_opcion_item(\''.$this->id_compra.'\', \''.$item->nro_item.'\')">Ofertar ítem</div></td>
                        </tr>';

                    $tabla = "<table class='tabla_item'>".$tabla."</table>";

                    $return .= $tabla;
                    
                    $return .= '<div class="opciones_item"></div></div>';
                    
                }
    
                return $return;

            }
        }

        public function nuevo_requerimiento(){

            $sql = sql_con(DATABASE_GESTION);

            $q = "SELECT MAX(nro_requerimiento) as max FROM requerimientos_compra WHERE id_compra = '".$this->id_compra."'";

            $q = $sql->query($q);

            $nro_requerimiento = 0;

            if($q){

                $r = $q->fetch_object();
                if($r->max != ''){
                    $nro_requerimiento = $r->max + 1;
                }

            }

            mysqli_close($sql);

            $requerimiento = new Requerimiento(NULL , $nro_requerimiento);

            $this->requerimientos[$requerimiento->nro_requerimiento] = $requerimiento;

            return $requerimiento->nro_requerimiento;

        }

        public function get_requerimientos(){
            
            $sql = sql_con();

            $q = $sql->query('SELECT * FROM gestion_bd_sandbox.requerimientos_compra WHERE id_compra = "'.$this->id_compra.'"');

            if($q){

                while($r = $q->fetch_object()){
                    $requerimiento = new Requerimiento( NULL , $r );
                    $this->requerimientos[$requerimiento->nro_requerimiento] = $requerimiento;
                }
                
            }
        }

        public function show_requerimientos(){

            $return = '
                <table>
                    <tr>
                        <th>Tipo</th>
                        <th>Comentario</th>
                        <th>Obligatorio</th>
                        <th>Adjunto</th>
                        <th>Realizado</th>
                    </tr>
            ';

            foreach($this->requerimientos as $requerimiento){
                $return .= '
                    <tr>
                        <td>'.$requerimiento->tipo_requerimiento.'</td>
                        <td>'.$requerimiento->comentario.'</td>
                        <td>'.$requerimiento->obligatorio.'</td>
                        <td>'.$requerimiento->adjunto.'</td>
                        <td>'.$requerimiento->realizado.'</td>
                    </tr>
                
                ';
            }

            $return .= '</table>';

        }

        public function botones_estado(){

            $estructura = '';
            
            if($this->estado_compra==4){
    
                if( $this->estado_interno != 4){
                    $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$this->id_compra.'", 4)\'>Desestimar</button></br>';
                }
                
                if($this->estado_interno != 2){
                    $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$this->id_compra.'", 2)\'>Guardar</button></br>';
                }
    	
            }	
            
            return $estructura;

        }

        public function visor_llamado(){

            $this->completar_datos();
    
            $botones = $this->botones_estado();
    
            $estado_compra = $this->estado_compra;
            $estado_interno = $this->estado_interno;
            $estado_interno_txt = estado_interno($this->estado_interno);
            $fecha_lim_rof = strtotime($this->fecha_hora_tope_entrega);
    
           
            $div_botones = '';
            $div_rof = '';
    
            if ( ($estado_compra == 4 || $estado_compra == 5) && $fecha_lim_rof > time() ) {
                $div_botones = 
                    '<div id="botones'.$this->id_compra.'">
                        '.$botones.'
                    </div>';
    
                $div_rof = 
                    '<div class="div_rof">
                        Recepción de ofertas hasta: '.$this->recepcion_ofertas().'
                    </div>';
            } else if ( $this->fecha_pub_adj != NULL ) {
                $div_rof = 
                    '<div class="div_rof">
                        Fecha publicación adj: '.$this->fecha_pub_adj.'
                    </div>';
            }
    
            $return = 
            '<div class="contCompra '.$estado_interno_txt[0].' '.estado_compra($this->estado_compra).'" id="'.$this->id_compra.'">
                <div class="visor">
                    <a href="detalle.php?id_compra='.$this->id_compra.'">
                        <h2>'.$this->titulo().'</h2>
                    </a>
                        <div>'.$this->mostrar_ue().'</div>
                        '.$div_rof.'
                        <div class="descCompra">
                            '.$this->objeto.'
                        </div>
                </div>
                '.$div_botones.'
            </div>';
        
            echo $return;

        }

        public function recepcion_ofertas(){

            $fecha = date('d/m/Y H:i', strtotime($this->fecha_hora_tope_entrega));

            return $fecha;


        }

    }

    Class Ofertas_Compra {

        public $id_compra;
        public $id_oferta;
        public $estado;
        public $nombre_oferta;
        public $responsable;
        public $fecha_alta;
        public $fecha_ult_mod;
        public $resolucion;
        
        public $documentos_oferta = Array();
        public $items_oferta = Array();

        public function __construct($oferta){
            $this->id_compra = $oferta->id_compra;
            $this->id_oferta = $oferta->id_oferta;
            $this->estado = $oferta->estado_oferta;
            $this->responsable = $oferta->responsable;
            $this->fecha_alta = $oferta->fecha_alta_oferta;
            $this->fecha_ult_mod = $oferta->fecha_ult_mod_oferta;
        }

    }

    class Items_Compra{

        public $id_compra;
        public $nro_item;
        public $cant_pedida;
        public $id_moneda_cotiz;
        public $fecha_hora_puja;
        public $durac_puja;
        public $tipo_margen_puja;
        public $margen_puja;
        public $id_articulo;
        public $desc_articulo;
        public $id_color;
        public $desc_color;
        public $id_unidad;
        public $unidad;
        public $id_variante;
        public $variante;
        public $unidad_medida_variante;
        public $medida_variante;
        public $presentacion;
        public $medida_presentacion;
        public $unidad_medida_presentacion;
        public $id_detalle_variante;
        public $desc_detalle_variante;
        public $id_marca;
        public $desc_marca;
        public $atributos_items_compra = Array();

        public function __construct($compra, $item)
            {
                $this->id_compra = $compra->id_compra;;
                $this->nro_item = $item->nro_item;
                $this->cant_pedida = $item->cant_pedida;
                $this->id_moneda_cotiz = $item->id_moneda_cotiz;
                $this->fecha_hora_puja = $item->fecha_hora_puja;
                $this->duracion_puja = $item->duracion_puja;
                $this->tipo_margen_puja = $item->tipo_margen_puja;
                $this->margen_puja = $item->margen_puja;
                $this->id_articulo = $item->id_articulo;
                $this->desc_articulo = $item->desc_articulo;
                $this->id_color = $item->id_color;
                $this->desc_color = $item->desc_color;
                $this->id_unidad = $item->id_unidad;
                $this->unidad = $item->unidad;
                $this->id_variante = $item->id_variante;
                $this->variante = $item->variante;
                $this->unidad_medida_variante = $item->unidad_medida_variante;
                $this->medida_variante = $item->medida_variante;
                $this->presentacion = $item->presentacion;
                $this->medida_presentacion = $item->medida_presentacion;
                $this->unidad_medida_presentacion = $item->unidad_medida_presentacion;
                $this->id_detalle_variante = $item->id_detalle_variante;
                $this->desc_detalle_variante = $item->desc_detalle_variante;
                $this->id_marca = $item->id_marca;
                $this->desc_marca = $item->desc_marca;
                $this->set_atributos_items();
        }

        public function set_atributos_items(){
            $sql = sql_con(DATABASE_COMPRAS);

            $q = $sql->query("SELECT * FROM atributos_items_compra WHERE id_compra = '".$this->id_compra."' AND nro_item = ".$this->nro_item);

            if($q && $q->num_rows > 0){

                while($r= $q->fetch_object()){
                    $atributos[] = $r;
                }

                foreach($atributos as $atributo)
                {
                    $this->atributos_items_compra[$atributo->id_prop_atributo] = new Atributos_items_compra($this, $atributo);
                }
            }
        }
    }

    class Atributos_items_compra {

        public $id_prop_atributo;
        public $desc_prop_atributo;
        public $id_unidad_med_prop_atributo;
        public $desc_unidad_med_prop_atributo;
        public $requerido;
        public $cod_condicion;
        public $valor_numerico;
        public $valor_texto;
        public $valor_fecha;
        public $valor_booleano;

        public function __construct(
            $item,
            $atributo
        )
        {
            $this->id_prop_atributo = $atributo->id_prop_atributo;
            $this->desc_prop_atributo = $atributo->desc_prop_atributo;
            $this->id_unidad_med_prop_atributo = $atributo->id_unidad_med_prop_atributo;
            $this->desc_unidad_med_prop_atributo = $atributo->desc_unidad_med_prop_atributo;
            $this->requerido = $atributo->requerido;
            $this->cod_condicion = $atributo->cod_condicion;
            $this->valor_numerico = $atributo->valor_numerico;
            $this->valor_texto = $atributo->valor_texto;
            $this->valor_fecha = $atributo->valor_fecha;
            $this->valor_booleano = $atributo->valor_booleano;
        }

    }

    class Valores_atributos_items_compra {
    }

    class Aclaraciones_llamado {
        public $id_compra;
        public $texto;
        public $fecha;
        public $nom_archivo;

        public function __construct($compra, $aclaraciones_llamado){
            $this->id_compra = $compra->id_compra;
            $this->texto = $aclaraciones_llamado->texto;
            $this->fecha = $aclaraciones_llamado->fecha;
            $this->nom_archivo = $aclaraciones_llamado->nom_archivo;
        }
    }

    class Aclaraciones_adjudicacion {
        public $id_compra;
        public $texto;
        public $fecha;
        public $nom_archivo;

        public function __construct($compra, $aclaraciones_adjudicacion){
            $this->id_compra = $compra->id_compra;
            $this->texto = $aclaraciones_adjudicacion->texto;
            $this->fecha = $aclaraciones_adjudicacion->fecha;
            $this->nom_archivo = $aclaraciones_adjudicacion->nom_archivo;
        }
    }

    class Modificaciones_llamado {
        public $id_compra;
        public $fecha;
        public $campo;
        public $valor_anterior;
        public $valor_nuevo;

        public function __construct($compra, $modificaciones_llamado){
            $this->id_compra = $compra->id_compra;
            $this->fecha = $modificaciones_llamado->fecha;
            $this->campo = $modificaciones_llamado->campo;
            $this->valor_anterior = $modificaciones_llamado->valor_anterior;
            $this->valor_nuevo = $modificaciones_llamado->valor_nuevo;
        }
    }

    class Items_adjudicacion {
        public $nro_item;
        public $tipo_doc_prov;
        public $nro_doc_prov;
        public $nombre_comercial;
        public $cant_adj;
        public $precio_unit;
        public $precio_tot_imp;
        public $id_moneda;
        public $id_articulo;
        public $desc_articulo;
        public $id_color;
        public $desc_color;
        public $id_unidad;
        public $unidad;
        public $id_variante;
        public $variante;
        public $unidad_medida_variante;
        public $medida_variante;
        public $presentacion;
        public $medida_presentacion;
        public $unidad_medida_presentacion;
        public $id_detalle_variante;
        public $desc_detalle_variante;
        public $id_marca;
        public $desc_marca;
        public $variacion;

        public function __construct($adjudicacion){
            $this->nro_item = $adjudicacion->nro_item;
            $this->tipo_doc_prov = $adjudicacion->tipo_doc_prov;
            $this->nro_doc_prov = $adjudicacion->nro_doc_prov;
            $this->nombre_comercial = $adjudicacion->nombre_comercial;
            $this->cant_adj = $adjudicacion->cant_adj;
            $this->precio_unit = $adjudicacion->precio_unit;
            $this->precio_tot_imp = $adjudicacion->precio_tot_imp;
            $this->id_moneda = $adjudicacion->id_moneda;
            $this->id_articulo = $adjudicacion->id_articulo;
            $this->desc_articulo = $adjudicacion->desc_articulo;
            $this->id_color = $adjudicacion->id_color;
            $this->desc_color = $adjudicacion->desc_color;
            $this->id_unidad = $adjudicacion->id_unidad;
            $this->unidad = $adjudicacion->unidad;
            $this->id_variante = $adjudicacion->id_variante;
            $this->variante = $adjudicacion->variante;
            $this->unidad_medida_variante = $adjudicacion->unidad_medida_variante;
            $this->medida_variante = $adjudicacion->medida_variante;
            $this->presentacion = $adjudicacion->presentacion;
            $this->medida_presentacion = $adjudicacion->medida_presentacion;
            $this->unidad_medida_presentacion = $adjudicacion->unidad_medida_presentacion;
            $this->id_detalle_variante = $adjudicacion->id_detalle_variante;
            $this->desc_detalle_variante = $adjudicacion->desc_detalle_variante;
            $this->id_marca = $adjudicacion->id_marca;
            $this->desc_marca = $adjudicacion->desc_marca;
            $this->variacion = $adjudicacion->variacion;
        }

    }

    class Contactos {

        public $id_contacto;
        public $id_compra;
        public $nombre_contacto;
        public $fax_contacto;
        public $email_contacto;
        public $id_ue;
        public $id_inciso;
        public $id_ucc;

        public function __construct($compra){
            $this->nombre_contacto = $compra->nombre_contacto;
            $this->fax_contacto = $compra->fax_contacto;
            $this->email_contacto = $compra->email_contacto;
            $this->id_ue = $compra->id_ue;
            $this->id_inciso = $compra->id_inciso;
            $this->id_ucc = $compra->id_ucc;
            
            $sql = sql_con(DATABASE_COMPRAS);

            $q;

            if($this->id_ucc != NULL){
                $q = $sql->query("SELECT * FROM contactos WHERE nombre_contacto = '".$compra->nombre_contacto."' AND id_ucc = ".$this->id_ucc);
            } else {
                $q = $sql->query("SELECT * FROM contactos WHERE nombre_contacto = '".$compra->nombre_contacto."' AND id_ue = ".$this->id_ue." AND id_inciso = ".$this->id_inciso);
            }

            if($q->num_rows == 0){

                $q = $sql->query("SELECT MAX(id_contacto) as id_contacto FROM contactos");
                
                if($q->num_rows > 0){
                    $id = $q->fetch_object();
                    $this->id_contacto = $id->id_contacto + 1;
                } else {
                    $this->id_contacto = 0; 
                }

                if($this->id_ucc != NULL){
                    $q = $sql->query("INSERT INTO contactos ( id_contacto , id_ucc , nombre_contacto ) VALUES ( ".$this->id_contacto." , ".$this->id_ucc." , '".$this->nombre_contacto."')");
                } else {
                    $q = $sql->query("INSERT INTO contactos ( id_contacto , id_ue , id_inciso , nombre_contacto ) VALUES ( ".$this->id_contacto." , ".$this->id_ue." , ".$this->id_inciso." , '".$this->nombre_contacto."')");
                }

            } else {
                $r = $q->fetch_object();
                $this->id_contacto = $r->id_contacto;
            }

            if($this->fax_contacto != NULL){
                $q = $sql->query("SELECT * FROM telefonos_contacto WHERE id_contacto = ".$this->id_contacto." AND numero = '".$this->fax_contacto."'");

                if($q->num_rows == 0){
                    $q = $sql->query("INSERT INTO telefonos_contacto ( id_contacto , numero ) VALUES ( ".$this->id_contacto.", '".$this->fax_contacto."' )");
                }
            }

            if($this->email_contacto != NULL){
                $q = $sql->query("SELECT * FROM mails_contacto WHERE id_contacto = ".$this->id_contacto." AND mail = '".$this->email_contacto."'");

                if($q->num_rows == 0){
                    $q = $sql->query("INSERT INTO mails_contacto ( id_contacto , mail ) VALUES ( ".$this->id_contacto.", '".$this->email_contacto."' )");
                }
            }

            $q = $sql->query("SELECT * FROM contactos_compra WHERE id_contacto = ".$this->id_contacto." AND id_compra = '".$this->id_compra."'");

            if($q->num_rows ==+ 0){
                $q = $sql->query("INSERT INTO contactos_compra ( id_contacto , id_compra) VALUES ( ".$this->id_contacto." , '".$this->id_compra."' )");
            }

            mysqli_close($sql);
        }

    }

    class Sistema {

        // GESTION DE SESION

        public ?string $inicio_sesion;
        public $historial = Array();
        public $error = Array();
        public $ip_origen;

        //GESTION USUARIOS

        public $usuario = NULL;
        public Array $usuarios;
        
        //ARCE

        private string $_fecha_primer_registro;
        private int $_total_llamados_bd;
        private int $_llamados_vigentes;
        private int $_llamados_expirados;
        private int $_adjudicaciones;

        //GESTION INTERNA

        private string $_fecha_ult_act_bd;
        private int $_llamados_nuevos;
        private int $_llamados_vistos;
        private int $_llamados_guardados;
        private int $_llamados_oferta_vigente;
        private int $_llamados_desestimados;

        //OFERTAS

        private int $_llamados_ofertados_total;
        private int $_ofertas_ganadas;
        private int $_ofertas_perdidas;
        private int $_ofertas_desestimadas;
        private Array $_total_montos_adjudicacion;

        //CACHE DE LLAMADOS

        public Array $seleccion_llamados = [];
        public Array $seleccion_claves;
        public int $cantidad_pagina = 25;
        public int $pagina_actual = 0;
        public int $pagina_anterior;
        public string $clave_busqueda;
        public string $objeto = '';

        //CODIGUERAS

        public Array $incisos;
        public Array $uccs;
        public Array $tipos_compra;
        public Array $sectores;
        public Array $tipos_requerimiento;

        //SUBCONTRATISTAS

        public Array $subcontratistas;

        public function __construct(){
            $this->inicio_sesion = date('Y-m-d H:i:s');
            $this->get_codigueras();
            $this->get_subcontratistas();
            $this->get_sectores();
            $this->get_usuarios();
            $this->get_requerimientos();
            //$this->ip_origen = $_SERVER['REMOTE_ADDR'];
        }

        public function get_codigueras(){

            $sql = sql_con(DATABASE_CODIGUERAS);

            $q = $sql->query('SELECT * FROM incisos');

            if($q){
                while($r = $q->fetch_object()){
                    $this->incisos[$r->id_inciso] = Array();
                    $this->incisos[$r->id_inciso]['nom_inciso'] = $r->nom_inciso;
                    $this->incisos[$r->id_inciso]['ues'] = Array();
                }
            }

            $q = $sql->query('SELECT * FROM unidades_ejecutoras');

            if($q){
                while($r = $q->fetch_object()){
                    $this->incisos[$r->id_inciso]['ues'][$r->id_ue] = $r->nom_ue;
                }
            }

            $q = $sql->query('SELECT * FROM unidades_compra_centralizadas');

            if($q){
                while($r = $q->fetch_object()){
                    $this->uccs[$r->id_ucc] = $r->nom_ucc;
                }
            }

            foreach($this->incisos as $clave => $inciso){

                $sigla = sigla_inciso($clave);

                if($sigla != NULL){
                    $this->incisos[$clave]['sigla'] = $sigla;
                }
            }

            $q = $sql->query('SELECT * FROM tipos_compra');

            if($q){
                while($r = $q->fetch_object()){
                    $this->tipos_compra[$r->id_tipo_compra]['sigla'] = $r->id_tipo_compra;
                    $this->tipos_compra[$r->id_tipo_compra]['nom'] = $r->descripcion;
                }
            }

            print_r($this->tipos_compra);

            mysqli_close($sql);

        }

        public function get_sectores(){
            
            $sql = sql_con(DATABASE_GESTION);

            $q = "SELECT * FROM sectores";

            $q = $sql->query($q);

            $result = Array();

            if($q){

                while($r = $q->fetch_object()){

                    $result['id'] = $r->id_sector;
                    $result['nombre'] = $r->nombre;
                    $this->sectores[$r->id_sector] = $result;

                }

            }

            mysqli_close($sql);

        }

        public function get_subcontratistas(){

            $sql = sql_con(DATABASE_GESTION);

            $query = "SELECT * FROM subcontratistas WHERE fecha_baja IS NULL";

            $q = $sql->query($query);

            if($q){

                while($r = $q->fetch_object()){

                    $this->subcontratistas[$r->id_subcontratista] = $r;

                }

            }

        }

        public function get_requerimientos(){

            $sql = sql_con(DATABASE_GESTION);

            $q = "SELECT * FROM tipos_requerimiento WHERE fecha_baja IS NULL";

            $q = $sql->query($q);

            if($q){

                while($r = $q->fetch_object()){

                    $result = Array();
                    $result['id'] = $r->id_requerimiento;
                    $result['descripcion'] = $r->descripcion;
                    $this->tipos_requerimiento[$r->id_requerimiento] = $result;

                }
            }

            mysqli_close($sql);
        }

        public function get_informe(){
            $fecha_hora_actual = date('Y-m-d H:i:s');

            $_SESSION['vigentes'] = Array();
            $_SESSION['desestimados'] = Array();

            $sql = new mysqli('localhost', 'root', '', '');

            $q = $sql->query('SELECT COUNT(id_compra) as _count FROM gestor_compras_estatales_sandbox.compras');

            if($q){
                $this->_total_llamados_bd = $q->fetch_object()->_count;
            }

            $q = $sql->query("SELECT id_compra FROM gestor_compras_estatales_sandbox.compras WHERE fecha_hora_tope_entrega > '".$fecha_hora_actual."'");

            $contador = 0;

            if($q){
                while($r = $q->fetch_object()){
                    $_SESSION['vigentes'][$r->id_compra] = $r->id_compra;
                    $contador++;
                }
            }

            $this->_llamados_vigentes = $contador;

            $q = $sql->query("SELECT COUNT(id_compra) as _count FROM gestion_bd_sandbox.compras_adjudicadas");

            if($q){
                $this->_adjudicaciones = $q->fetch_object()->_count;
            }

            $q = $sql->query("SELECT fecha_ult_act as fecha FROM gestion_bd_sandbox.fecha_ult_actualizacion WHERE contador = (SELECT MAX(contador) FROM gestion_bd_sandbox.fecha_ult_actualizacion)");

            if($q){
                if($q->num_rows > 0){
                    $this->_fecha_ult_act_bd = $q->fetch_object()->fecha;
                } else {
                    $this->_fecha_ult_act_bd = "Nunca.";
                }
            } else {
                $this->_fecha_ult_act_bd = 'Ocurrió un error al recibir la información.';
            }

            $q = $sql->query("SELECT COUNT(gestion.id_compra) AS _count FROM gestion_bd_sandbox.gestion_compras AS gestion INNER JOIN gestor_compras_estatales_sandbox.compras AS compras ON gestion.id_compra = compras.id_compra WHERE gestion.estado_interno < 2 AND compras.fecha_hora_tope_entrega > '".$fecha_hora_actual."'");

            if($q){
                $this->_llamados_nuevos = $q->fetch_object()->_count;
            }

            $q = $sql->query("SELECT COUNT(id_compra) AS _count FROM gestion_bd_sandbox.gestion_compras WHERE estado_interno > 0 ");

            if($q){
                $this->_llamados_vistos = $q->fetch_object()->_count;
            }
            
            $q = $sql->query("SELECT COUNT(id_compra) AS _count FROM gestion_bd_sandbox.gestion_compras WHERE estado_interno = 2 OR estado_interno =3 ");

            if($q){
                $this->_llamados_guardados = $q->fetch_object()->_count;
            }

            $q = $sql->query("SELECT id_compra FROM gestion_bd_sandbox.gestion_compras WHERE estado_interno = 4");

            $contador = 0;

            if($q){
                while($r = $q->fetch_object()){
                    $_SESSION['desestimados'][$r->id_compra] = $r->id_compra;
                    $contador++;
                }
            }

            $this->_llamados_desestimados = $contador;

            $sql->close();
        }

        public function mostrar_informe(){
            echo "<div id='monitor'>";
            echo "Compras registradas en la base de datos: ".$this->_total_llamados_bd."<br>";
            echo "Llamados vigentes: ".$this->_llamados_vigentes."<br>";
            echo "Adjudicaciones registradas en la base de datos: ".$this->_adjudicaciones."<br>";
            echo "Fecha de ultima actualización de la base de datos: ".$this->_fecha_ult_act_bd."<br><br>";
            
            echo "Llamados nuevos: ".$this->_llamados_nuevos."<br>";
            echo "Llamados vistos: ".$this->_llamados_vistos."<br>";
            echo "Llamados guardados: ".$this->_llamados_guardados."<br>";
            echo "Llamados desestimados: ".$this->_llamados_desestimados."<br>";
            echo "</div>";
        }

        public function registrar_error($error){
            $this->error[] = $error;
        }

        public function limpiar_error(){
            $this->error = Array();
        }

        public function show_ip(){
            echo $this->ip_origen;
        }

        public function set_user($user){
            $this->usuario = $user;
        }

        public function get_usuarios(){

            $sql = sql_con(GESTION_USUARIOS);

            $q = "SELECT * FROM usuarios";

            $q = $sql->query($q);

            if($q){

                while($r = $q->fetch_object()){
                    $this->usuarios[$r->id_usuario] = $r;
                }

            }

            mysqli_close($sql);

        }

        public function buscar_llamados(){
            
            $array_seleccion = Array();
        
        }

        public function vaciar_seleccion(){
            $this->seleccion_llamados = Array();
        }

        public function obtener_compra($id){

            $sql = sql_con(DATABASE_COMPRAS);
            
            $query = "
            SELECT * 
            FROM gestor_compras_estatales_sandbox.compras as compras 
            INNER JOIN gestion_bd_sandbox.gestion_compras as gestion  
            ON compras.id_compra = gestion.id_compra 
            WHERE compras.id_compra = '".$id."'";

            $q = $sql->query($query);

            $compra = NULL;

            if($q){
                while($r = $q->fetch_object()){
                    $compra = $r;
                }
            }

            $compra = new Compras($compra);

            $this->compra_actual = $compra;

            return $compra;

        }
        
    }
    
    class Sesion_usuario {

        //INFO DE USUARIO

        public int $id_usuario;
        public string $nombre;
        public string $apellido;
        public int $perfil_usuario;
        public string $fecha_alta_usuario;

        //GESTION DE SESION DE USUARIO
        
        public bool $bool_session = false;
        public int $inicio_sesion;
        public int $ult_actividad;
        public string $fecha_ult_logout;

        //INFO DE HISTORICO DE TRABAJO

        public int $llamados_vistos_gral;
        public int $llamados_catalogados_gral;
        public int $llamados_guardados_gral;
        public int $llamados_desestimados_gral;
        public int $llamados_presupuestados_gral;
        public int $llamados_ofertados_gral;
        public int $tiempo_plataforma_gral;

        public int $llamados_vistos_sesion = 0;
        public int $llamados_catalogados_sesion = 0;
        public int $llamados_guardados_sesion = 0;
        public int $llamados_desestimados_sesion = 0;
        public int $llamados_presupuestados_sesion = 0;
        public int $llamados_ofertados_sesion = 0;
        public int $tiempo_plataforma_sesion = 0;

        public function __construct($usuario, $contraseña){

            $user = NULL;
            $contraseña = md5($contraseña);

            $sql = sql_con(GESTION_USUARIOS);

            $query = "SELECT * FROM usuarios WHERE usuario = '".$usuario."'";

            $q = $sql->query($query);

            if($q){
                while($r = $q->fetch_object()){
                    $user = $r;
                }
            }

            mysqli_close($sql);

            if($contraseña === $user->hash_password){
                $this->bool_session = true;
                $this->inicio_sesion = time();
                $this->ult_actividad = $this->inicio_sesion;
                $this->id_usuario = $user->id_usuario;
                $this->nombre = $user->nombre;
                $this->apellido = $user->apellido;
                $this->perfil_usuario = $user->perfil_usuario;
                $this->fecha_alta_usuario = $user->fecha_ingreso_plataforma;

                $this->llamados_vistos_gral = $user->llamados_vistos;
                $this->llamados_catalogados_gral = $user->llamados_catalogados;
                $this->llamados_guardados_gral = $user->llamados_guardados;
                $this->llamados_desestimados_gral = $user->llamados_desestimados;
                $this->llamados_presupuestados_gral = $user->llamados_presupuestados;
                $this->llamados_ofertados_gral = $user->llamados_ofertados;
                $this->tiempo_plataforma_gral = $user->tiempo_plataforma;
            }
        }

        public function marca_reloj(){
            
            $actual = time();

            if(($this->ult_actividad + 7200) < $actual ){
                $this->cerrar_sesion(false);
            } else if (($this->ult_actividad + 30) > $actual ) {
                $this->tiempo_plataforma_sesion += 30;
                $this->ult_actividad = time();
            } else {
                $this->tiempo_plataforma_sesion += ($actual - $this->ult_actividad);
                $this->ult_actividad = time();
            }
        }

        public function cerrar_sesion($bool){
            
            $this->bool_session = false;

            $tiempo = 0;

            if($bool){
                $tiempo = time() - $this->ult_actividad;
            }

            $tiempo = $tiempo + $this->tiempo_plataforma_sesion + $this->tiempo_plataforma_gral;

            $sql = sql_con(GESTION_USUARIOS);

            $query = "UPDATE usuarios SET tiempo_plataforma = ".$tiempo." WHERE id_usuario = ".$this->id_usuario;

            $q = $sql->query($query);

            mysqli_close($sql);

        }

        public function info_usuario(){

            $tiempo = time() - $this->inicio_sesion;

            $tiempo_actual_hs = intval($tiempo / 3600);

            $tiempo_actual_minutos = intval(($tiempo % 3600) / 60);

            $tiempo_actual_segundos = intval(($tiempo % 3600) % 60);

            $tiempo_total = $this->tiempo_plataforma_gral + $tiempo;

            $tiempo_total_hs = intval($tiempo_total / 3600);

            $tiempo_total_minutos = intval(($tiempo_total % 3600) / 60);

            $tiempo_total_segundos = intval(($tiempo_total % 3600) % 60);

            $fecha_alta = date("d/m/Y", strtotime($this->fecha_alta_usuario));

            echo "
                <table id='tabla_usuario'>
                    <tr>
                        <td>Usuario:</td>
                        <td>".$this->nombre.' '.$this->apellido." (".perfil_usuario($this->perfil_usuario).")</td>
                    </tr>
                    <tr>
                        <td>Tiempo de sesión actual: </td>
                        <td>".$tiempo_actual_hs." hora(s), ".$tiempo_actual_minutos." minuto(s), ".$tiempo_actual_segundos." segundo(s)</td>
                    </tr>
                    <tr>
                        <td>Tiempo total: </td>
                        <td>".$tiempo_total_hs." hora(s), ".$tiempo_total_minutos." minuto(s), ".$tiempo_total_segundos." segundo(s)</td>
                    </tr>
                    <tr>
                        <td>Fecha de alta usuario:</td>
                        <td>".$fecha_alta."</td>
                    </tr>
                    <tr>
                        <td>Llamados vistos:</td>
                        <td>".$this->llamados_vistos_gral."</td>
                    </tr>
                    <tr>
                        <td>Llamados catalogados:</td>
                        <td>".$this->llamados_catalogados_gral."</td>
                    </tr>
                    <tr>
                        <td>Llamados guardados:</td>
                        <td>".$this->llamados_guardados_gral."</td>
                    </tr>
                    <tr>
                        <td>Llamados desestimados:</td>
                        <td>".$this->llamados_desestimados_gral."</td>
                    </tr>
                    <tr>
                        <td>Llamados presupuestados:</td>
                        <td>".$this->llamados_presupuestados_gral."</td>
                    </tr>
                    <tr>
                        <td>Llamados ofertados:</td>
                        <td>".$this->llamados_ofertados_gral."</td>
                    </tr>

                </table>
            ";


        }

    }

    class Busqueda {

        public $tipo_estado = NULL;
        public $tipo_estado_interno = NULL;

        public $tipo_busqueda = NULL;
        public $inciso = NULL;
        public $unidad_ejecutora = NULL;

        public $tipo_contratacion = NULL;
        public $subtipo_contratacion = NULL;

        public $numero_llamado = NULL;
        public $anio_llamado = NULL;

        public $tipo_rango_fechas = NULL;
        public $rango_fecha_inicio = NULL;
        public $rango_fecha_fin = NULL;
        public $fecha_aux;

        public $tipo_busqueda_item = NULL;

        public $item_familia = NULL;
        public $item_subfamilia = NULL;
        public $item_clase = NULL;
        public $item_subclase = NULL;

        public $cod_articulo = NULL;

        public $proveedor_tipo = NULL;
        public $proveedor_doc = NULL;

        public $objeto = NULL;

        public $resultados = Array();

        public function __construct($key){
            
            if($key == 'filtros'){
                $_SESSION['sistema']->vaciar_seleccion();
                $this->get_busqueda();
                $this->armar_query();
            } else if ($key == 'no_cat' || $key == 'guardados' || $key == 'cotizaciones' ){
                $_SESSION['sistema']->vaciar_seleccion();
                // $_SESSION['sistema']->seleccion_llamados['operativa'] = Array();
                // $_SESSION['sistema']->seleccion_llamados['herreria'] = Array();
                // $_SESSION['sistema']->seleccion_llamados['alb_pint'] = Array();
                // $_SESSION['sistema']->seleccion_llamados['seniales'] = Array();
                // $_SESSION['sistema']->seleccion_llamados['otros'] = Array();
                $this->tipo_busqueda = 'lv';
                $this->tipo_rango_fechas = 'rof';
                $this->fecha_aux = date('Y-m-d H:i:s');
                $this->tipo_estado_interno = $key;
                $this->armar_query();
            } else if ($key == 'descartados' ){
                $_SESSION['sistema']->vaciar_seleccion();
                $this->tipo_busqueda = 'lv';
                $this->tipo_rango_fechas = 'rof';
                $this->fecha_aux = date('Y-m-d H:i:s');
                $this->tipo_estado_interno = $key;
                $this->armar_query();
            } else if ($key == 'adjudicados'){
                $_SESSION['sistema']->vaciar_seleccion();
                $this->tipo_busqueda = 'a';
                $this->tipo_rango_fechas = '';
                $this->tipo_estado_interno = $key;
                $this->armar_query();
            } else if ($key == 'obj'){
                $_SESSION['sistema']->vaciar_seleccion();
                $this->tipo_busqueda = 'l';
                $this->tipo_rango_fechas = '';
                $this->objeto = $_SESSION['sistema']->objeto;
                $this->armar_query();
            }
        }

        public function get_busqueda(){

            if(isset($_POST['tipo_estado'])){
                $this->tipo_estado = $_POST['tipo_estado'];
            } else {
                $this->tipo_estado = NULL;
            }

            if(isset($_POST['tipo_estado_interno'])){
                $this->tipo_estado_interno = $_POST['tipo_estado_interno'];
            } else {
                $this->tipo_estado_interno = NULL;
            }

            if(isset($_POST['tipo_pub'])){
                $this->tipo_busqueda = $_POST['tipo_pub']; 
            } else {
                $this->tipo_busqueda = NULL;
            };
            if(isset($_POST['org_contr_in']) && $_POST['org_contr_in'] != 0){
                $this->inciso = $_POST['org_contr_in']; 
            } else {
                $this->inciso = NULL;
            };
            if(isset($_POST['org_contr_ue']) && $_POST['org_contr_ue'] != 0){
                $this->unidad_ejecutora = $_POST['org_contr_ue']; 
            } else {
                $this->unidad_ejecutora = NULL;
            };
        
            if(isset($_POST['tipo_contr']) && $_POST['tipo_contr'] != 0){
                $this->tipo_contratacion = $_POST['tipo_contr']; 
            } else {
                $this->tipo_contratacion = NULL;
            };
            if(isset($_POST['subtipo_contr']) && $_POST['subtipo_contr'] != 0){
                $this->subtipo_contratacion = $_POST['subtipo_contr']; 
            } else {
                $this->subtipo_contratacion = NULL;
            };
        
            if(isset($_POST['num_llamado'])){
                $this->numero_llamado = $_POST['num_llamado']; 
            } else {
                $this->numero_llamado = NULL;
            };
            if(isset($_POST['anio_llamado'])){
                $this->anio_llamado = $_POST['anio_llamado']; 
            } else {
                $this->anio_llamado = NULL;
            };
        
            if(isset($_POST['tipo_fecha'])){
                $this->tipo_rango_fechas = $_POST['tipo_fecha']; 
            } else {
                $this->tipo_rango_fechas = NULL;
            };
        
            if(isset($_POST['fecha_inicio'])){
                $this->rango_fecha_inicio = $_POST['fecha_inicio'];
            } else {
                $this->rango_fecha_inicio = NULL;
            };
        
            if(isset($_POST['fecha_fin'])){
                $this->rango_fecha_fin = $_POST['fecha_fin']; 
            } else {
                $this->rango_fecha_fin = NULL;
            };
        
            if(isset($_POST['catalogo'])){
                $this->tipo_busqueda_item = $_POST['catalogo']; 
            } else {
                $this->tipo_busqueda_item = NULL;
            };
        
            if(isset($_POST['item_familia'])){
                $this->item_familia = $_POST['item_familia']; 
            } else {
                $this->item_familia = NULL;
            };
        
            if(isset($_POST['item_subfamilia'])){
                $this->item_subfamilia = $_POST['item_subfamilia']; 
            } else {
                $this->item_subfamilia = NULL;
            };
        
            if(isset($_POST['item_clase'])){
                $this->item_clase = $_POST['item_clase']; 
            } else {
                $this->item_clase = NULL;
            };
        
            if(isset($_POST['item_subclase'])){
                $this->item_subclase = $_POST['item_subclase']; 
            } else {
                $this->item_subclase = NULL;
            };
        
            if(isset($_POST['cod_articulo'])){
                $this->cod_articulo = $_POST['cod_articulo']; 
            } else {
                $this->cod_articulo = NULL;
            };
        
            if(isset($_POST['tipo_doc_prov'])){
                $this->proveedor_tipo = $_POST['tipo_doc_prov']; 
            } else {
                $this->proveedor_tipo = NULL;
            };
        
            if(isset($_POST['nro_doc_prov'])){
                $this->proveedor_doc = $_POST['nro_doc_prov']; 
            } else {
                $this->proveedor_doc = NULL;
            };

            if(isset($_POST['input_objeto'])){
                $this->objeto = $_POST['input_objeto']; 
            } else {
                $this->objeto = NULL;
            };
        }

        public function armar_query(){

            $_SESSION['sistema']->seleccion_claves = Array();
            
            $select = "SELECT * "; 
            $from = "FROM (gestor_compras_estatales_sandbox.compras as compras INNER JOIN gestion_bd_sandbox.gestion_compras as gestion ON compras.id_compra = gestion.id_compra )"; 
            $where = Array();
            $group = Array();
            $tipo_publicacion = 'fecha_publicacion';
            $fecha_hora_actual = date('Y-m-d H:i:s');
            
            if($this->tipo_busqueda == 'lv'){
                $where[] = "compras.estado_compra = '4' AND compras.fecha_hora_tope_entrega > '".$fecha_hora_actual."'";
            } else if ($this->tipo_busqueda == 'a') {
                $where[] = "( compras.estado_compra = '7' OR compras.estado_compra = '17' OR compras.estado_compra = '27' )";
                $tipo_publicacion = 'fecha_pub_adj';
                if($this->proveedor_tipo != NULL && $this->proveedor_doc != NULL){
                    $from .= " INNER JOIN gestor_compras_estatales_sandbox.items_adjudicacion as items ON compras.id_compra = items.id_compra ";
                    $where[] = 'compras.id_compra IN (SELECT id_compra FROM gestor_compras_estatales_sandbox.items_adjudicacion WHERE tipo_doc_prov = "'.$this->proveedor_tipo.'" AND nro_doc_prov = "'.$this->proveedor_doc.'") ';
                    $group[] = 'compras.id_compra';
                }
            } else if ($this->tipo_busqueda == 'l') {
                if($this->proveedor_tipo != NULL && $this->proveedor_doc != NULL){
                    $from .= "
                        INNER JOIN gestion_bd_sandbox.gestion_compras
                        ON compra.id_compra = gestion.id_compra";
                }
            }

            if($this->tipo_estado_interno != NULL){
                if($this->tipo_estado_interno == 2){
                    $query = "compras.estado_interno = '2' OR compras.estado_interno = '3'";
                } else if ($this->tipo_estado_interno == 'no_cat'){
                    $query = "compras.estado_interno < '2'";
                } else if ($this->tipo_estado_interno == 'guardados'){
                    $query = "compras.estado_interno = '2' OR compras.estado_interno = '3'";
                }  else if ($this->tipo_estado_interno == 'cotizaciones'){
                    $query = "compras.estado_interno = '3'";
                }  else if ($this->tipo_estado_interno == 'descartados'){
                    $query = "compras.estado_interno = '4'";
                }  else if ($this->tipo_estado_interno == 'adjudicados'){
                    $query = "compras.estado_interno = '5'";
                }  else {
                    $query = "compras.estado_interno = '".$this->tipo_estado_interno."'";
                }
                $where[] = "compras.id_compra IN (SELECT id_compra FROM gestion_bd_sandbox.gestion_compras as compras WHERE ".$query." )";
            }

            if($this->inciso != NULL){
                $where[] = "compras.id_inciso = '".$this->inciso."' ";
            }

            if($this->unidad_ejecutora != NULL){
                $where[] = "compras.id_ue = '".$this->unidad_ejecutora."' ";
            } 

            if($this->tipo_contratacion != NULL){
                $where[] = "compras.id_tipocompra = '".$this->tipo_contratacion."' ";
            }

            if($this->subtipo_contratacion != NULL){
                $where[] = "compras.subtipo_compra = '".$this->subtipo_contratacion."' ";
            }

            if($this->numero_llamado != NULL){
                $where[] = "compras.num_compra = '".$this->numero_llamado."' ";
            }

            if($this->anio_llamado != NULL){
                $where[] = "compras.anio_compra = '".$this->anio_llamado."' ";
            }

            $tipo_rango = '';

            if($this->tipo_rango_fechas == 'mod'){
                $tipo_rango = 'compras.fecha_ult_mod_llamado';
            } else if ($this->tipo_rango_fechas == 'rof'){
                $tipo_rango = 'compras.fecha_hora_tope_entrega';
            } else if ($this->tipo_rango_fechas == 'pub'){
                $tipo_rango = 'compras.'.$tipo_publicacion;
            } else {
                $tipo_rango = '';
            }

            if($this->objeto != NULL){
                $where[] = 'objeto LIKE "%'.$this->objeto.'%"';
            }

            if($this->rango_fecha_inicio != NULL){
                $where[] = $tipo_rango." >= '".$this->rango_fecha_inicio." 00:00:00' ";
            }

            if($this->rango_fecha_fin != NULL){
                $where[] = $tipo_rango." <= '".$this->rango_fecha_fin." 23:59:59' ";
            }

            if($this->fecha_aux != NULL){
                $where[] = $tipo_rango." >= '".$this->fecha_aux."' ";
            }

            if($this->tipo_busqueda_item == 'clas'){

                $in = '';

                if($this->item_familia != NULL){
                    $from .= 'INNER JOIN gestion_compras_estatales_sandbox.items_compra as items ON compras.id_compra = items.id_compra ';
                    $in .= 'items.id_articulo IN ( SELECT cod as id_articulo FROM catalogo_arce.art_serv_obra WHERE fami_cod = "'.$this->item_familia.'" ';
                }

                if($this->item_subfamilia != NULL){
                    $in .= 'AND subf_cod = "'.$this->item_subfamilia.'" ';
                }

                if($this->item_clase != NULL){
                    $in .= 'AND clas_cod = "'.$this->item_clase.'" ';
                }

                if($this->item_subclase != NULL){
                    $in .= 'AND subc_cod = "'.$this->item_subclase.'" ';
                }

                if($in != ''){
                    $where[] = $in.') ';
                }

            } else if($this->tipo_busqueda_item == 'art'){

                $from .= 'INNER JOIN gestion_compras_estatales_sandbox.items_compra as items ON compras.id_compra = items.id_compra ';
                $where[] = 'items.id_articulo = "'.$this->cod_articulo.'" ';

            }

            $query = '';

            if(count($where) > 0){

                $query = " WHERE ".$where[0];

                foreach($where as $i){
                    if($i != $where[0]){
                        $query .= "AND ".$i." ";
                    }
                }
            }

            if(count($group) > 0){
                $group = "GROUP BY ".$group[0]." ";
            } else {
                $group = "";
            }

            $order = 'ORDER BY compras.fecha_hora_tope_entrega ASC';

            $query = $select.$from.$query.$group.$order;

            // echo $query;

            $sql = sql_con();

            echo $query;

            $q = $sql->query($query);

            mysqli_close($sql);

            if($q){
                while($r = $q->fetch_object()){
                    $_SESSION['sistema']->seleccion_llamados[$r->id_compra] = new Compras($r);  
                    $_SESSION['sistema']->seleccion_claves[] = $r->id_compra;
                }
            }
        }   
    }

    class Requerimiento {

        public $nro_requerimiento;
        public $fecha_alta;
        public $funcionario_alta;
        public $obligatorio;
        public $tipo;
        public $estado;
        public $fecha_cumplido;
        public $funcionario_cumplido;
        public $descripcion;
        public $archivo_adjunto;

        public function __construct($requerimiento = NULL , $nro_requerimiento = NULL){

            if($requerimiento == NULL){
                $this->funcionario_alta = $_SESSION['user']->id_usuario;
                $this->fecha_alta = date('Y-m-d H:i:s');
            } else {
                $this->nro_requerimiento = $requerimiento->nro_requerimiento;
                $this->fecha_alta = $requerimiento->fecha_alta;
                $this->funcionario_alta = $requerimiento->funcionario_alta;
                $this->obligatorio = $requerimiento->obligatorio;
                $this->tipo = $requerimiento->tipo;
                $this->estado = $requerimiento->estado;
                $this->fecha_cumplido = $requerimiento->fecha_cumplido;
                $this->funcionario_cumplido = $requerimiento->funcionario_cumplido;
                $this->descripcion = $requerimiento->descripcion;
                $this->archivo_adjunto = $requerimiento->archivo_adjunto;
            }

        }

        public function get_post(){

            if(isset($_POST['tipo_requerimiento'])){
                $tipo_requerimiento = $_POST['tipo_requerimiento'];
            };

            if(isset($_POST['usuario_ingreso'])){
                $usuario_ingreso = $_POST['usuario_ingreso'];
            };

            if(isset($_POST['fecha_ingreso'])){
                $fecha_ingreso = $_POST['fecha_ingreso'];
            };

            if(isset($_POST['obligatorio'])){
                $obligatorio = $_POST['obligatorio'];
            };

            if(isset($_POST['realizado'])){
                $realizado = $_POST['realizado'];
            };

            if(isset($_POST['comentario'])){
                $comentario = $_POST['comentario'];
            };

            if(isset($_POST['archivo_adjunto'])){
                $archivo_adjunto = $_POST['archivo_adjunto'];
            };

            if(isset($_POST['fecha_requerimiento'])){
                $fecha_requerimiento = $_POST['fecha_requerimiento'];
            };

            if(isset($_POST['fecha_realizado'])){
                $fecha_realizado = $_POST['fecha_realizado'];
            };

            if(isset($_POST['usuario_realizo'])){
                $usuario_realizo = $_POST['usuario_realizo'];
            };
        }

        public function form_requerimiento(){

            $form = 
                "<form id='form_requerimiento' method='POST' action='javascript:void(0)'>
                    <label for='tipo'>Tipo de requerimiento</label>
                    <select id='tipo_req' name='tipo_req' onchange='form_req( \"tipo\" )'>
                        <option></option>";

            foreach($_SESSION['sistema']->tipos_requerimiento as $tipo){

                $form .= "<option value='".$tipo['id']."'>".$tipo['descripcion']."</option>";

            }
                    
            $form .=
                    "</select>
                    <label></label>
                </form>";

            return $form;
        }

        public function alta_requerimiento( $id_compra , $tipo , $descripcion , $obligatorio ){

            $sql = sql_con(DATABASE_GESTION);

            $q = "INSERT INTO requerimientos_compra ( id_compra , nro_requerimiento , fecha_alta , funcionario_alta , tipo , descripcion , obligatorio ) VALUES ( '".$id_compra."' , ".$this->nro_requerimiento." , '".date('Y-m-d H:i:s')."' , ".$_SESSION['user']->id_usuario." , ".$tipo." , '".$descripcion."' , ".$obligatorio." )";

            $q = $sql->query($q);

            mysqli_close($sql);

        }

        public function mostrar_requerimiento(){

            $return = "
                <div class='requerimiento_compra'>
                    <h2>".$_SESSION['sistema']->tipos_requerimiento[$this->tipo]['descripcion']."</h2>
                    <p>".$this->descripcion."</p>
                </div>";

            return $return;

        }
    
    }

    class Oferta {

        public $oferta;
        public $id_compra;
        public $nro_oferta;
        public $tipo_compra;
        public $nombre_oferta;
        public $funcionario_alta;
        public $fecha_alta;
        public $fecha_ult_mod_oferta;

        public $subcontratistas = Array();

        public $plazo_ejecucion = NULL;
        public $comienzo_obra = NULL;

        public function __construct($id_oferta){
            $this->id_oferta = $id_oferta;
            $this->cargar_oferta();
        }

        public function cargar_oferta(){
            
            $sql = sql_con(DATABASE_GESTION);
            $query = "SELECT * FROM ofertas WHERE id_oferta = '".$this->id_oferta."'";
            $q = $sql->query($query);

            $oferta = false;    

            if($q){

                while($r = $q->fetch_object()){
                    $oferta = $r;
                }

            }

            if(!$oferta){
                $this->crear_oferta();
            }

        }

        public function crear_oferta(){

            $id_compra = $_SESSION['compra_actual']->id_compra;

            $fecha_alta = date('Y-m-d H:i:s');

            $query = "INSERT INTO ofertas ( id_oferta , id_compra , funcionario_alta , fecha_alta , fecha_ult_mod , estado_oferta ) VALUES ( '".$this->id_oferta."' , '".$id_compra."' , '".$_SESSION['user']->id_usuario."' , '".$fecha_alta."' , '".$fecha_alta."' , '0')";

            $sql = sql_con(DATABASE_GESTION);

            $q = $sql->query($query);

            mysqli_close($sql);

        }

        public function agregar_subc($id_subccontratista){
            
            $this->subcontratistas[] = $id_subccontratista;

            $sql = sql_con(DATABASE_GESTION);

            $query = "INSERT INTO subcontratistas_oferta ( id_oferta , id_subcontratista , funcionario_alta , fecha_alta ) VALUES ( '".$this->id_oferta."' , '".$id_subcontratista."' , '".$_SESSION['user']->id_usuario."' , '".date('Y-m-d H:i:s')."' )";

            $q = $sql->query($query);

        }

        public function mostrar_subc(){
            
            if(count($this->subcontratistas) > 0 ){
                echo 
                    "<table id='tabla_subc_asig'>
                        <tr>
                            <th>Nombre subcontratista</th>
                        </tr>";

                    foreach($this->subcontratistas as $subcontratista){
                        echo 
                            "<tr>
                                <td>".$subcontratista->nombre."</td>
                            <tr>";
                    }

                echo "</table>";
            }

        }

    }

    class Items_oferta {

        public $oferta;
        public $nro_item;

    }

    class Costos_oferta {

        public $id_compra;
        public $nro_oferta;
        public $descripcion;
        public $items_costos =  Array();

    }

    class Items_costos {

    }

?>
