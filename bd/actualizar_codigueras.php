<?php

    include '../funcs/funcs.php';

    $sql = sql_con(DATABASE_CODIGUERAS);

    function actualizarTopesLegalesCategorias($url, $sql){

        $q = $sql->query('DELETE FROM topes_legales_categorias');
        
        $xml = simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $categoria = $child['id-categoria'];

            $descripcion = $child['descripcion'];
            
            $q = $sql->query("INSERT INTO topes_legales_categorias(id_categoria, descripcion) VALUES ('$categoria', '$descripcion')");
        }
    }

    function actualizarEstadosCompra($url, $sql){

        
        $q = $sql->query('DELETE FROM estados_compra');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id'];

            $descripcion = $child['descripcion'];
            
            $q = $sql->query("INSERT INTO estados_compra(id_estado, descripcion_estado) VALUES ('$id', '$descripcion')");
        }
    }

    function actualizarIncisos($url, $sql){

        
        $q = $sql->query('DELETE FROM incisos');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id-inciso'];

            $nom = $child['nom-inciso'];
            
            $q = $sql->query("INSERT INTO incisos(id_inciso, nom_inciso) VALUES ('$id', '$nom')");
        }
    }

    function actualizarEstadosProveedor($url, $sql){

        
        $q = $sql->query('DELETE FROM estados_proveedor');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $estado = $child['estado'];

            $descripcion = $child['desc-estado'];

            $val_adjs = $child['val-adjs'];

            $val_amps = $child['val-amps'];
            
            $q = $sql->query("INSERT INTO estados_proveedor(estado, descripcion, val_adjs, val_amps) VALUES ('$estado', '$descripcion', '$val_adjs', '$val_amps')");
        }
    }

    function actualizarMonedas($url, $sql){

        
        $q = $sql->query('DELETE FROM monedas');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id-moneda'];

            $descripcion = $child['desc-moneda'];

            $sigla = $child['sigla-moneda'];

            $id_arbitraje = $child['id-moneda-arbitraje'];
            
            $q = $sql->query("INSERT INTO monedas(id_moneda, nom_moneda, sigla_moneda, id_moneda_arbitraje) VALUES ('$id', '$descripcion', '$sigla', '$id_arbitraje')");
        }
    }

    function actualizarObjetosGasto($url, $sql){

        
        $q = $sql->query('DELETE FROM objetos_gasto');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $odg = $child['odg'];

            $descripcion = $child['descripcion'];
            
            $q = $sql->query("INSERT INTO objetos_gasto(odg, nom_gasto) VALUES ('$odg', '$descripcion')");
        }
    }

    function actualizarPorcentajesSubprogramasPCPD($url, $sql){

        
        $q = $sql->query('DELETE FROM porcentajes_subprograma_pcpd');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $codigo = $child['codigo-subprograma'];

            $fecha_vig = formatear_fecha_hora($child['fecha-vigencia']);

            $porcentaje = $child['porcentaje'];
            
            $q = $sql->query("INSERT INTO porcentajes_subprograma_pcpd(codigo_subprograma, fecha_vigencia, porcentaje) VALUES ('$codigo', '$fecha_vig', '$porcentaje')");
        }
    }

    function actualizarSubprogramasPCPD($url, $sql){

        
        $q = $sql->query('DELETE FROM subprogramas_pcpd');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $codigo = $child['codigo'];

            $descripcion = $child['descripcion'];

            $fecha_desde = formatear_fecha_hora($child['fecha-desde']);

            $fecha_hasta =  formatear_fecha_hora($child['fecha-hasta']);
            
            $q = $sql->query("INSERT INTO subprogramas_pcpd(id_subprograma, nom_subprograma, fecha_desde, fecha_hasta) VALUES ('$codigo', '$descripcion', '$fecha_desde', '$fecha_hasta')");
        }
    }

    function actualizarSubtiposCompra($url, $sql){

        
        $q = $sql->query('DELETE FROM subtipos_compra');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id'];

            $id_tipo_compra = $child['id-tipocompra'];

            $resumen = str_replace("'", " ",$child['resumen']);

            $pub_llamado = $child['pub-llamado'];

            $cond_precios_ofertas = $child['cond-precios-ofertas'];

            $fecha_baja = formatear_fecha_hora($child['fecha-baja']);

            $prov_rupe = $child['prov-rupe'];

            $pub_adj = $child['pub-adj'];

            $cant_adj = $child['cant-adj'];
            
            $q = $sql->query("INSERT INTO subtipos_compra(id_subtipo, id_tipocompra, resumen, pub_llamado, cond_precios_ofertas, fecha_baja, prov_rupe, pub_adj, cant_adj) VALUES ('$id', '$id_tipo_compra', '$resumen', '$pub_llamado', '$cond_precios_ofertas', '$fecha_baja', '$prov_rupe', '$pub_adj', '$cant_adj')");
            }

    }

    function actualizarTiposAjusteAdj($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_ajustes_adjudicacion');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id'];

            $descripcion = $child['descripcion'];

            $reiteracion = $child['reiteracion'];

            $resolucion = $child['resolucion'];

            $pub_llamado = $child['pub-llamado'];

            $nuevo_item_oferta = $child['nuevo-item-ofe'];

            $modif_item_adj = $child['modif-item-adj'];

            $nuevo_item_adj = $child['nuevo-item-adj'];
            
            $q = $sql->query("INSERT INTO tipos_ajustes_adjudicacion(id_tipo_ajuste, descripcion, reiteracion, resolucion, pub_llamado, nuevo_item_oferta, modif_item_adj, nuevo_item_adj) VALUES ('$id', '$descripcion', '$resolucion', '$reiteracion', '$pub_llamado', '$nuevo_item_oferta', '$modif_item_adj', '$nuevo_item_adj')");
        }
    }

    function actualizarTiposCompra($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_compra');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id'];

            $descripcion = $child['descripcion'];

            $oferta_economica = $child['oferta-economica'];

            $acto_apertura = $child['acto-apertura'];

            $plazo_min_oferta = $child['plazo-min-oferta'];

            $resolucion_obligatoria = $child['resolucion-obligatoria'];

            $solics_llamado = $child['solics-llamado'];

            $ampliaciones = $child['ampliaciones'];

            $tope_legal = $child['tope-legal'];

            $pcpd = $child['pcpd'];
            
            $q = $sql->query("INSERT INTO tipos_compra(id_tipo_compra, descripcion, oferta_economica, acto_apertura, plazo_min_oferta, resolucion_obligatoria, solics_llamado, ampliaciones, tope_legal, pcpd) VALUES ('$id', '$descripcion', '$oferta_economica', '$acto_apertura', '$plazo_min_oferta', '$resolucion_obligatoria', '$solics_llamado', '$ampliaciones', '$tope_legal', '$pcpd')");
        }
    }

    function actualizarTiposDoc($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_doc');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $tipo = $child['tipo'];

            $descripcion = $child['descripcion'];

            $prov_rupe = $child['prov-rupe'];

            $pcpd = $child['pcpd'];
            
            $q = $sql->query("INSERT INTO tipos_doc(tipo, descripcion, prov_rupe, pcpd) VALUES ('$tipo', '$descripcion', '$prov_rupe', '$pcpd')");
        }
    }

    function actualizarTiposResolucion($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_res');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id = $child['id'];

            $descripcion = $child['descripcion'];
            
            $q = $sql->query("INSERT INTO tipos_res(id_res, descripcion) VALUES ('$id', '$descripcion')");
        }
    }

    function actualizarTiposResTiposAjusAdj($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_res_tipos_ajus_adj');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id_tipoajusteadj = $child['id-tipoajusteadj'];

            $id_tipores = $child['id-tiporesol'];
            
            $q = $sql->query("INSERT INTO tipos_res_tipos_ajus_adj(id_tipoajusteadj, id_tipores) VALUES ('$id_tipoajusteadj', '$id_tipores')");
        }
    }

    function actualizarTiposResCompra($url, $sql){

        
        $q = $sql->query('DELETE FROM tipos_res_tipos_compra');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id_tipores = $child['id-tipo-resol'];

            $id_tipo_compra = $child['id-tipo-compra'];
            
            $q = $sql->query("INSERT INTO tipos_res_tipos_compra(id_tipores, id_tipocompra) VALUES ('$id_tipores', '$id_tipo_compra')");
        }
    }

    function actualizarTopesLegales($url, $sql){

        
        $q = $sql->query('DELETE FROM topes_legales');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id_tipo_compra = $child['id-tipo-compra'];

            $fecha_desde = formatear_fecha_hora($child['fecha-desde']);

            $id_categoria = $child['id-categoria'];

            $tope = $child['tope'];
            
            $q = $sql->query("INSERT INTO topes_legales(id_tipo_compra, fecha_desde, id_categoria, tope) VALUES ('$id_tipo_compra', '$fecha_desde', '$id_categoria', '$tope')");
        }
    }

    function actualizarUETopesAmpliados($url, $sql){

        
        $q = $sql->query('DELETE FROM ues_topes_ampliados');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id_inciso = $child['id-inciso'];

            $id_ue = $child['id-ue'];

            $fecha_desde = formatear_fecha_hora($child['fecha-desde']);

            $fecha_hasta = formatear_fecha_hora($child['fecha-hasta']);

            $id_categoria = $child['id-categoria'];
            
            $q = $sql->query("INSERT INTO ues_topes_ampliados(id_inciso, id_ue, fecha_desde, fecha_hasta, id_categoria) VALUES ('$id_inciso', '$id_ue', '$fecha_desde', '$fecha_hasta', '$id_categoria')");
        }
    }

    function actualizarUCCs($url, $sql){

        
        $q = $sql->query('DELETE FROM unidades_compra_centralizadas');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $id_ucc = $child['id-ucc'];

            $nom_ucc = $child['nom-ucc']; 
                       
            $query = "INSERT INTO unidades_compra_centralizadas(id_ucc, nom_ucc) VALUES ('$id_ucc', '$nom_ucc')";

            echo $query.'<br>';
            
            $q = $sql->query($query);
        }
    }

    function actualizarUEs($url, $sql){

        
        $q = $sql->query('DELETE FROM unidades_ejecutoras');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child ){

            $id_inciso = $child['id-inciso'];

            $id_ue = $child['id-ue'];

            $nom_ue = $child['nom-ue'];

            $query = "INSERT INTO unidades_ejecutoras(id_inciso, id_ue, nom_ue) VALUES ('$id_inciso', '$id_ue', '$nom_ue')";

            echo $query.'<br>';
            
            $q = $sql->query($query);
        }
    }

    function actualizarUnidadesMedida($url, $sql){

        $q = $sql->query('DELETE FROM unidades_medida');

        $xml= simplexml_load_file($url);

        foreach( $xml->children() as $child){

            $cod = $child['cod'];

            $descripcion = $child['descripcion'];

            $fecha_baja = "'".cadena_fecha($child['fecha-baja'])."'";

            if($fecha_baja == '"0000-00-00"'){
                $fecha_baja = NULL;
            }
            
            $motivo_baja = $child['motivo-baja'];

            $query = "INSERT INTO unidades_medida(cod, descripcion, fecha_baja, motivo_baja) VALUES ('$cod', '$descripcion', $fecha_baja, '$motivo_baja')";
            
            echo $query;
            
            $q = $sql->query($query);
        }
    }

    function actualizar_codigueras(){

        $sql = sql_con(DATABASE_CODIGUERAS);
        
        actualizarTopesLegalesCategorias('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTopesLegalesCategorias.do', $sql);
        actualizarEstadosCompra('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteEstadosCompra.do', $sql);
        actualizarIncisos('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteIncisos.do', $sql);
        actualizarEstadosProveedor('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteEstadosProveedor.do', $sql);
        actualizarMonedas('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteMonedas.do', $sql);
        actualizarObjetosGasto('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteObjetosGasto.do', $sql);
        actualizarPorcentajesSubprogramasPCPD('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reportePorcentajesSubprogramasPCPD.do', $sql);
        actualizarSubprogramasPCPD('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteSubprogramasPCPD.do', $sql);
        actualizarSubtiposCompra('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteSubTiposCompra.do', $sql);
        actualizarTiposAjusteAdj('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposAjusteAdj.do', $sql);
        actualizarTiposCompra('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposCompra.do', $sql);
        actualizarTiposDoc('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposDocumento.do', $sql);
        actualizarTiposResolucion('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposResolucion.do', $sql);
        actualizarTiposResTiposAjusAdj('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposResolucionTipoAjusteAdj.do', $sql);
        actualizarTiposResCompra('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTiposResolucionCompra.do', $sql);
        actualizarTopesLegales('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteTopesLegales.do', $sql);
        actualizarUETopesAmpliados('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteUETopesAmpliados.do', $sql);
        actualizarUCCs('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteUCCs.do', $sql);
        actualizarUEs('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteUnidadesEjecutoras.do', $sql);
        actualizarUnidadesMedida('https://www.comprasestatales.gub.uy/comprasenlinea/jboss/reporteUnidadesMedida.do', $sql);

        mysqli_close($sql);

    }

    actualizar_codigueras();

?>