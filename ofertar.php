<?php

    include 'funcs/funcs.php';

    $compra = $_SESSION['compra_actual'];

    if(isset($_POST['id_oferta'])){
        $id_oferta = $_POST['id_oferta'];
    } else {
        $id_oferta = '';
    }

    $_SESSION['user']->oferta_actual = new Oferta($id_oferta);

    if(isset($_SESSION['ruta'])){
        if($_SESSION['ruta'] == 'nueva_oferta'){
            $id_oferta = $compra->crear_oferta_llamado();
            unset($_SESSION['ruta']);
        }
    } else {
        $cant = count($compra->ofertas_usuario);
        if($cant > 0){
            $id_oferta = $compra->ofertas_usuario[$cant - 1]->id_oferta;
        }
    }

    $rubros = get_rubros_oferta($id_oferta);
    
    $_SESSION['oferta_actual'] = $id_oferta;

    $titulo = ltrim(substr(strchr($id_oferta, '_'), 1), '0');

    $monedas = Array();

    $sql = sql_con();

    $q = $sql->query("SELECT * FROM codigueras_arce.monedas WHERE id_moneda = id_moneda_arbitraje");

    mysqli_close($sql);

    if($q){
        while($r = $q->fetch_object()){
            $monedas[$r->id_moneda] = $r;
        };
    }

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/header.css" />
        <link rel="stylesheet" type="text/css" href="css/detalle.css" />
        <link rel="stylesheet" type="text/css" href="css/ofertar.css" />
		<title><?php echo 'Oferta '.$titulo.' - '.$compra->titulo(); ?></title>
		<script src="js/funcs.js"></script>
	</head>
	<body>
        <header id="header_detalle">
            <div id="header_title">
                <?php 
                    echo $compra->titulo().'<br>'; 
                    echo $compra->mostrar_ue().'<br>';
                ?>
            </div>
            <div id="botones_header">
                <?php
                ?>
            </div> 
        </header>
        <div id="pag">
            <div id="barra_lateral_detalle">
            </div>
            <div id="contenedor_detalle">
                <?php echo $compra->detalle_llamado(); ?>
                <?php 
                    // echo $compra->mostrar_items_oferta();
                ?>
                <div id="div_rubros">
                    <?php
                    
                        echo show_rubros_oferta($rubros);

                    ?>
                </div>
                <div id="agregar_rubro">
                    <table>
                        <form method="POST" action="javascript:void(0);">
                            <tr>
                                <td>Descripción</td>
                                <td id="td_desc"><input type='text' id="textarea_desc" name='nuevo_rubro_desc'/></td>
                            </tr>
                            <tr>
                                <td>Cantidad</td>  
                                <td><input type='text' name='nuevo_rubro_cant'/></td>             
                            </tr>
                            <tr>
                                <td>Magnitud</td>   
                                <td>
                                    <select name='nuevo_rubro_magn'>
                                        <?php 
                                            foreach($_SESSION['magnitudes'] as $magnitud){
                                                echo "<option>".$magnitud."</option>";
                                            }
                                        ?>
                                    </select> 
                                </td>            
                            </tr>
                            <tr>
                                <td>Costo unitario s/IVA</td>
                                <td>
                                    <select id="select_moneda" name='nuevo_rubro_moneda'>
                                        <?php 
                                            foreach($monedas as $moneda){
                                                if($moneda->id_moneda == '0'){
                                                    echo "<option selected>".$moneda->nom_moneda."</option>";
                                                } else {
                                                    echo "<option>".$moneda->nom_moneda."</option>";
                                                }
                                            }
                                        ?>
                                    </select> 
                                </td>
                                <td><input type='textarea' name='nuevo_rubro_costo'/></td>     
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <input type='submit' onclick="agregar_rubro_oferta()"/>
                                </td>
                            </tr>
                        <form>
                    </table>
                </div>
                <div id="cont_subc">
                    <div id="cont_tabla_subc">
                        <?php $_SESSION['user']->oferta_actual->mostrar_subc(); ?>
                    </div>
                    <div id="asignar_subc">
                        <form id="anadir_subc" method="POST">
                            <label for="lista_subc">Añadir subcontratista</label>
                            <select name="lista_subc">
                                <option value=''></option>";
                                <?php
                                    foreach($_SESSION['sistema']->subcontratistas as $subcontratista){
                                        echo "<option value='".$subcontratista->id_subcontratista."'>".$subcontratista->nombre."</option>";
                                    }				
                                ?>
                            </select>
                            <input type="submit" value="Añadir" onclick='anadir_subc()'>
                        </form>
                    </div>
                </div>
                <div id="contenedor_comentarios">
                    <?php include '' ?>
                </div>
            </div>
        </div>
	</body>
</html>
