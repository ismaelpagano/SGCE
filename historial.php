<?php 
    
    include 'funcs/funcs.php';

    $sql = sql_con();

    $query_detalles_compra = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.compras as compras INNER JOIN gestor_compras_estatales_sandbox.oferentes as oferentes ON compras.id_compra = oferentes.id_compra WHERE oferentes.nro_doc_prov = '215036060012' ORDER BY fecha_pub_adj DESC");

    $query_oferentes_compra = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.oferentes as oferentes WHERE oferentes.id_compra IN (SELECT id_compra FROM gestor_compras_estatales_sandbox.oferentes as oferentes where nro_doc_prov = '215036060012')");

    $query_items_compra = $sql->query("SELECT * FROM gestor_compras_estatales_sandbox.items_adjudicacion as item WHERE item.id_compra IN (SELECT id_compra FROM gestor_compras_estatales_sandbox.oferentes as oferentes WHERE oferentes.nro_doc_prov = '215036060012') ORDER BY nro_item");  
    
    mysqli_close($sql);

    $compras = Array();
    $oferentes = Array();
    $items_adjudicados = Array();

    if($query_detalles_compra){
        while($r = $query_detalles_compra->fetch_object()){
            $compras[$r->id_compra] = $r;
        }
    }

    if($query_oferentes_compra){
        while($r = $query_oferentes_compra->fetch_object()){
            $oferentes[$r->id_compra][] = $r;
        }
    }

    if($query_items_compra){
        while($r = $query_items_compra->fetch_object()){
            $items_adjudicados[$r->id_compra][] = $r;
        }
    }

    function ordenar_adjudicaciones($compras, $oferentes, $items_adjudicados){

        foreach($compras as $compra){

            $unidad_ejecutora = '';
            $oferentes_compra = $oferentes[$compra->id_compra];

            if($compra->id_ue != NULL){
                $unidad_ejecutora = $_SESSION['incisos'][$compra->id_inciso]->nom_inciso.' - '.$_SESSION['ues'][$compra->id_inciso][$compra->id_ue]->nom_ue.'<br>';
            } else {
                $unidad_ejecutora = $_SESSION['uccs'][$compra->id_ucc]->nom_ucc.'<br>';
            }

            echo 
                "<div class='cont_adj' id='compra_id_".$compra->id_compra."'>"
                .$_SESSION['tipos_compra'][$compra->id_tipocompra]->descripcion.' '
                .$compra->num_compra.'/'.$compra->anio_compra.'<br>'
                .$unidad_ejecutora.$compra->objeto.'<br>';

            echo "<table class='lista_oferentes'>";
            echo "
                <tr>
                    <th class='nom_rut_ofe'>Oferentes</th>
                </tr>";


            foreach($oferentes_compra as $oferente){
                echo "<tr>";
                echo "<td class='nom_rut_ofe'>".$oferente->nombre_comercial." - ".$oferente->nro_doc_prov."</td>";
                echo "</tr>";
            }

            echo "</table>";

            if($compra->id_tipo_resol != 3 && $compra->id_tipo_resol != 4 && $compra->id_tipo_resol != 7 ){

                echo "<table class='items_adj'>";

                echo 
                    "<tr>
                        <th class='nro_item'>Ítem</th>
                        <th class='desc_art'>Descripción</th>
                        <th class='adj_art'>Adjudicatario</th>
                        <th class='cant_item_adj'>Cant.</th>
                        <th class='monto_item_un'>Precio un. s/IVA</th>
                        <th class='monto_item_tot'>Precio total. c/IVA</th>
                    </tr>";
                
                foreach($items_adjudicados[$compra->id_compra] as $item){
                    echo "<tr class='item'>";
                    echo "<td class='nro_item td_nro_item'>".$item->nro_item."</td>";
                    echo "<td class='desc_art td_desc_art'>".$item->desc_articulo."</td>";
                    echo "<td class='adj_art td_adj_art'>".$item->nombre_comercial."</td>";
                    echo "<td class='cant_item_adj td_cant_item_adj'>".$item->cant_adj."</td>";
                    echo "<td class='monto_item_un td_monto_item_un'>".$_SESSION['monedas'][$item->id_moneda]->sigla_moneda.$item->precio_unit."</td>";
                    echo "<td class='monto_item_tot td_monto_item_tot'>".$_SESSION['monedas'][$item->id_moneda]->sigla_moneda.$item->precio_tot_imp."</td>";
                    echo "</tr>";
                }
    
                echo "</table>";

            }

            echo "</div>";
            
            echo '<br><br>';

        }
    };

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/historial.css" />
		<title>Historial de llamados</title>
		<script src="js/funcs.js"></script>
	</head>
	<body>
		<div id="pag">
			<div id="escena">
                    <?php
                        ordenar_adjudicaciones($compras, $oferentes, $items_adjudicados);
                    ?>
			</div>
		</div>
	</body>
</html>