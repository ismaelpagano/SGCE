<?php 

    include 'funcs/funcs.php';

    $ofertas = $_SESSION['compra_actual']->ofertas;

    if(count($ofertas) > 0){

        $return = 
            '
                <p id="txt_verificacion">Atención: ya tienes una oferta de este llamado. <span>¿Quieres retomarla?</span></p>
                <div id="div_btns_verificacion">
                    <div class="div_btn_verificacion">
                        <button id="btn_verificacion_af" class="btn_verificacion" onclick="crear_oferta(true)">
                            Sí
                        </button>
                    </div>
                    <div class="div_btn_verificacion">
                        <button id="btn_verificacion_neg" class="btn_verificacion" onclick="crear_oferta(false)">
                            No, crear otra oferta
                        </button>
                    </div>
                </div>
            ';

        echo $return;

    }

?>