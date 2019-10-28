<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$type = "";
$issue = "";

if(isset($_POST['back'])){

	if($_SESSION["typeHeader"] == "1") header("location:bubbleChartAmount.php");
	else header("location:bubbleChartRework.php");

}
else{

	$url = $_SERVER["REQUEST_URI"];
	$vetor = explode("-", $url);
	$type = $vetor[0];
	$vetor = explode("=", $type);
	$type = $vetor[1];
	$_SESSION["typeHeader"] = "";
	$_SESSION["typeHeader"] = $type;

	$url = $_SERVER["REQUEST_URI"];
	$vetor = explode("-", $url);
	$issue = $vetor[1];
	$vetor = explode("=", $issue);
	$issue = $vetor[1];

	$version = array();
	$vAtual = $_SESSION['currentVersion'];

	if($_SESSION['connect']->getStatus() == true){

		$version = $_SESSION['version'];
		$vAtual = $_SESSION['currentVersion'];
		$vAtual = $vAtual - 1;
		$_SESSION['connect']->connect();
		$conjuntoOriginal = $_SESSION['connect']->consultPathIssues($version[$vAtual],$issue);
		Arquivo::salvarCSV("../js/read/treemapIssues.csv", $conjuntoOriginal);
	}
}

?>