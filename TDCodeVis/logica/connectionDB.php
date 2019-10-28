<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$status = "none";
$connectClick = "display:none;";
if($_SESSION['connect']->getStatus() == true){
	
	$status = "Connected";
	$host = $_SESSION['connect']->getHost();
	$port = $_SESSION['connect']->getPort();
	$db = $_SESSION['connect']->getDatabase();
	$user = $_SESSION['connect']->getUser();
	$pass = $_SESSION['connect']->getPassword();
}
else $status = "Disconnected";

if(isset($_POST['connectBTN'])){

	if($_POST['host'] != "" && $_POST['port'] != "" && $_POST['db'] != "" && $_POST['user'] != "" &&$_POST['password'] != ""){
		
		$_SESSION['connect'] = new ConnectDB();
		$_SESSION['connect']->initialize($_POST['host'],$_POST['port'],$_POST['db'],$_POST['user'],$_POST['password']);
		$_SESSION['connect']->connect();

		$conjunto = $_SESSION['connect']->consult();
		$_SESSION['version'] = $conjunto->getChavesVersoes();
		Arquivo::salvarCSV("../dados/catalog-tdcodevis.csv", $conjunto);

		$v = count($conjunto->getChavesVersoes())-1;
		$_SESSION['currentVersion'] = $v + 1;
		$v = $conjunto->getChavesVersoes()[$v];
		$conjunto = $_SESSION['connect']->consultPath($v);
		Arquivo::salvarCSV("../js/read/treemap.csv", $conjunto);

		$conjunto = $_SESSION['connect']->consultIssuesAmount($_SESSION['version']);
		Arquivo::salvarCSV("../dados/bubbleChartAmount.csv", $conjunto);

		$conjunto = $_SESSION['connect']->consultIssuesRework($_SESSION['version']);
		Arquivo::salvarCSV("../dados/bubbleChartRework1.csv", $conjunto);

		$conjunto = $_SESSION['connect']->consultIssuesRework2($_SESSION['version']);
		Arquivo::salvarCSV("../dados/bubbleChartRework2.csv", $conjunto);

		$conjunto = $_SESSION['connect']->consultAmount3($_SESSION['version']);
		Arquivo::salvarCSV("../dados/starplotSeverity.csv", $conjunto);

		$vetorseverity = array();
		$conjunto = $_SESSION['connect']->consultSeverity($v,false,$vetorseverity);
		Arquivo::salvarCSV("../js/read/boxplotSeverity.csv", $conjunto);

		$conjunto = $_SESSION['connect']->consultSeverity2($_SESSION['version'],"BLOCKER");
		Arquivo::salvarCSV("../js/read/boxplotVersions.csv", $conjunto);		

		echo "<script>window.location('../view/connection.php')</script>";
	}
	$connectClick = "display:none;";
	header("location:connection.php");

}

?>