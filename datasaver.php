<?php
	include "dbcon.php";
	date_default_timezone_set("Africa/Nairobi");
	
	function preparedata($fields,$values){
		$fld=explode(",",$fields); $n=0; $vals=explode("^",$values);
		$data=array();
		foreach($fld as $one){
			$n+=1; $k=$n-1; $val=$vals[$k];
			@$data[$one].=$val;
		}
		return $data;
	}
	
	function query($qry){
		$data=preparedata("query",$qry);
		$res=request("http://mpesa.mabnets.com/kfpcreceive.php",$data);
		return (is_numeric($res)) ? "Failed: No internet Connection":$res;
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
	
	//save news
	if(isset($_POST['src'])){
		$src=clean($_POST['src']);
		$loc=trim($_POST['nloc']);
		$mssg=clean($_POST['nmsg']);
		$tm=time();
		
		if(mysqli_query($con,"INSERT INTO `news` VALUES(id=(id+1),'$loc','$src','$mssg','$tm')")){
			echo "success";
		}
		else{
			echo "Failed to post news";
		}
	}
	
	//save produce
	if(isset($_POST['qnty'])){
		$qnty=clean($_POST['qnty']);
		$pid=clean($_POST['prod']);
		$sid=trim($_POST['sid']);
		$stn=trim($_POST['pickup']);
		
		$sq=mysqli_query($con,"SELECT *FROM `demand` WHERE `id`='$pid'");
		while($row=mysqli_fetch_assoc($sq)){
			$ms=$row['measure']; $mxs=$row['maxsupply']; $cost=$row['rate']; $name=$row['product'];
		}
		
		if($stn=="none"){
			echo "Failed: No pickup stations found in your county,contact admin to fix this";
		}
		elseif(!is_numeric($qnty)){
			echo "Error! Quantity should be numeric";
		}
		elseif($qnty>$mxs){
			echo "Failed: Quantity you wish to supply is above the demand";
		}
		else{
			$total=$qnty*$cost; $tm=time(); $rem=$mxs-$qnty;
			if(mysqli_query($con,"INSERT INTO `supplies` VALUES(id=(id+1),'$sid','$stn','$name','$qnty $ms','$cost','$total','0','0','$tm')")){
				mysqli_query($con,"UPDATE `demand` SET `maxsupply`='$rem' WHERE `id`='$pid'");
				echo 'success';
			}
			else{
				echo 'Failed to save supply';
			}
		}
	}
	
	//save product items
	if(isset($_POST['prids'])){
		$ids=trim($_POST['prids']);
		$sid=trim($_POST['pfid']);
		$code=trim(strtoupper($_POST['pcode']));
		$tm=time();
		$dy=date("d-m-Y");
		
		$sql=preparedata("getdata","SELECT *FROM `kfpcpay` WHERE `code`='$code'");
		$res=request("http://mpesa.mabnets.com/kfpcreceive.php",$sql);
		
		if(is_numeric($res)){echo "Error: Code validation requires internet connection";}
		else{
			$data=json_decode($res,true); $no=0;
			foreach($data as $row){
				$no+=1; $status=$row['status']; $amnt=$row['amount']; $phon=$row['phone'];
			}
		
			if($no===0){
				echo "Failed: Code $code is not recognized";
			}
			elseif($status==1){
				echo "Failed: MPESA Code is already used";
			}
			else{
				$pdes="Payment for company products ";
				foreach(explode(",",$ids) as $id){
					$sql=mysqli_query($con,"SELECT *FROM `products` WHERE `id`='$id'");
					while($row=mysqli_fetch_assoc($sql)){
						$name=$row['product']; $pic=$row['photo']; $cost=$row['cost']; $tm+=1; $des=$row['details'];
						$pdes.="$name - $des @KES $cost,";
						mysqli_query($con,"INSERT INTO `purchases` VALUES(id=(id+1),'$name','$pic','$des','$cost','$sid','$dy','$tm','$tm')");
					}
				}
				query("UPDATE `kfpcpay` SET `status`='1' WHERE `code`='$code'");
				mysqli_query($con,"INSERT INTO `payments` VALUES(id=(id+1),'$sid','$pdes','MPESA','$code','$amnt','$tm')");
				echo "success";
			}
		}
	}
	
	//save input items
	if(isset($_POST['pids'])){
		$ids=trim($_POST['pids']);
		$sid=trim($_POST['fid']);
		$code=trim(strtoupper($_POST['pcode']));
		$tm=time();
		
		$sql=preparedata("getdata","SELECT *FROM `kfpcpay` WHERE `code`='$code'");
		$res=request("http://mpesa.mabnets.com/kfpcreceive.php",$sql);
		
		if(is_numeric($res)){echo "Error: Code validation requires internet connection";}
		else{
			$data=json_decode($res,true); $no=0;
			foreach($data as $row){
				$no+=1; $status=$row['status']; $amnt=$row['amount']; $phon=$row['phone'];
			}
		
			if($no===0){
				echo "Failed: Code $code is not recognized";
			}
			elseif($status==1){
				echo "Failed: MPESA Code is already used";
			}
			else{
				$pdes="Payment for farm Inputs ";
				foreach(explode(",",$ids) as $id){
					$sql=mysqli_query($con,"SELECT *FROM `inputs` WHERE `id`='$id'");
					while($row=mysqli_fetch_assoc($sql)){
						$name=$row['name']; $pic=$row['photo']; $cost=$row['cost']; $tm+=1;
						$pdes.="$name @KES $cost,";
						mysqli_query($con,"INSERT INTO `sales` VALUES(id=(id+1),'$sid','$name','$pic','$cost','$tm','$tm')");
					}
				}
				query("UPDATE `kfpcpay` SET `status`='1' WHERE `code`='$code'");
				mysqli_query($con,"INSERT INTO `payments` VALUES(id=(id+1),'$sid','$pdes','MPESA','$code','$amnt','$tm')");
				echo "success";
			}
		}
	}
	
	//update aggronomist account
	if(isset($_POST['argn'])){
		$user=clean(strtolower($_POST['argn']));
		$loc=clean($_POST['loc']);
		$pass=clean($_POST['pass']);
		$fon=clean($_POST['fon']);
		$sid=trim($_POST['aid']);
		
		if(mysqli_query($con,"UPDATE `agronomists` SET `name`='$user',`password`='$pass',`phone`='$fon',`location`='$loc' WHERE `id`='$sid'")){
			echo 'Details updated successfull';
		}
		else{
			echo 'Failed to update account';
		}
	}
	
	//update farmer account
	if(isset($_POST['user'])){
		$user=clean(strtolower($_POST['user']));
		$loc=clean($_POST['loc']);
		$pass=clean($_POST['pass']);
		$fon=clean($_POST['fon']);
		$sid=trim($_POST['fid']);
		
		if(mysqli_query($con,"UPDATE `farmers` SET `name`='$user',`password`='$pass',`phone`='$fon',`location`='$loc' WHERE `id`='$sid'")){
			echo 'Details updated successfull';
		}
		else{
			echo 'Failed to update account';
		}
	}
	
	if(isset($_POST['getcom'])){
		$u=trim($_POST['getcom']);
		$sql=mysqli_query($con,"SELECT *FROM `chats` WHERE `receiver`='$u' AND `status`='0'");
		echo mysqli_num_rows($sql);
	}

?>