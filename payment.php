<?php
	
	include "dbcon.php";
	
	function preparedata($fields,$values){
		$fld=explode(",",$fields); $n=0; $vals=explode("^",$values);
		$data=array();
		foreach($fld as $one){
			$n+=1; $k=$n-1; $val=$vals[$k];
			@$data[$one].=$val;
		}
		return $data;
	}
	
	function request($url,$data){
		$str=http_build_query($data);
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$str);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		$result=curl_exec($ch); 
		$err=curl_errno($ch);
		curl_close($ch);
		if($err !=0){$result=$err;}
		return $result;
	}
	
	if(isset($_POST['pay'])){
		$amnt=trim($_POST['pay']);
		$id=trim($_POST['uid']);
		$sql=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$id'");
		$fon=mysqli_fetch_assoc($sql)['phone'];
		
		$post=preparedata("phone,amount","$fon^$amnt");
		$res=request("http://mpesa.mabnets.com/kfpcpay.php",$post);
		
		echo (is_numeric($res)) ? "Failed: Poor Internet Connection":$res;
	}
		
	if(isset($_POST['mpay'])){
		$amnt=trim($_POST['mpay']);
		$fon=trim($_POST['phone']);
		$post=preparedata("phone,amount","$fon^$amnt");
		$res=request("http://mpesa.mabnets.com/kfpcpay.php",$post);
		
		echo (is_numeric($res)) ? "Failed: Poor Internet Connection":$res;
	}

?>