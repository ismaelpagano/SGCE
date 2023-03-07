SELECT compras.id_compra , compras.anio_compra 
FROM ( gestor_compras_estatales_2023.compras as compras 
    INNER JOIN gestion_bd.gestion_compras as gestion 
    ON compras.id_compra = gestion.id_compra ) 
WHERE gestion.estado_arce = '4' 
    AND gestion.fecha_hora_tope_entrega >= '2023-03-07 11:39:11' 
    AND gestion.estado_interno < '2' 
    AND gestion.fecha_hora_tope_entrega >= '2023-03-07 11:39:11' 
    ORDER BY gestion.fecha_hora_tope_entrega ASC;