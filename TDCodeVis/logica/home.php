<?php

require_once("../db/ConnectDB.php");

session_start();

if(!isset($_SESSION['connect'])){
	$_SESSION['connect'] = new ConnectDB();
}

?>