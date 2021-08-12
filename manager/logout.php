<?php
	session_start();
	unset($_SESSION['manager']);
	header("location:index.php");
?>