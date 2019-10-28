<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$version = array();
$vAtual = $_SESSION['currentVersion'];

//Inicializando conjunto de dados
$conjuntoOriginal = new Conjunto();
$conjuntoDeTrabalho = new Conjunto();

$verificacao = "inicio";

if($_SESSION['connect']->getStatus() == true){

	$version = $_SESSION['version'];
}

if(isset($_POST['filtrar'])){

	$verificacao = "filtro";

	if($_REQUEST['mode'] == "absolut"){
		$conjuntoOriginal = Arquivo::lerCSV("../dados/bubbleChartRework1.csv");
		$_SESSION['reworkMode'] = "absolut";
	}
	else{ 
		$conjuntoOriginal = Arquivo::lerCSV("../dados/bubbleChartRework2.csv");
		$_SESSION['reworkMode'] = "individual";
	}

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

	Arquivo::salvarCSV("../js/read/bubbleChartRework.csv", $conjuntoDeTrabalho);
}

if($verificacao == "inicio"){

	$_SESSION['reworkMode'] = "absolut";
	$conjuntoOriginal = Arquivo::lerCSV("../dados/bubbleChartRework1.csv");
	$conjuntoDeTrabalho = Processamento::AplicarFiltroVersao($conjuntoOriginal, $vAtual, 3);
	Arquivo::salvarCSV("../js/read/bubbleChartRework.csv", $conjuntoDeTrabalho);
}

?>