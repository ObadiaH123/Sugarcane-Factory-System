<?php
	
	session_start();
	$sess=trim($_GET['sess']);
	unset($_SESSION[$sess]);
	header("location:login.php");
	
?>