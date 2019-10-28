<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$version = array();
$vetorDeChaves = array(5,4,3,2,1);
$vAtual = $_SESSION['currentVersion'];

if($_SESSION['connect']->getStatus() == true){

	$version = $_SESSION['version'];
}

if(isset($_POST['filtrar'])){

	$vetorDeChaves = array();

	if(isset($_REQUEST['severity'])){ //Verificando quais filtros foram selecionados
	    
	    foreach ($_REQUEST['severity'] as $chaves){
	      $vetorDeChaves[] = $chaves;
		}
	}

	if(sizeof($vetorDeChaves) == 0) $filtro = false;
	else $filtro = true;

	$elem = array();
	$elem = explode("-", $_POST['versoes']);
	$_SESSION['currentVersion'] = $elem[1] + 1;
	
	$vAtual = $_SESSION['currentVersion'];
	$_SESSION['connect']->connect();
	$conjunto = $_SESSION['connect']->consultSeverity($elem[0],$filtro,$vetorDeChaves);
	Arquivo::salvarCSV("../js/read/boxplotSeverity.csv", $conjunto);
	
	header("location:boxplotSeverity.php");

}

?>