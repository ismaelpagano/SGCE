<?php 

    include 'funcs.php';

    $url = "https://www.comprasestatales.gub.uy/consultas/rss";

    $fecha = fechaHoraActual();

    if(isset($_POST['pub']))
    {
        $url = $url.'/tipo-pub/'.$_POST['pub'];
    } else {
        $url = $url.'/tipo-pub/VIG';
    }

    if(isset($_POST['tipo-doc']))
    {
        $url = $url.'/tipo-doc/'.$_POST['tipo-doc'];
    } else {
        $url = $url.'/tipo-doc/R';
    }

    if(isset($_POST['tipo-fecha']))
    {
        $url = $url.'/tipo-fecha/'.$_POST['tipo-fecha'];
    } else {
        $url = $url.'/tipo-fecha/MOD';
    }

    if(isset($_POST['inicio']))
    {
        $inicio = $_POST['inicio'];
        $inicio = (string)date('Y-m-d', strtotime($inicio));
    } else {
        $fechaInicio = (string)date('Y-m-d');
        $fechaInicio = $fechaInicio.'+00:0000';
    }

    if(isset($_POST['fin']))
    {
        $fin = $_POST['fin'];
        $fin = (string)date('Y-m-d', strtotime($fin));
    } else {
        $fechaFin = (string)date('Y-m-d');
        $fechaFin = $fechaFin.'+23:5959';
    }

    $url =  $url."/rango-fecha/".$fechaInicio."_".$fechaFin."/";

    if(isset($_POST['filtro-cat']))
    {
        $url = $url.'/filtro-cat/'.$_POST['filtro-cat'];
    }

    echo $url;

    $id_compras = Array();

    function feed($feedURL){
        $i = 0; 
        $url = $feedURL; 
        $rss = simplexml_load_file($url); 
            foreach($rss->channel->item as $item) { 
                $link = $item->link;
                $id = substr(strchr($link, 'id/'), 3);
                $id_compras[(string)$id] = $id;
            }
    }

    

    feed($url);

    $a = 'https://www.comprasestatales.gub.uy/consultas/rss/tipo-pub/VIG/tipo-doc/R/tipo-fecha/MOD/rango-fecha/2022-06-02+00:0000_2022-06-02+23:5959/';
    $b = 'https://www.comprasestatales.gub.uy/consultas/rss/tipo-pub/VIG/tipo-doc/R/tipo-fecha/MOD/rango-fecha/2022-06-02+00:0000_2022-06-02+23:5959/';

    if($a == $b){
        echo "bien";
    }

?>

