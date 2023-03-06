<?php

    include 'funcs/funcs.php';

    $id_compra = '';

    if(isset($_GET['id_compra'])){
        $id_compra = $_GET['id_compra'];
    } else {
        $id_compra = NULL;
    }

    if($id_compra != NULL){

        if(isset($_SESSION['sistema']->seleccion_llamados[$id_compra])){
            $compra = $_SESSION['sistema']->seleccion_llamados[$id_compra]; 
        } else {
            $compra = $_SESSION['sistema']->obtener_compra($id_compra);
        }

        $compra->completar_datos();
        $compra->set_items();
    } else {
        echo 'No se encuentra objeto con ese identificador.';
    }

    $compra->get_requerimientos();

    $div_requerimientos = '';

    foreach($compra->requerimientos as $requerimiento){

        print_r($requerimiento);

        $div_requerimientos .= $requerimiento->mostrar_requerimiento();

    }

    $nuevo_requerimiento = new Requerimiento();

    $sql = sql_con('gestor_compras_estatales_'.$compra->anio_compra);

    $_SESSION['compra_actual'] = $compra;

    $id_contexto = $compra->id_compra;

    mysqli_close($sql);

    $div_estado = "";
    $estado_compra = $compra->estado_compra;
    $fecha_tope = $compra->fecha_hora_tope_entrega;

    $estado_interno = estado_interno($compra->estado_interno);

    $compra->llamado_visto();

    // $compra->agregar_contacto_bd()

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/header.css" />
        <link rel="stylesheet" type="text/css" href="css/detalle.css" />
        <link rel="stylesheet" type="text/css" href="css/comentarios.css" />
		<title><?php echo $compra->titulo(); ?></title>
		<script src="js/funcs.js"></script>
	</head>
	<body onload="boton_estado_header('<?php echo estado_interno($compra->estado_interno)[0]; ?>')">
        <header id="header_detalle">
            <div id="header_title">
                <?php 
                    echo $compra->titulo().'<br>'; 
                    echo $compra->mostrar_ue().'<br>';
                ?>
            </div>
            <div id="botones_header">
            </div> 
        </header>
        <div id="pag">
            <div id="contenido_pag">
                <div id="barra_lateral_detalle">
                    <?php $compra->barra_lateral(); ?>
                </div>
                <div id="contenedor_detalle">
                    <!-- <div id="nav_pestañas">
                        <ul id="ul_nav_pestañas">
                            <li id="li_nav_obj" class="li_nav_pestaña">Objeto</li>
                            <li id="li_nav_his" class="li_nav_pestaña">Historial</li>
                            <li id="li_nav_ges" class="li_nav_pestaña">Gestión</li>
                        </ul>
                    </div> -->
                    <p id="objeto" onclick="copiar_portapapeles()"><?php echo $compra->detalle_llamado(); ?></p>
                    <?php 
                        echo $compra->mostrar_items();
                        echo $compra->mostrar_ofertas_llamado();
                    ?>
                    <div id="contenedor_adm"></div>
                    <div id="cont_sector_signado">
                        <div id="cont_tabla_subc">
                            <?php // $_SESSION['user']->oferta_actual->mostrar_subc(); ?>
                        </div>
                        <!-- <div id="asignar_sector">
                            <form id="asignar_sector" method="POST">
                                <label for="lista_sectores">Asignar sector</label>
                                <select name="lista_sectores">
                                    <option value=''></option>";
                                    <?php
                                        // foreach($_SESSION['sistema']->sectores as $sector){
                                        //     echo "<option value='".$sector['id']."'>".$sector['nombre']."</option>";
                                        // }				
                                    ?>
                                </select>
                                <input type="submit" value="Asignar" onclick='anadir_sector()'>
                            </form>
                        </div> -->
                    </div>
                    <div id="cont_requerimientos">
                        <?php  //echo $div_requerimientos; ?>
                        <?php  //echo $nuevo_requerimiento->form_requerimiento(); ?>
                    </div>
                    <div id="contenedor_comentarios"><?php include 'comentarios.php'; ?></div>
                </div>
                <div id="objeto_llamado"></div>
                <div id="historial_llamado"></div>
                <div id="gestion_llamado"></div>
            </div>
        </div>
	</body>
</html>
