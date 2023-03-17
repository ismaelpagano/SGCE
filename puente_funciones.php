<?php 

    include 'funcs/funcs.php';

    class Objeto {

        public string $identificador;
        public string $tipo;
        public $objeto;

        public function __construct($objeto){

            $this->tipo = $objeto->tipo;
            $this->identificador = md5(date('Y-m-d H:i:s'));
            $this->objeto = &$objeto;
            $_SESSION['sistema']->objetos[$this->identificador] = $this;

        }
    }

    class Interprete {

        public $objeto;

        public function __construct($identificador){

            $this->objeto = $_SESSION['sistema']->objetos[$identificador];

        }

        // public function 

        public function tarea(){

            if($this->objeto->tipo == 'Compra'){

                echo $this->objeto->id_compra;

            }

        }

    }

    $_SESSION['sistema']->seleccion = Array();

    $compra = Array();

    $id_compra = '1000000';

    $compra[] = $id_compra;

    $compra[] = 2023;

    $objeto = new Compras($compra);

    print_r($_SESSION['sistema']->seleccion);

    echo $_SESSION['sistema']->objetos[$objeto->identificador]->objeto->id_compra.'<br>';

    $objeto->objeto->id_compra = '1000001';
    
    echo $_SESSION['sistema']->seleccion[$id_compra]->id_compra.'<br>';

    // if(isset($_POST['objeto'])){

    //     $objeto = new Interprete($_POST['objeto']);

    //     print_r($_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra);

    //     $_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra = 'Gabriel';

    //     print_r($_SESSION['sistema']->identificadores[$objeto->identificador]->id_compra);

    // }

?>