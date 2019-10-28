<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$version = array();
$vAtual = $_SESSION['currentVersion'];

//Inicializando conjunto de dados
$conjuntoOriginal = Arquivo::lerCSV("../dados/bubbleChartAmount.csv");
$conjuntoDeTrabalho = new Conjunto();

$verificacao = "inicio";

if($_SESSION['connect']->getStatus() == true){

	$version = $_SESSION['version'];
}

if(isset($_POST['filtrar'])){

	$verificacao = "filtro";

	$elem = array();
	$elem = explode("-", $_POST['versoes']);
	$_SESSION['currentVersion'] = $elem[1] + 1;

	$vAtual = $_SESSION['currentVersion'];
	$conjuntoDeTrabalho = Processamento::AplicarFiltroVersao($conjuntoOriginal, $vAtual, 3);

	$vetorDeChaves = array();  
	if(isset($_REQUEST['key'])){ //Verificando quais filtros foram selecionados

		foreach ($_REQUEST['key'] as $chaves){             
	      $vetorDeChaves[] = $chaves;
	    }
	}

	if(count($vetorDeChaves) > 0){
	   	$conjuntoDeTrabalho = Processamento::filtrarSeveridade($conjuntoDeTrabalho, $vetorDeChaves, 2);
	}

	Arquivo::salvarCSV("../js/read/bubbleChartAmount.csv", $conjuntoDeTrabalho);
}

if($verificacao == "inicio"){

	$conjuntoDeTrabalho = Processamento::AplicarFiltroVersao($conjuntoOriginal, $vAtual, 3);
	Arquivo::salvarCSV("../js/read/bubbleChartAmount.csv", $conjuntoDeTrabalho);
}

?>