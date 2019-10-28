<?php

require_once("../db/ConnectDB.php");
require_once("../processamento/Arquivo.php");
require_once("../processamento/Processamento.php");

session_start();

$_SESSION['severitySelected'] = "Blocker";

if($_SESSION['connect']->getStatus() == true){

	if(isset($_POST['filtrar'])){

		$_SESSION['severitySelected'] = $severity = $_POST['severity'];
		$severity = strtoupper($severity);
		
		$_SESSION['connect']->connect();
		$conjunto = $_SESSION['connect']->consultSeverity2($_SESSION['version'],$severity);
		Arquivo::salvarCSV("../js/read/boxplotVersions.csv", $conjunto);

		header("location:boxplotVersions.php");

	}
}

?>