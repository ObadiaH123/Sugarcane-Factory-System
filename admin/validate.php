<?php
	session_start();
	include "dbcon.php";
	
	if(isset($_POST['usern'],$_POST['password'])){
		$user=trim(strtolower(mysqli_real_escape_string($con,addslashes($_POST['usern']))));
		$pass=trim(mysqli_real_escape_string($con,addslashes($_POST['password'])));
		
		$sql=mysqli_query($con,"SELECT *FROM `admin` WHERE `username`='$user' AND `password`='$pass'");
		if(mysqli_num_rows($sql)===0){
			echo "Incorrect username or password";
		}
		else{
			$_SESSION['admin']=mysqli_fetch_array($sql)['id'];
			echo "success";
		}
	}
	
?>