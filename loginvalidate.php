<?php 
	session_start();
	include "dbcon.php";
	
	if(isset($_POST['fon'])){
		$fon=trim(htmlentities(mysqli_real_escape_string($con,$_POST['fon'])));
		$pass=trim(htmlentities(mysqli_real_escape_string($con,$_POST['password'])));
		
		$sql=mysqli_query($con,"SELECT *FROM `farmers` WHERE `phone`='$fon' AND `password`='$pass'");
		$sql2=mysqli_query($con,"SELECT *FROM `agronomists` WHERE `phone`='$fon' AND `password`='$pass'");
		
		if(mysqli_num_rows($sql)===1){
			$res=mysqli_fetch_array($sql);
			$st=$res['status']; $id=$res['id']; $name=prepare(strtolower($res['name']));
			if($st==0){
				echo "Sorry <b>$name</b>, your account has not been approved by the admin";
			}
			else{
				$_SESSION['farmer']=$id;
				echo 'correct:farmer';
			}
		}
		elseif(mysqli_num_rows($sql2)===1){
			$_SESSION['agron']=mysqli_fetch_array($sql2)['id'];
			echo 'correct:agron';
		}
		else{
			echo 'Incorrect phone or password';
		}
	}

?>