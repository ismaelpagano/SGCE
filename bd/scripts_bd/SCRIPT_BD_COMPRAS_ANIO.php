<?php 


    function create_database_compras_anio($anio){

        $sql = sql_con();

        $script = "CREATE DATABASE gestor_compras_estatales_".$anio;

        $q = $sql->query($script);

        mysqli_close($sql);

        $sql = sql_con("gestor_compras_estatales_".$anio);

        $scripts = Array();

        $scripts[] = "CREATE TABLE `compras` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `id_inciso` INT(2) NULL DEFAULT NULL , `id_ue` INT(3) NULL DEFAULT NULL , `id_ucc` INT(2) NULL DEFAULT NULL , `num_compra` VARCHAR(50) NULL DEFAULT NULL , `anio_compra` INT(4) NULL DEFAULT NULL , `nro_ampliacion` INT NULL DEFAULT NULL , `estado_compra` INT NULL DEFAULT NULL , `nombre_pliego` VARCHAR(200) NULL DEFAULT NULL , `fecha_publicacion` DATETIME NULL DEFAULT NULL , `fecha_ult_mod_llamado` DATETIME NULL DEFAULT NULL , `id_tipocompra` CHAR(2) NULL DEFAULT NULL , `subtipo_compra` CHAR(3) NULL DEFAULT NULL , `objeto` VARCHAR(2000) NULL DEFAULT NULL , `fecha_hora_apertura` DATETIME NULL DEFAULT NULL , `lugar_apertura` VARCHAR(200) NULL DEFAULT NULL , `fecha_sol_prorr` DATE NULL DEFAULT NULL , `fecha_sol_aclar` DATE NULL DEFAULT NULL , `fecha_hora_tope_entrega` DATETIME NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `lugar_entrega` VARCHAR(200) NULL DEFAULT NULL , `precio_pliego` FLOAT(15) NULL DEFAULT NULL , `id_moneda_pliego` INT(2) NULL DEFAULT NULL , `lugar_compra_pliego` VARCHAR(200) NULL DEFAULT NULL , `nombre_contacto` VARCHAR(200) NULL DEFAULT NULL , `fax_contacto` VARCHAR(50) NULL DEFAULT NULL , `email_contacto` VARCHAR(50) NULL DEFAULT NULL , `fecha_pub_adj` DATETIME NULL DEFAULT NULL , `fecha_compra` DATE NULL DEFAULT NULL , `fecha_vigencia_adj` DATE NULL DEFAULT NULL , `fondos_rotatorios` CHAR(1) NOT NULL DEFAULT 'N' , `apel` CHAR(1) NOT NULL DEFAULT 'N' , `arch_adj` VARCHAR(200) NULL DEFAULT NULL , `monto_adj` FLOAT(15) NULL DEFAULT NULL , `id_moneda_monto_adj` INT(2) NULL DEFAULT NULL , `id_tipo_resol` INT(3) NULL DEFAULT NULL , `num_resol` INT(9) NULL DEFAULT NULL , `es_reiteracion` CHAR(1) NOT NULL DEFAULT 'N' , `arch_reiteracion` VARCHAR(200) NULL DEFAULT NULL, PRIMARY KEY (`id_compra`(9))) ENGINE = InnoDB;";

        $scripts[] = "CREATE TABLE `items_compra` ( `id_compra` VARCHAR(9) NULL DEFAULT NULL , `nro_item` INT NULL DEFAULT NULL , `cant_pedida` FLOAT(15) NULL DEFAULT NULL , `id_moneda_cotiz` INT(2) NULL DEFAULT NULL , `fecha_hora_puja` DATETIME NULL DEFAULT NULL , `duracion_puja` INT(2) NULL DEFAULT NULL , `tipo_margen_puja` VARCHAR(1) NULL DEFAULT NULL , `margen_puja` INT(13) NULL DEFAULT NULL , `id_articulo` INT(6) NULL DEFAULT NULL , `desc_articulo` VARCHAR(255) NULL DEFAULT NULL , `id_color` INT(3) NULL DEFAULT NULL , `desc_color` VARCHAR(20) NULL DEFAULT NULL , `id_unidad` INT(3) NULL DEFAULT NULL , `unidad` VARCHAR(30) NULL DEFAULT NULL , `id_variante` VARCHAR(30) NULL DEFAULT NULL , `variante` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_variante` VARCHAR(30) NULL DEFAULT NULL , `medida_variante` VARCHAR(60) NULL DEFAULT NULL , `presentacion` VARCHAR(60) NULL DEFAULT NULL , `medida_presentacion` VARCHAR(60) NULL DEFAULT NULL , `unidad_medida_presentacion` VARCHAR(30) NULL DEFAULT NULL , `id_detalle_variante` INT(8) NULL DEFAULT NULL , `desc_detalle_variante` VARCHAR(70) NULL DEFAULT NULL , `id_marca` INT(4) NULL DEFAULT NULL , `desc_marca` VARCHAR(40) NULL DEFAULT NULL ) ENGINE = InnoDB;";


    }

    




?>
