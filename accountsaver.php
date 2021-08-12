<?php
	include "dbcon.php";
	
	if(isset($_POST['fname'])){
		$name=trim(htmlentities(mysqli_real_escape_string($con,addslashes(strtolower($_POST['fname'])))));
		$phone=trim(htmlentities(mysqli_real_escape_string($con,addslashes(strtolower($_POST['fon'])))));
		$loc=trim(htmlentities(mysqli_real_escape_string($con,addslashes($_POST['loc']))));
		$pass=trim(htmlentities(mysqli_real_escape_string($con,addslashes($_POST['pass']))));
		$idno=clean($_POST['idno']);
		$time=time(); $err="";
		
		$check=mysqli_query($con,"SELECT *FROM `farmers` WHERE `phone`='$phone'");
		$check2=mysqli_query($con,"SELECT *FROM `farmers` WHERE `idno`='$idno'");
		
		if(count(explode(" ",$name))<2){
			$err= "Please provide more than one name";
		}
		else if(strlen($phone) !=10){
			$err= "Phone number must be 10 numbers";
		}
		else if(mysqli_num_rows($check)===1){
			$err= "Phone number $phone is already in use";
		}
		else if(mysqli_num_rows($check2)===1){
			$err= "ID number $idno is already in the system";
		}
		else{
			if(mysqli_query($con,"INSERT INTO `farmers` VALUES(id=(id+1),'$name','$phone','$idno','$pass','$loc','0','$time')")){
				$err= "success";
			}
			else{
				$err= "Account creation Failed: Try again later";
			}
		}
		
		echo $err;
	}

?>