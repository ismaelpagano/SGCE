<?php 

    include 'funcs/funcs.php';

    class Interprete {


        public function __construct($identificador){

            $this->objeto = $_SESSION['sistema']->identificadores[$identificador];
            $this->identificador = $identificador;

        }

        public function tarea(){

            if($this->objeto->tipo == 'Compra'){

                echo $this->objeto->id_compra;

            }

        }

    }


    if(isset($_POST['objeto'])){

        $objeto = new Interprete($_POST['objeto']);

        print_r($_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra);

        $_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra = 'Gabriel';

        print_r($_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra);

    }

?>