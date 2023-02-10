<?php

    include 'funcs/funcs.php';

    $sql = sql_con();

    $fecha_actual = date('Y-m-d H:i:s');

    $query = "DROP VIEW IF EXISTS gestion_bd_sandbox.nuevos_llamados";

    $q = $sql->query($query);

    $query ="   CREATE VIEW gestion_bd_sandbox.nuevos_llamados 
                AS 
                    (   SELECT compras.id_compra as id_compra , 
                        compras.id_inciso as id_inciso , 
                        compras.id_ue as id_ue ,
                        compras.fecha_hora_tope_entrega as fecha_hora_tope_entrega , 
                        gestion.estado_interno as estado_interno 
                        FROM gestor_compras_estatales_sandbox.compras as compras 
                        INNER JOIN gestion_bd_sandbox.gestion_compras as gestion 
                        ON compras.id_compra = gestion.id_compra
                        WHERE estado_interno < '2' 
                        AND compras.estado_compra = '4'
                        AND fecha_hora_tope_entrega > '".$fecha_actual."' 
                    )
                    ORDER BY compras.fecha_hora_tope_entrega ASC";

    $q = $sql->query($query);

    $cant = 0;

    $query = 'SELECT * FROM gestion_bd_sandbox.nuevos_llamados';

    $q = $sql->query($query);

    $compras = Array();

    if($q){

        print_r($q);

    }



?>

CREATE VIEW gestion_bd_sandbox.llamados_guardados 
    AS 
        (   
            SELECT compras.id_compra as id_compra , 
            compras.id_inciso as id_inciso , 
            compras.id_ue AS id_ue ,
            compras.fecha_hora_tope_entrega AS fecha_hora_tope_entrega , 
            gestion.estado_interno AS estado_interno 
            FROM gestor_compras_estatales_sandbox.compras AS compras 
            INNER JOIN gestion_bd_sandbox.gestion_compras AS gestion 
            ON compras.id_compra = gestion.id_compra
            WHERE estado_interno = '2' OR estado_interno = '3'
            AND compras.estado_compra = '4'
            AND fecha_hora_tope_entrega > '".$fecha_actual."' 
        ) 
        ORDER BY compras.fecha_hora_tope_entrega ASC";

CREATE INDEX index_compras
ON gestion_bd_sandbox.nuevos_llamados (id_compras)

CREATE VIEW gestion_bd_sandbox.llamados_guardados 
    AS 
        (   
            SELECT compras.id_compra as id_compra , 
            compras.id_inciso as id_inciso , 
            compras.id_ue AS id_ue ,
            compras.fecha_hora_tope_entrega AS fecha_hora_tope_entrega , 
            gestion.estado_interno AS estado_interno 
            FROM gestor_compras_estatales_sandbox.compras AS compras 
            INNER JOIN gestion_bd_sandbox.gestion_compras AS gestion 
            ON compras.id_compra = gestion.id_compra
            WHERE estado_interno = '2' OR estado_interno = '3'
            AND compras.estado_compra = '4'
            AND fecha_hora_tope_entrega > '2022-08-05 15:00:00' 
        ) 
        ORDER BY compras.fecha_hora_tope_entrega ASC";