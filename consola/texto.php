<?php

    include 'funcs.php';

    $myfile = file("testfile.txt") or die("Unable to open file!");


    $query = '';

    $i = 0;

    foreach($myfile as $line){

        if($i < 10){

            $query .= $line."; ";
            $i++;

        } else {

            $sql = sql_con('gestion_bd');
            $q = $sql->multi_query($query);
            if($q);
            $i = 0;
            $query = $line."; ";
            mysqli_close($sql);

        }

    }

?>