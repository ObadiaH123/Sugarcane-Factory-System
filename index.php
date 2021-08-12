<?php 

include("dbcon.php");?>	
<html>
	<title>My Account</title>
	<meta charset="utf-8">
	<meta name="viewport"content="width=device-width,initial-scale=1.0,user-scalable=no,user-scalable=0">

	<head>
		<link rel="shortcut icon"href="images/favi.ico">
		<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
		<script src="js/jquery.js"></script>
		
		<style>
		*{margin:0px;}
		a{text-decoration:none;}
		.main{
			margin:0 auto;max-width:500px;height:100%;background:rgba(0,0,0,0.3);font-family:cambria
		}
		body{background-image:url('images/back.jpg');background-size:cover;background-repeat:no-repeat}
		td{background:rgba(0,250,154,0.6);color:#fff;width:50%;text-shadow:0px 1px 1px #000;font-size:17px;cursor:pointer;border-radius:5px;}
		td:hover{background:rgba(32,178,170,0.7);transition:.5s;color:#fff;}
		</style>
	</head>
	<body>
		<div class="main">
			<div style="padding:20px"><br>
			<center>
				<p><img src="images/logo.png" height="100"></p><br>
				<h3 style="color:#fff;text-shadow:0px 1px 1px #000">Transmara sugar Processing factory</h3><br>
				<table cellpadding="10" cellspacing="15px" style="width:100%;height:300px;text-align:center">
				<tr><td onclick="navigate('manager/index.php')">Manager<br>Signin</td><td onclick="navigate('admin/index.php')">Admin<br>Signin</td></tr>
				<tr><td onclick="navigate('login.php')">Agrnonomist<br>Signin</td><td onclick="navigate('login.php')">Farmer<br>Signin</td></tr>
				</table><br>
				<h4 style="color:#fff;text-shadow:0px 1px 1px #000">Copyright &copy; TSPF 2020</h4>
			</center>
			</div>
		</div>
	</body>
	</html>
	
	<script>
		function navigate(to){
			window.location.replace(to);
		}
	</script>