<?php

    include 'funcs/funcs.php';

	//     actualizador('2022-07-20', '2022-07-21');

    // $_SESSION['sistema'] = '';

    // $sistema = new Sistema($_SESSION['sistema']);

    // $usuario = new Sesion_usuario("Ismael", "texto");

    // if($usuario->bool_session){
    //         echo "Ingesó exitosamente";
    // } else {
    //         echo "Error";
    // }

    // $sql = sql_con(DATABASE_COMPRAS);

    // $query = "SELECT * from items_compra as items INNER JOIN compras on items.id_compra = compras.id_compra where compras.fecha_publicacion >= '2022-07-20 00:00:00' AND fecha_publicacion <= '2022-07-20 23:59:59'";

    // $q = $sql->query($query);

    // $i = 0;

    // if($q){
    //         while($r = $q->fetch_object()){
    //                 $i++;
    //         }
    // }
	

	// get_registros_fecha();

    // $fecha = '01/01/2022 05:40';

    // echo strtotime(cadena_fecha_hora($fecha)).'<br>';

    // echo time();

        // class Compra{
            
        //     public $id_compra;

        //     public function __construct($id_compra){
        //         $this->id_compra = $id_compra;
        //     }
        // }

        // $compra = new Compra('416869');

        // $compra = new Compras($compra);

        // $compra->completar_datos();

        // $compra->enviar_objeto_socket();

        $sql = sql_con(DATABASE_GESTION);

        $q = "SELECT * FROM tipos_requerimiento WHERE fecha_baja IS NULL";

        $q = $sql->query($q);

        if($q){

            while($r = $q->fetch_object()){

                $result = Array();
                $result['id'] = $r->id_requerimiento;
                $result['descripcion'] = $r->descripcion;
                print_r($result);
                echo '<br>';

            }
        }

        mysqli_close($sql);


?>

<!DOCTYPE html>
<html>
    <head>
            <meta charset="UTF-8" />
            <title>Test</title>
            <script src="js/funcs.js"></script>
    </head>
    <body>
            <?php 
                    // $sistema->mostrar();

                    // echo $i;
            ?>
    </body>
</html>