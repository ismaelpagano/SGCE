<?php

    function roles_usuario($codigo){

        $return = '';

        switch($codigo){
            case 0: $return = 'INVITADO';
            break;
            case 1: $return = 'ADMIN';
            break;
            case 2: $return = 'SUPERVISOR';
            break;
            case 3: $return = 'USUARIO';
            break;
        }

        return $return;

    }



?>