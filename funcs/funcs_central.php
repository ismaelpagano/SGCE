<?php

    include 'funcs/clases.php';

    if(isset($_POST['objeto'])){

        $id_obj = $_POST['objeto']->hash;

    }

    $_SESSION['sistema']->variables[$id_obj];

?>