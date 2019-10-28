<?php

//Importando classes
require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");
require_once("../processamento/Normalizacao.php");
require_once("../processamento/Conjunto.php");

//Inicializando conjunto de dados
$conjuntoOriginal = Arquivo::lerCSV("../dados/starplotSeverity.csv");
$conjuntoDeTrabalho = new Conjunto();

$verificacao = "inicio";

session_start();

$version = array();
$vAtual = $_SESSION['currentVersion'];

if($_SESSION['connect']->getStatus() == true){

  $version = $_SESSION['version'];

}

if(isset($_POST['filtrar'])){

  //$elem = array();
  //$elem = explode("-", $_POST['versoes']);
  //$_SESSION['currentVersion'] = $elem[1] + 1;

  $verificacao = "fim";
  $vAtual = $_SESSION['currentVersion'];

  $vetorDeChaves = array();  

  if(isset($_REQUEST['version'])){ //Verificando quais filtros foram selecionados

    foreach ($_REQUEST['version'] as $chaves){ 
      $vetorDeChaves[] = $chaves;
    }
  }

  if(count($vetorDeChaves) > 0){
    $conjuntoDeTrabalho = Processamento::AplicarFiltroStarplot2Versoes($conjuntoOriginal, $vetorDeChaves);
    //$conjuntoDeTrabalho = Normalizacao::Coluna($conjuntoDeTrabalho,1);
    Arquivo::salvarCSV("../js/read/starplotSeverity.csv", $conjuntoDeTrabalho);
  }
  else{
    $conjuntoDeTrabalho = Processamento::AplicarFiltroStarplot2($conjuntoOriginal, $vAtual);
    Arquivo::salvarCSV("../js/read/starplotSeverity.csv", $conjuntoDeTrabalho);
  }

}


if($verificacao == "inicio"){ //Não ativou o filto, então carrega o conjunto de dados por completo
    
  //$conjuntoDeTrabalho = Normalizacao::Coluna($conjuntoOriginal,1);
  $conjuntoDeTrabalho = Processamento::AplicarFiltroStarplot2($conjuntoOriginal,$vAtual);
  Arquivo::salvarCSV("../js/read/starplotSeverity.csv", $conjuntoDeTrabalho);

}

?>