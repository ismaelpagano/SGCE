<?php

    include "funcs/funcs.php";
    include "scripts_actualizar_bd.php";

    $url = "https://www.comprasestatales.gub.uy/comprasenlinea/jboss/generarReporte?tipo_publicacion=l&tipo_compra=&rango-fecha=20%2F06%2F2022+-+20%2F06%2F2022&dia_inicial=20&mes_inicial=6&anio_inicial=2022&hora_inicial=0&dia_final=20&mes_final=6&anio_final=2022&hora_final=23";
    
    actualizador_supremo_compras($url);

?>