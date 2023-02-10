$items = count($compra->items->children());

if($items>0){

    $items = $compra->items->children();

    foreach($items as $item){

        $attributes_item = $item->attributes();
        $nro_item = $attributes['nro_item'];

        $sql = sql_con();

        $query = 'SELECT * FROM items_compra WHERE id_compra = \''.$id_compra.'\' AND nro_item = '.$nro_item;
        
        echo $query;
        
        $q = $sql->query($query);

        $query = '';

        $i = 0;

        if($q->num_rows > 0)
        {
            $query = "UPDATE items_compra SET ";

            foreach($attributes as $a => $b){
                if(is_int($b) || is_float($b)){
                    if($i < $count - 1){
                        $query = $query.$a.' = '.$b.', ';
                    } else {
                        $query = $query.$a.' = '.$b;
                    }
                } else {
    
                    if($a = 'fecha_hora_puja'){
                        $b = formatearFechaHora($b);
                    }
    
                    if($i < $count - 1){
                        $query = $query.$a.' = \''.$b.'\', ';
                    } else {
                        $query = $query.$a.' = \''.$b.'\'';
                    }
                }   
                $i++;
            }

            $query = $query.' WHERE items_compra = \''.$id_compra.'\' AND nro_item = '.$nro_item;

        } else {

            $query = "INSERT INTO items_compra ( id_compra, ";
            $values = " ) VALUES ('".$id_compra."', ";

            foreach($attributes as $a => $b){

                if(in_array($a, $numerico) || in_array($a, $flotante)){
                    
                    if($i < $count - 1){
                        $query = $query.$a.', ';
                        $values = $values.$b.', ';
                    } else {
                        $query = $query.$a;
                        $values = $values.$b;
                    }

                } else {
                    
                    if(in_array($a, $fecha) || in_array($a, $fechaHora)){
                    $b = formatearFechaHora($b);
                    }

                    if($i < $count - 1){
                        $query = $query.$a.' = \''.$b.'\', ';
                    } else {
                        $query = $query.$a.' = \''.$b.'\'';
                    }
    
                $i++;
            }

            $values = $values." )";
            $query = $query.$values;
        }

        $q = $sql->query($query);

        mysqli_close($sql);

        $atributos_item = $item->atributos_item->children();

        foreach($atributos_item as $atributo_item){

            $attributes_atributo_item = $atributo_item->attributes();

            //atributos requeridos
            
            $id_prop_atributo = $attributes_atributo_item->id_prop_atributo;
            $desc_prop_atributo = $attributes_atributo_item->desc_prop_atributo;
            $id_unidad_med_prop_atributo = $attributes_atributo_item->id_unidad_med_prop_atributo;
            $desc_unidad_med_prop_atributo = $attributes_atributo_item->desc_unidad_med_prop_atributo;
            $requerido = $attributes_atributo_item->requerido;

            //atributos opcionales

            $cod_condicion = $attributes_atributo_item->cod_condicion;
            $valor_numerico = $attributes_atributo_item->valor_numerico;
            $valor_texto = $attributes_atributo_item->valor_texto;
            $valor_fecha = $attributes_atributo_item->valor_fecha;
            $valor_booleano = $attributes_atributo_item->valor_booleano;

            $valores_atributo_item = $atributo_item->valores_atributo_item->children();

            foreach($valores_atributo_item as $valor_atributo_item){
                
                $attributes_valor = $valor_atributo_item->attributes();
                
                $valor_numerico = $attributes_valor->valor_numerico;
                $valor_texto = $attributes_valor->valor_texto;
                $valor_fecha = $attributes_valor->valor_fecha;
            }


        }

    }

    }
}

// $oferentes = $compra->oferentes->children();

// foreach($oferentes as $oferente){

//     $attributes_oferente = $oferente->attributes();

//     //atributos relacionales

//     $id_compra;

//     //atributos requeridos
    
//     $tipo_doc_prov = $oferente->tipo_doc_prov;
//     $nro_doc_prov = $oferente->nro_doc_prov;
//     $nombre_comercial = $oferente->nombre_comercial;
// }

// $adjudicaciones = $compra->adjudicaciones->children();

// foreach($adjudicaciones as $adjudicacion){

//     $attributes_adj = $adjudicacion->attributes();

//     //atributos relacionales

//     $id_compra;

//     //atributos requeridos

