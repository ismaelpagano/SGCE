<?php 

$array = array('14', '22', '32', '04', '35', '42', '29', '06');

$jugadas = array();

for ($i = 0; $i < 4; $i++){
    $numeros = array();
    for($j = 0; $j < 5; $j++){
        $bool = true;
        while($bool){
            $num = $array[rand(0, (count($array) - 1))];
            if(in_array($num, $numeros)){

            } else {
                $bool = false;
            }
        }
            $numeros[$j] = $num;
    }
    $jugadas[$i] = $numeros;
}

for ($i = 0; $i < 4; $i++){
    for($j = 0; $j < 5; $j++){
        echo $jugadas[$i][$j].' ';
    }
    echo '<br>';
}


?>