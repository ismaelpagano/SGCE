<?php

    include 'funcs.php';
    include '../mods/visor_llamado.php';

    if($_SESSION['sistema']->pagina_actual != NULL){
        $pagina_actual = $_SESSION['sistema']->pagina_actual;
    } else {
        $pagina_actual = 0;
    }

    $_SESSION['sistema']->clave_busqueda = 'seleccion';

    $cant_pagina = $_SESSION['sistema']->cantidad_pagina;

    $total = count($_SESSION['sistema']->seleccion_llamados); //Cuenta la cantidad de compras seleccionadas

    setcookie('result', $total, time() + (86400 * 30), "/"); //setea una cookie durante 30 días

    $paginas = $total / $cant_pagina; //Cuenta la cantidad de paginas necesarias para almacenar los resultados

    for($i=($pagina_actual-5); ($i<=$pagina_actual+5) && ($i<$paginas); $i++){     //Muestra las paginas

        $clase = '';
        
        if($i==$pagina_actual){
            $clase = 'class="current_page"';
        }
        
        if($i+1>0){
            echo '<button '.$clase.' onclick="nueva_busqueda(\''.$_SESSION['sistema']->clave_busqueda.'\', '.$i.')">'.($i+1).'</button>';
        }
    }

    for ($i=($pagina_actual * $cant_pagina); ($i < $pagina_actual * $cant_pagina + $cant_pagina) && ($i < $total); $i++){
    
        $_SESSION['sistema']->seleccion_llamados[$_SESSION['sistema']->seleccion_claves[$i]]->visor_llamado();
        

    }

    for($i=($pagina_actual-5); ($i<=$pagina_actual+5) && ($i<$paginas); $i++){     //Muestra las paginas

        $clase = '';
        
        if($i==$pagina_actual){
            $clase = 'class="current_page"';
        }
        
        if($i+1>0){
            echo '<button '.$clase.' onclick="nueva_busqueda(\''.$_SESSION['sistema']->clave_busqueda.'\', '.$i.')">'.($i+1).'</button>';
        }
    }


?>