//     $nro_item = $attributes_adj->nro_item;
//     $tipo_doc_prov = $attributes_adj->tipo_doc_prov;
//     $nombre_comercial = $attributes_adj->nombre_comercial;
//     $cant_adj = $attributes_adj->cant_adj;
//     $id_unidad = $attributes_adj->id_unidad;
//     $unidad = $attributes_adj->unidad;
//     $precio_unit = $attributes_adj->precio_unit;
//     $precio_tot_imp = $attributes_adj->precio_tot_imp;
//     $id_moneda = $attributes_adj->id_moneda;
//     $id_articulo = $attributes_adj->id_articulo;
//     $desc_articulo = $attributes_adj->desc_articulo;
//     $id_variante = $attributes_adj->id_variante;
//     $variante = $attributes_adj->id_variante;
//     $unidad_medida_variante = $attributes_adj->unidad_medida_variante;
//     $medida_variante = $attributes_adj->medida_variante;
//     $presentacion = $attributes_adj->presentacion;
//     $medida_presentacion = $attributes_adj->medida_presentacion;
//     $unidad_medida_presentacion = $attributes_adj->unidad_medida_presentacion;

//     //atributos opcionales

//     $id_color = $attributes_adj->id_color;
//     $desc_color = $attributes_adj->desc_color;
//     $id_detalle_variante = $attributes_adj->id_detalle_variante;
//     $desc_detalle_variante = $attributes_adj->desc_detalle_variante;
//     $id_marca = $attributes_adj->id_marca;
//     $desc_marca = $attributes_adj->desc_marca;
//     $variacion = $attributes_adj->variacion;

//     $atributos_adjudicacion = $adjudicacion->atributos_adjudicacion->children();

//     foreach($atributos_adjudicacion as $atributo_adjudicacion){

//         $attributes_atributo_adjudicacion = $atributo_adjudicacion->attributes();

//         //atributos relacionales

//         $id_compra;
//         $nro_item;

//         //atributos requeridos

//         $id_prop_atributo = $attributes_atributo_adjudicacion->id_prop_atributo;
//         $desc_prop_atributo = $attributes_atributo_adjudicacion->desc_prop_atributo;
//         $id_unidad_med_prop_atributo = $attributes_atributo_adjudicacion->id_unidad_med_prop_atributo;
//         $desc_unidad_med_prop_atributo = $attributes_atributo_adjudicacion->desc_unidad_med_prop_atributo;

//         //atributos opcionales

//         $valor_numerico = $attributes_atributo_adjudicacion->valor_numerico;
//         $valor_texto = $attributes_atributo_adjudicacion->valor_texto;
//         $valor_fecha = $attributes_atributo_adjudicacion->valor_fecha;
//         $valor_booleano = $attributes_atributo_adjudicacion->valor_booleano;

//     }
// }

// $aclaraciones_adj = $compra->aclaraciones_adj->children();

// foreach($aclaraciones_adj as $aclaracion_adj){

//     $attributes_aclaracion_adj = $aclaracion_adj->attributes();
    
//     //atributos relacionales

//     $id_compra;

//     //atributos requeridos

//     $texto = $attributes_aclaracion_adj->texto;
//     $fecha = $attributes_aclaracion_adj->fecha;

//     //atributos opcionales

//     $nom_archivo = $attributes_aclaracion_adj->nom_archivo;
// }

// $aclaraciones_lla = $compra->aclaraciones_lla->children();

// foreach($aclaraciones_lla as $aclaracion_lla){

//     $attributes_aclaracion_lla = $aclaracion_lla->attributes();

//     //atributos relacionales

//     $id_compra;

//     //atributos requeridos

//     $texto = $attributes_aclaracion_lla->texto;
//     $fecha = $attributes_aclaracion_lla->fecha;

//     //atributos opcionales

//     $nom_archivo = $attributes_aclaracion_lla->nom_archivo;
// }

// $historial_mod_llamado = $compra->historial_mod_llamado->children();

// foreach($historial_mod_llamado as $mod_llamado){

//     $attributes_mod_llamado = $mod_llamado->attributes();

//     //atributos relacionales

//     $id_compra;
    
//     //atributos requeridos

//     $fecha = $attributes_mod_llamado->fecha;
//     $campo = $attributes_mod_llamado->campo;
//     $valor_anterior = $attributes_mod_llamado->valor_anterior;
//     $valor_nuevo = $attributes_mod_llamado->valor_nuevo;

// }