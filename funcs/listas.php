<?php
    
    function estado_interno($estado){//El estado interno del llamado
        
        $ret = Array();

        switch($estado){
            case 0:
            case 'no_visto': 
                $ret[0] = 'no_visto';
                $ret[1] = 'Nuevo llamado';
                $ret[2] = '0';
            break;
            case 1:
            case 'visto': 
                $ret[0] = 'visto';
                $ret[1] = 'Nuevo llamado';
                $ret[2] = '1';
            break;
            case 2:
            case 'guardado': 
                $ret[0] = 'guardado';
                $ret[1] = 'Llamado guardado';
                $ret[2] = '2';
            break;
            case 3:
            case 'elaborando_oferta': 
                $ret[0] = 'elaborando_oferta';
                $ret[1] = 'Elaborando oferta';
                $ret[2] = '3';
            break;
            case 31:
            case 'oferta_elaborada': 
                $ret[0] = 'oferta_elaborada';
                $ret[1] = 'Oferta elaborada';
                $ret[2] = '3';
            break;
            case 32:
            case 'oferta_enviada': 
                $ret[0] = 'oferta_enviada';
                $ret[1] = 'Oferta envíada';
                $ret[2] = '3';
            break;
            case 4:
            case 'rechazado': 
                $ret[0] = 'rechazado';
                $ret[1] = 'Llamado rechazado';
                $ret[2] = '4';
            break;
            case 5:
            case 'adjudicado': 
                $ret[0] = 'adjudicado';
                $ret[1] = 'Adjudicacion';
                $ret[2] = '5';
            break;
            case 6:
            case 'oferta rechazada': 
                $ret[0] = 'oferta rechazada';
                $ret[1] = 'Oferta rechazada';
                $ret[2] = '6';
            break;
            default:
                $ret[0] = 'desconocido';
                $ret[1] = 'Estado llamado desconocido';
                $ret[2] = '7';
            break;
        }

        return $ret;
    }

    function estado_compra($estado){
        switch($estado){
            case 4: $ret = 'en_tramite';
            break;
            case 5: $ret = 'en_estudio';
            break;
            case 7: $ret = 'adjudicada';
            break;
            case 11: $ret = 'ofertas_preparadas';
            break;
            case 17: $ret = 'adjudicada_con_ampliacion';
            break;
            case 19: $ret = 'llamado_anulado';
            break;
            case 27: $ret = 'adjudicada_con_renovacion';
            break;
            default: $ret = 0;
        }

        return $ret;

    }

    function estado_oferta($estado){

        $return = Array();

        switch($estado){
            case 0: 
                $return['id'] = 0;
                $return['nom'] = 'Elaborando oferta';
            break;
            case 1: 
                $return['id'] = 1;
                $return['nom'] = 'Oferta lista';
            break;
            case 2: 
                $return['id'] = 2;
                $return['nom'] = 'Enviada a Consejo Directivo';
            break;
            case 3: 
                $return['id'] = 3;
                $return['nom'] = 'Oferta aprobada';
            break;
            case 4: 
                $return['id'] = 4;
                $return['nom'] = 'Oferta envíada';
            break;
            case 5: 
                $return['id'] = 5;
                $return['nom'] = 'Oferta rechazada';
            break;           
            default: 
                $return['id'] = 5;
                $return['nom'] = 'Estado desconocido';
            break;
        }

        return $return;

    }

    function perfil_usuario($clave){

        $ret = '';

        switch($clave){
            case 0: $ret = "Administrador";
            break;
            case 1: $ret = "Presupuestador";
            break;
            case 2: $ret = "Buscador";
            break;
            case 3: $ret = "Bloqueado";
            break;
            default: $ret = "Usuario desconocido";
            
        }

        return $ret;

    };

    // function requerimiento_llamado($codigo){
        
    //     $ret = '';

    //     switch($codigo){

    //         case 0: $ret = "Compra de pliego";
    //         break;
    //         case 1: $ret = "Visita obligatoria";
    //         break;
    //         case 2: $ret = "Certificado";
    //         break;
    //         case 3: $ret = "Listado de personal superior";
    //         break;
    //         case 4: $ret = "Listado de equipos y maquinarias";
    //         break;
    //         case 5: $ret = "Moneda de cotización";
    //         break;
    //         case 6: $ret = "Plazo de ejecución";
    //         break;
    //         case 7: $ret = "Plazo de mantenimiento de oferta";
    //         break;
    //         default: $ret = "Requerimiento defectuoso o desconocido";
    //         break;
            
    //     }

    //     return $ret;
    // }

    function sigla_inciso($id){
        
        $return = '';

        switch($id){
            case 4: $return = 'MI';
            break;
            case 5: $return = 'MEF';
            break;
            case 7: $return = 'MGAP';
            break;
            case 8: $return = 'MIEM';
            break;
            case 10: $return = 'MTOP';
            break;
            case 11: $return = 'MEC';
            break;
            case 12: $return = 'MSP';
            break;
            case 13: $return = 'MTSS';
            break;
            case 14: $return = 'MVOT';
            break;
            case 15: $return = 'MIDES';
            break;
            case 25: $return = 'ANEP';
            break;
            case 26: $return = 'UDELAR';
            break;
            case 27: $return = 'INAU';
            break;
            case 28: $return = 'BPS';
            break;
            case 29: $return = 'ASSE';
            break;
            case 31: $return = 'UTU';
            break;
            case 32: $return = 'INUMET';
            break;
            case 34: $return = 'JUTEP';
            break;
            case 35: $return = 'INISA';
            break;
            case 50: $return = 'BCU';
            break;
            case 51: $return = 'BROU';
            break;
            case 52: $return = 'BHU';
            break;
            case 53: $return = 'BSE';
            break;
            case 60: $return = 'ANCAP';
            break;
            case 61: $return = 'UTE';
            break;
            case 62: $return = 'AFE';
            break;
            case 63: $return = 'PLUNA';
            break;
            case 64: $return = 'ANP';
            break;
            case 65: $return = 'ANTEL';
            break;
            case 66: $return = 'OSE';
            break;
            case 68: $return = 'ANV';
            break;
            case 69: $return = 'URSEA';
            break;
            case 71: $return = 'URSEC';
            break;
            case 98: $return = 'IM';
            break;
            default: $return = NULL;
            break; 
        }

        return $return;

    }

    function sector_($id_tipo){

    }

    function sector_asignado($cod){

        $retorno = Array();

        switch($cod){
            case 0: 
                $retorno['nom'] = 'Operativa';
            break;
            case 1: 
                $retorno['nom'] = 'Herreria';
            break;
            case 2: 
                $retorno['nom'] = 'Albañilería y pintura';
            break;
            case 3: 
                $retorno['nom'] = 'Señales';
            break;
            case 4: 
                $retorno['nom'] = 'Software Factory';
            break;
            default: 
                $retorno['nom'] = 'Otros';
            break;
        }

        return $retorno;
        
    }

?>