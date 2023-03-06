<?php

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



?>