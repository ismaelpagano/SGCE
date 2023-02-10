<?php
    function visor_llamado($i){

        $compra = $_SESSION['sistema']->seleccion_llamados[$i];

        $compra->completar_datos();
    
        $botones = botones_compra($i, FALSE);

        $estado_compra = $compra->estado_compra;
        $estado_interno = $compra->estado_interno;
        $estado_interno_txt = estado_interno($compra->estado_interno);
        $fecha_lim_rof = strtotime($compra->fecha_hora_tope_entrega);

       
        $div_botones = '';
        $div_rof = '';

        if ( ($estado_compra == 4 || $estado_compra == 5) && $fecha_lim_rof > time() ) {
            $div_botones = 
                '<div id="botones'.$compra->id_compra.'">
                    '.$botones.'
                </div>';

            $div_rof = 
                '<div class="div_rof">
                    Recepción de ofertas hasta: '.$compra->recepcion_ofertas().'
                </div>';
        } else if ( $compra->fecha_pub_adj != NULL ) {
            $div_rof = 
                '<div class="div_rof">
                    Fecha publicación adj: '.$compra->fecha_pub_adj.'
                </div>';
        }

        $return = 
        '<div class="contCompra '.$estado_interno_txt[0].' '.estado_compra($compra->estado_compra).'" id="'.$compra->id_compra.'">
            <div class="visor">
                <a onclick="detalle_llamado(\''.$i.'\')" href="detalle.php?id_compra='.$compra->id_compra.'"> 
                    <h2>'.$compra->titulo().'</h2>
                    <div><a>'.$compra->mostrar_ue().'</a></div>
                    '.$div_rof.'
                    <div class="descCompra">
                        '.$compra->objeto.'
                    </div>
                </a>
            </div>
            '.$div_botones.'
        </div>';
    
        echo $return;
    }
?>

