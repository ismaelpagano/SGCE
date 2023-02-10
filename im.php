<?php

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.montevideo.gub.uy/asl/sistemas/siab/Cartelera.nsf/comprasconcomentarios/3E520DB81B9CD342032588EC005849C3');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);  

        $pos = substr($result, strpos($result, '<img src="'));

        $pos2 = substr($pos, strpos($pos, '"'), true);

        echo $pos2;

        // $result = substr_replace($result, "img/im.gif", $pos, 0);

        echo $result;

?>