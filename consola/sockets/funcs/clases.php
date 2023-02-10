<?php

    set_time_limit(0);

    function find_free_port(){
        $sock = socket_create_listen(0); 

        if($sock){
            socket_getsockname($sock, $addr, $port); 
            socket_close($sock);
    
            return $port;
        } else {

            return 'ERROR';
        }
    }

    function estado_a_cod($estado){

        $return;

        switch($estado){
            case 'NUEVOS':
                $return = 1;
            break;
            case 'GUARDADOS':
                $return = 2;
            break;
            case 'OFERTADOS':
                $return = 3;
            break;
        }

        return $return;
    }

    function cod_a_estado($cod){

        switch($cod){
            case 0:
            case 1:
                $coleccion = 'NUEVOS';
            break;
            case 2:
                $coleccion = 'GUARDADOS';
            break;
            case 3:
                $coleccion = 'OFERTADOS';
            break;
        }

        return $coleccion;

    }

    class Objeto {

        public $atributo;

        public function __construct($mensaje){
        
            $this->atributo = $mensaje;

        }

        public function socket_send(){

            $host = "127.0.0.1";
            $port = 5353;

            set_time_limit(0);
        
            $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        
            $result = socket_connect($socket, $host, $port) or die("Could not connect toserver\n");
            
            socket_write($socket, $this->atributo) or die("Could not send data to server\n");

            $result = socket_read ($socket, 1024) or die("Could not read server response\n");

        }
    }

    class Solicitud {

        public $mensaje;
        public $puerto_origen;
        public $puerto_destino;
        public $tipo;

        public function __construct($tipo, $mensaje, $puerto_origen = NULL, $puerto_destino = NULL){

            $this->tipo = $tipo;
            $this->puerto_origen = $puerto_origen;
            $this->puerto_destino = $puerto_destino;
            $this->mensaje = $mensaje;

        }

        public function tamanio_base($tamanio){

            $this->mensaje = $tamanio;

        }
    }

    class Socket_script {
        
        public $host = '127.0.0.1';
        public $port;
        public $socket;
        public $spawn;

        public function __construct($tipo = 'USER', $puerto){

            if($tipo == 'SERVER'){
                $port = 5353;
            } else if ($puerto == NULL) {
                $port = find_free_port();
            }

            if($port != 'ERROR'){
                $this->port = $port;
                $this->socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
                $this->result = socket_bind($this->socket, $this->host, $this->port) or die("Could not bind to socket\n");
            } else {
                echo 'ERROR';
            }
        }

        public function socket_listening($tamanio = 1024){
            
            $result = socket_listen($this->socket, 0) or die("Could not set up socket listener\n");
            $spawn = socket_accept($this->socket) or die("Could not accept incoming connection\n");
            $entrada = socket_read($spawn, $tamanio) or die("Could not read input\n");
            $entrada = json_decode($entrada);
            
            if($entrada->tipo == 'SOLICITUD_BASE'){
                // print_r($entrada);
                $respuesta_base = new Solicitud('RESPUESTA_BASE', 'OK', $this->port, $entrada->puerto_origen);
                // echo "Respuesta base: \n";
                // print_r($respuesta_base);
                // echo ".\n";
                $this->enviar($respuesta_base);
                return $this->socket_listening(intval($entrada->mensaje));
            } else {
                return $entrada;
            };
        }



        public function enviar($solicitud, $bool = false){
            
            //Acá iría algún paso previo de verificación del mensaje

            $mensaje = json_encode($solicitud);

            $tamanio = strlen($mensaje);

            $destino = $solicitud->puerto_destino;

            $result = false;
            $socket = NULL;

            if($tamanio > 1024 && !$bool){   

                $solicitud_base = new Solicitud('SOLICITUD_BASE', strval($tamanio), $this->port, $destino);
                $solicitud_base = json_encode($solicitud_base);

                $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
                $result = socket_connect($socket, '127.0.0.1', $destino) or die("Could not connect to server\n");

                socket_write($socket, $solicitud_base, $tamanio) or die("Could not send data to server\n");
                socket_close($socket);
                $respuesta_base = $this->socket_listening();

                if($respuesta_base->tipo == 'RESPUESTA_BASE' && $respuesta_base->mensaje == 'OK'){
                    $this->enviar($solicitud, true);
                }

            } else {

                $socket = socket_create(AF_INET, SOCK_STREAM, 0);
                $result = @socket_connect($socket, '127.0.0.1', $destino);         
                
                if($result){
                    socket_write($socket, $mensaje, $tamanio);
                } else {
                    // system('cls');
                    throw new Exception('Error al conectar socket. Intentando reconectar...');
                }

                socket_close($socket);

                return $result;

            }
        }
    }

    class Cliente {

        public $puerto;
        public $socket;
        public $nombre;
        public $id;
        public $estado = 'INACTIVO';
        public $llamados = Array();

        public function __construct($usuario, $contraseña){
            
            $this->socket = new Socket_script('USER', NULL);

            $this->puerto = $this->socket->port;
            
            $registro = Array();
            $registro['usuario'] = $usuario;
            $registro['hash'] = md5($contraseña); 
            $registro = json_encode($registro);
            
            $solicitud = new Solicitud('SOLICITUD_CONEXION', $registro, $this->puerto, 5353);

            $result = 0;

            try {
                $result = $this->socket->enviar($solicitud);
            } catch (Exception $e){
                echo $e->getMessage();
            }
            
            if($result){
                $response = $this->socket->socket_listening();
                $this->estado = 'ACTIVO';
            }

            // if($response->tipo == 'REGISTRO' && $response->mensaje != 'ERROR'){
            //     $usuario = json_decode($response->mensaje);
            //     $this->id = $usuario->id;
            //     $this->nombre = $usuario->nombre;
            // } else {
            //     return 'ERROR';
            // }

            $this->llamados = new Llamados('USER');

            if($this->estado == 'INACTIVO'){
                $this->estado = 'ERROR';
            }

            $this->socket = NULL;

        }

        public function llamados(){

            $colecciones = Array('NUEVOS', 'GUARDADOS', 'OFERTADOS');

            $this->socket = new Socket_script('USER', $this->puerto);

            foreach($colecciones as $coleccion){
                $solicitud = new Solicitud('LLAMADOS', $coleccion, $this->socket->port, 5353);
                $this->socket->enviar($solicitud);
                $response = $this->socket->socket_listening();
                $response = json_decode($response->mensaje);
                $this->llamados->coleccion[$coleccion]['CANTIDAD'] = 0;
                foreach($response as $llamado){
                    $compra = new Compras($llamado);
                    $this->llamados->coleccion[$coleccion]['COLECCION'][$compra->id_compra] = $compra;
                    $this->llamados->coleccion[$coleccion]['CANTIDAD']++;
                }
            }
        }

        public function socket_listening(){

            $result = $this->socket->socket_listening();
            echo json_decode($result);
        
        }

        public function main(){

            while(true){

                $this->socket_listening();
                echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

            }

            socket_close($this->spawn);
            socket_close($this->socket);

        }
    }

    class Usuario {

        public $id;
        public $nombre;
        public $rol;
        public $puerto;

        public function __construct($usuario, $puerto){
            $this->id = $usuario->id_usuario;
            $this->nombre = $usuario->nombre;
            $this->rol = $usuario->perfil_usuario;
            $this->puerto = $puerto;
        }
    }

    class Llamados {
        
        public $coleccion = Array();
        public $actualizados = Array();

        public function __construct($user){

            $this->coleccion['NUEVOS'] = Array();
            $this->coleccion['GUARDADOS'] = Array();
            $this->coleccion['OFERTADOS'] = Array();
            $this->coleccion['NUEVOS']['COLECCION'] = Array();
            $this->coleccion['GUARDADOS']['COLECCION'] = Array();
            $this->coleccion['OFERTADOS']['COLECCION'] = Array();

            if($user == 'ADMIN'){

                $this->actualizar_llamados();

            }
        }

        public function actualizar_llamados(){

            $sql = new mysqli('localhost', 'root', '', '');
            $sql->set_charset('utf8');

            function ingresar($query){

                $coleccion = Array();
                $coleccion['COLECCION'] = Array();

                if($query){
                    
                    while($r = $query->fetch_object()){
                        $compra = new Compras($r);
                        $coleccion['COLECCION'][$r->id_compra] = $compra;
                    }
                    $coleccion['CANTIDAD'] = count($coleccion['COLECCION']);
                }

                return $coleccion;
                
            }

            $fecha = date('Y-m-d H:i');
            
            $q = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.compras as compras INNER JOIN gestion_bd_sandbox.gestion_compras as gestion ON compras.id_compra = gestion.id_compra WHERE gestion.estado_interno < 2 AND ( compras.estado_compra = 4 OR compras.estado_compra = 5) AND compras.fecha_hora_tope_entrega > '".$fecha."' ORDER BY .compras.fecha_hora_tope_entrega ASC" );
            
            $this->coleccion['NUEVOS'] = ingresar($q);

            $fecha = date('Y-m-d H:i');

            $q = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.compras as compras INNER JOIN gestion_bd_sandbox.gestion_compras as gestion ON compras.id_compra = gestion.id_compra WHERE gestion.estado_interno = 2 AND .compras.fecha_hora_tope_entrega > '".$fecha."' ORDER BY .compras.fecha_hora_tope_entrega ASC" );

            $this->coleccion['GUARDADOS'] = ingresar($q);

            $fecha = date('Y-m-d H:i');

            $q = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.compras as compras INNER JOIN gestion_bd_sandbox.gestion_compras as gestion ON compras.id_compra = gestion.id_compra WHERE gestion.estado_interno = 3 ORDER BY .compras.fecha_hora_tope_entrega DESC" );

            $this->coleccion['OFERTADOS'] = ingresar($q);

            mysqli_close($sql);

        }

        public function enviar_llamados($tipo){
            
            $return = json_encode($this->coleccion[$tipo]['COLECCION']);

            return $return;
        }

        public function armar_mensaje($cambio, $id_compra, $valor_anterior, $valor_nuevo, $id_usuario){

            $mensaje = Array();

            if($cambio == 'ESTADO_INTERNO'){
                $tipo = Array();
                $tipo['PRIMARIO'] = 'ACTUALIZACION_COMPRA';
                $tipo['SECUNDARIO'] = $cambio;
                $mensaje['TIPO'] = $tipo;
                $mensaje['ID_COMPRA'] = $id_compra;
                $mensaje['VALOR_ANTERIOR'] = $valor_anterior;
                $mensaje['VALOR_NUEVO'] = $valor_nuevo;
            }

            $mensaje['USUARIO'] = $id_usuario;

            return $mensaje;

        }

        public function actualizar_compra($mensaje, $bool = false){

            $solicitud = NULL;
 
            $mensaje = (array) $mensaje;
            $mensaje['TIPO'] = (array) $mensaje['TIPO'];

            if($mensaje['TIPO']['SECUNDARIO'] == 'ESTADO_INTERNO'){

                $estado_actual = cod_a_estado($mensaje['VALOR_ANTERIOR']);
                $id_compra = $mensaje['ID_COMPRA'];
                
                $compra = NULL;
                $cod_nuevo = estado_a_cod($mensaje['VALOR_NUEVO']);

                $valor_nuevo = $mensaje['VALOR_NUEVO'];

                $compra = $this->coleccion[$estado_actual]['COLECCION'][$id_compra];
                unset($this->coleccion[$estado_actual]['COLECCION'][$id_compra]);
                $this->coleccion[$estado_actual]['CANTIDAD']--;
                $compra->id_compra = $cod_nuevo;
                $this->coleccion[$valor_nuevo]['COLECCION'][$id_compra] = $compra;
                $this->coleccion[$valor_nuevo]['CANTIDAD']++;

            }

            if($bool){

                $solicitud = new Solicitud($mensaje['TIPO']['PRIMARIO'], json_encode($mensaje));

                return $solicitud;
            }

        }

    }

    class Chat {

        public $usuarios = Array();
        public $id_chat;
        public $historico = Array();
        public $ult_actualizacion = NULL;

        public function __construct($usuarios, $id_chat = NULL){
            
            foreach($usuarios as $usuario){
                $this->usuarios[$usuario->id] = $usuario;
            }

            if($id_chat == NULL){
                //SCRIPT DE BUSQUEDA DE NOMBRE LIBRE PARA IDENTIFICADOR DE CHAT 
                //BÁSICAMENTE CREAR UN ID BASADO EN UN CRITERIO ESTANDAR QUE NO SE ENCUENTRE EN LA BASE DE DATOS
            } else {

                $sql = sql_con('gestion_usuarios');

                $sql->query('');

            }

            //ENTRE DOS USUARIOS PUEDE EXISTIR UNA SOLA INSTANCIA DE CONVERSACIÓN 
            //POR LO QUE EL INDICADOR DE LOS CHATS PUEDE INVOLUCRAR LOS ID DE LOS PARTICIPANTES CON UN CRITERIO BIEN SIMPLE
            //EL USUARIO QUE TENGA ID MENOR VA PRIMERO SEGUIDO DEL USUARIO QUE TENGA ID MAYOR 
            //EJEMPLO ID_CHAT = 502716
        }

        public function entrada_mensaje($mensaje){
            $this->historico[] = $mensaje;
            foreach($this->usuarios as $usuario){

            }
        }

        // VER DE IMPLEMENTAR CHATS POR COMPRA

    }

    class Mensaje {

        public $usuario;
        public $fecha_enviado;
        public $texto;

        public function __construct($usuario, $fecha_enviado, $texto){
            $this->usuario = $usuario;
            $this->fecha_enviado = $fecha_enviado;
            $this->texto = $texto;
        }

    }

    class Servidor {

        public $host;
        public $port;
        public $socket;
        public $spawn;
        
        public $usuarios = Array();

        public $chats = Array();

        public $llamados;

        public function __construct(){
            $this->socket = new Socket_script('SERVER', 5353);
            $this->llamados = new Llamados('ADMIN');
        }

        public function socket_listening(){

            $result = $this->socket->socket_listening();
            $this->leer_solicitud($result);
        
        }

        public function leer_solicitud($solicitud){

            switch($solicitud->tipo){
                case 'LLAMADOS': $this->llamados($solicitud);
                break;
                case 'SOLICITUD_CONEXION': $this->registrar_usuario($solicitud);
                break;
                case 'CHAT': ;
                break;
                case 'ACTUALIZACION_COMPRA':
                    $mensaje = json_decode($solicitud->mensaje);
                    $solicitud = $this->llamados->actualizar_compra($mensaje, true);
                    $this->propagar_actualizacion($solicitud, $mensaje->USUARIO);
                break;
            }
        }

        public function llamados($solicitud){
            $destino = $solicitud->puerto_origen;
            $tipo = $solicitud->mensaje;
            $envio = $this->llamados->enviar_llamados($tipo);
            $envio = new Solicitud('RESPUESTA_LLAMADOS', $envio, $this->socket->port, $destino);
            $this->socket->enviar($envio);
        }

        public function registrar_usuario($solicitud){
            
            $registro = json_decode($solicitud->mensaje);
            $destino = $solicitud->puerto_origen;

            $sql = new mysqli('localhost', 'root', '', '');
            $sql->set_charset('utf8');

            $q = $sql->query("SELECT id_usuario, nombre, apellido, fecha_ingreso_plataforma, perfil_usuario, telefono, mail, tiempo_plataforma FROM gestion_usuarios.usuarios WHERE usuario = '".$registro->usuario."' AND hash_password ='".$registro->hash."'");

            if($q){

                $usuario = $q->fetch_object();
                $id = $usuario->id_usuario;
                $this->usuarios[$id] = new Usuario($usuario, $destino);
                $mensaje = json_encode($this->usuarios[$id]);       
            
            } else {

                $mensaje = 'ERROR';

            }
            
            $respuesta = new Solicitud('REGISTRO', $mensaje, 5353, $destino);

            $this->socket->enviar($respuesta);
        }
    
        public function main(){

            while(true){

                if(count($this->usuarios)>0){
                    echo "USUARIOS ONLINE:\n";
                    foreach($this->usuarios as $clave => $valor){
                        echo '["'.strval($clave).'"] '.$valor->nombre."\n";
                    };
                }

                echo "\n\n";
                echo "Llamados nuevos: ".$this->llamados->coleccion['NUEVOS']['CANTIDAD']."\n";
                echo "Llamados guardados: ".$this->llamados->coleccion['GUARDADOS']['CANTIDAD']."\n";
                echo "Llamados ofertados: ".$this->llamados->coleccion['OFERTADOS']['CANTIDAD']."\n";

                $this->socket_listening();
                echo chr(27).chr(91).'H'.chr(27).chr(91).'J';

            }

            socket_close($this->spawn);
            socket_close($this->socket);

        }

        public function propagar_actualizacion($solicitud, $id_usuario){
    
            foreach($this->usuarios as $clave => $usuario){

                if($id_usuario != $clave){
                    $solicitud->puerto_origen = 5353;
                    $solicitud->puerto_destino = $usuario->puerto;
                    $this->socket->enviar($solicitud);
                }

            }

        }

    }

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
            $this->num_compra = $compra->num_compra;
            $this->anio_compra = $compra->anio_compra;
            $this->nro_ampliacion = $compra->nro_ampliacion;
            $this->id_inciso = $compra->id_inciso;
            $this->id_ue = $compra->id_ue;
            $this->id_ucc = $compra->id_ucc;
            $this->estado_compra = $compra->estado_compra;
            $this->fecha_publicacion = $compra->fecha_publicacion;
            $this->fecha_ult_mod_llamado = $compra->fecha_ult_mod_llamado;  
            $this->id_tipocompra = $compra->id_tipocompra;
            $this->subtipo_compra = $compra->subtipo_compra;
            $this->objeto = $compra->objeto;
            $this->fecha_hora_apertura = $compra->fecha_hora_apertura;
            $this->lugar_apertura = $compra->lugar_apertura;
            $this->fecha_sol_prorr = $compra->fecha_sol_prorr;
            $this->fecha_sol_aclar = $compra->fecha_sol_aclar;
            $this->fecha_hora_puja = $compra->fecha_hora_puja;
            $this->lugar_entrega = $compra->lugar_entrega;
            $this->fecha_hora_tope_entrega = $compra->fecha_hora_tope_entrega;
            $this->nombre_pliego = $compra->nombre_pliego;
            $this->precio_pliego = $compra->precio_pliego;
            $this->id_moneda_pliego = $compra->id_moneda_pliego;
            $this->lugar_compra_pliego = $compra->lugar_compra_pliego;
            $this->nombre_contacto = $compra->nombre_contacto;
            $this->fax_contacto = $compra->fax_contacto;
            $this->email_contacto = $compra->email_contacto;
            $this->fecha_pub_adj = $compra->fecha_pub_adj;
            $this->fecha_compra = $compra->fecha_compra;
            $this->fecha_vigencia_adj = $compra->fecha_vigencia_adj;
            $this->fondos_rotatorios = $compra->fondos_rotatorios;
            $this->apel = $compra->apel;
            $this->arch_adj = $compra->arch_adj;
            $this->monto_adj = $compra->monto_adj;
            $this->id_moneda_monto_adj = $compra->id_moneda_monto_adj;
            $this->id_tipo_resol = $compra->id_tipo_resol;
            $this->num_resol = $compra->num_resol;
            $this->es_reiteracion = $compra->es_reiteracion;
            $this->arch_reiteracion = $compra->arch_reiteracion;
            $this->estado_interno = $compra->estado_interno;
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

            $sql = sql_con(DATABASE_CODIGUERAS);

            $q = $sql->query("SELECT descripcion FROM tipos_compra WHERE id_tipo_compra = '".$this->id_tipocompra."'");

            mysqli_close($sql);
        
            if($q){
                while($r = $q->fetch_object()){
                    $tipo_compra = $r;
                }
            }

            $titulo = $tipo_compra->descripcion.' '.$this->num_compra.'/'.$this->anio_compra;

            return $titulo;
        }

        public function mostrar_ue(){

            $sql = sql_con(DATABASE_CODIGUERAS);
            
            $retorno = '';
            
            if($this->id_inciso != NULL){

                $query = 'SELECT * FROM incisos INNER JOIN unidades_ejecutoras ON incisos.id_inciso = unidades_ejecutoras.id_inciso WHERE id_ue = '.$this->id_ue.' AND incisos.id_inciso = '.$this->id_inciso;

                $q = $sql->query($query);
                
                if($q->num_rows > 0){
                    $r = $q->fetch_object();
                }
                
                $retorno = $r->nom_inciso." - ".$r->nom_ue;
            } else {
                $q = $sql->query('SELECT * FROM unidades_compra_centralizadas WHERE id_ucc = '.$this->id_ucc);

                if($q->num_rows > 0){
                    $r = $q->fetch_object();
                }
                
                $retorno = $r->nom_ucc;;
            }
    
            mysqli_close($sql);

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

            echo $this->objeto.'<br><br><br>';

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
                            <td><a href='".URL_ACLARACION.$aclaracion->nom_archivo."'>Archivo</td>
                        </tr>";
                }

                echo "</table><br><br><br>";
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

            if( $estado == 19 ){

                $cabezal .= "<div id='llamado_anulado'><p>Llamado anulado</p></div>";

            } else if( $estado == 4 || $estado == 5 ) {
                
                if( $this->apel == 'S' ){
                    $cabezal .= "<div id='apertura_electronica'><p>Apertura electrónica</p></div>";
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
                        <td>".estado_oferta($oferta->estado)."</td>
                    </tr>";
            }

            $tabla .= "</table>";

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

        public function set_requerimiento(){

            $requerimiento = new Requerimiento();
        
            $this->requerimientos[$requerimiento->tipo_requerimiento] = $requerimiento;

            $query = 'INSERT INTO gestion_bd_sandbox.requerimientos_compra ( id_compra';
            $values = ') VALUES ( "'.$this->id_compra.'" , ';

            $attributes = $requerimiento->attributes();

            foreach($attributes as $a => $b){

                $query .= ' , '.$a;
                $values .= ' , "'.$b.'"';

            }

            $query .= $values.' )';

        }

        public function get_requerimientos(){
            
            $sql = sql_con();

            $q = $sql->query('SELECT * FROM gestion_bd_sandbox.requerimientos_compra WHERE id_compra = "'.$this->id_compra.'"');

            if($q){

                while($r = $q->fetch_object()){
                    $this->requerimientos[] = $r;
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

        public function botones_estado($i){

            $estructura = '';
            
            if($this->estado_compra==4){
    
                if( $this->estado_interno != 4){
                    $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$this->id_compra.'", '.$i.' , 4)\'>Desestimar</button></br>';
                }
                
                if($this->estado_interno != 2){
                    $estructura = $estructura.'<button type="button" onclick=\'actualizarEstado("'.$this->id_compra.'", '.$i.' , 2)\'>Guardar</button></br>';
                }
    	
            }	
            
            return $estructura;

        }

        public function visor_llamado($i){

            $this->completar_datos();
    
            $botones = $this->botones_estado($i);
    
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
                    <a  href="detalle.php?array_key='.$i.'">
                        <h2>'.$this->titulo().'</h2>
                        <div><a>'.$this->mostrar_ue().'</a></div>
                        '.$div_rof.'
                        <div class="descCompra">
                            '.$this->objeto.'
                        </div>
                    </a>
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

?>