<?php

    include 'funcs/funcs.php';
    include 'bd/create_databases.php';
    include 'bd/create_tables.php';

    crear_dbs();

    create_tables();

    include 'bd/actualizar_catalogo.php';
    include 'bd/actualizar_codigueras.php';




?>