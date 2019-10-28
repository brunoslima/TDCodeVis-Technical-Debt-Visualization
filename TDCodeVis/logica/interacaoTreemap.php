<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$version = array();
$vAtual = $_SESSION['currentVersion'];

if($_SESSION['connect']->getStatus() == true){

	$version = $_SESSION['version'];

}

if(isset($_POST['filtrar'])){

	$elem = array();
	$elem = explode("-", $_POST['versoes']);
	$_SESSION['currentVersion'] = $elem[1] + 1;

	$vAtual = $_SESSION['currentVersion'];
	$_SESSION['connect']->connect();
	$conjuntoOriginal = $_SESSION['connect']->consultPath($elem[0]);

	$verificacao = "filtro";
	$vetorDeChaves = array();  

	if(isset($_REQUEST['key'])){ //Verificando quais filtros foram selecionados

		foreach ($_REQUEST['key'] as $chaves){             
	      $vetorDeChaves[] = $chaves;
	    }
	}

	if(count($vetorDeChaves) > 0){
	    
	    $conjuntoOriginal = Processamento::filtrarSeveridadeTree($conjuntoOriginal, $vetorDeChaves, 2);
	    //$conjuntoDeTrabalho = Normalizacao::Coluna($conjuntoDeTrabalho,2);
	    
	}
	
	Arquivo::salvarCSV("../js/read/treemap.csv", $conjuntoOriginal);
	header("location:treemap.php");
}
?>