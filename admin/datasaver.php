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
	
	//save station
	if(isset($_POST['stn'])){
		$name=clean(strtolower($_POST['stn']));
		$loc=trim($_POST['ploc']);
		
		if(empty($name)){
			echo "Error! Station is empty";
		}
		else{
			if(mysqli_query($con,"INSERT INTO `stations` VALUES(id=(id+1),'$name','$loc')")){
				echo "success";
			}
			else{
				echo "Failed to add station";
			}
		}
	}
	
	//save product
	if(isset($_POST['prd'])){
		$prod=clean(strtolower($_POST['prd']));
		$det=clean($_POST['pdet']);
		$cost=clean($_POST['pcost']);
		$tmp=$_FILES['foto']['tmp_name'];
		$name=strtolower($_FILES['foto']['name']);
		$ext=@end(explode(".",$name));
		$allowed=array("jpg","png","jpeg","gif");
		$tm=time();
		
		if($tmp==null){
			echo "Choose product Photo First";
		}
		else{
			if(getimagesize($tmp)){
				if(in_array($ext,$allowed)){
					$newname="Prod-".date("dmY-his").".$ext";
					$saven=$newname;
					$save="../photos/".$newname;
					list($width,$height)=getimagesize($tmp);
					if($ext=="png" || $ext=="PNG"){
						$newname=imagecreatefrompng($tmp);
					}
					if($ext=='jpg' || $ext=='jpeg' || $ext=="JPEG" || $ext=="JPG"){
						$newname=imagecreatefromjpeg($tmp);
					}
					if($width > $height and $width>150){
						$new_width=150;
						$new_height=($height/$width)*150;
					}
					elseif($height>$width and $height>150){
						$new_height=150;
						$new_width=($width/$height)*150;
					}
					elseif($height==$width and $width>150){
						$new_height=150;
						$new_width=150;
					}
					else{
						$new_width=$width;
						$new_height=$height;
					}
				
					$tmp_image=imagecreatetruecolor($new_width,$new_height);
					imagecopyresampled($tmp_image,$newname,0,0,0,0,$new_width,$new_height,$width,$height);
					if(imagejpeg($tmp_image,$save,100)){
						$sql=mysqli_query($con,"INSERT INTO `products` VALUES(id=(id+1),'$prod','$saven','$det','$cost','$tm')");
						if($sql){
							echo 'success';
						}
						else{
							echo "Failed to Save product";
							unlink($save);
						}
					}
					imagedestroy($tmp_image);
					imagedestroy($newname);
				}
				else{
					echo "Image extension $ext is not supported";
				}
			}
			else{
				echo "Choose a valid Image file only";
			}
		}
	}
	
	//save farm inputs
	if(isset($_POST['pname'])){
		$prod=clean($_POST['pname']);
		$det=clean($_POST['det']);
		$cost=clean($_POST['icost']);
		$loc=clean($_POST['cnty']);
		$tmp=$_FILES['pic']['tmp_name'];
		$name=strtolower($_FILES['pic']['name']);
		$ext=@end(explode(".",$name));
		$allowed=array("jpg","png","jpeg","gif");
		$tm=time();
		
		if($tmp==null){
			echo "Choose Farm input Photo First";
		}
		else{
			if(getimagesize($tmp)){
				if(in_array($ext,$allowed)){
					$newname="Input-".date("dmY-his").".$ext";
					$saven=$newname;
					$save="../photos/".$newname;
					list($width,$height)=getimagesize($tmp);
					if($ext=="png" || $ext=="PNG"){
						$newname=imagecreatefrompng($tmp);
					}
					if($ext=='jpg' || $ext=='jpeg' || $ext=="JPEG" || $ext=="JPG"){
						$newname=imagecreatefromjpeg($tmp);
					}
					if($width > $height and $width>150){
						$new_width=150;
						$new_height=($height/$width)*150;
					}
					elseif($height>$width and $height>150){
						$new_height=150;
						$new_width=($width/$height)*150;
					}
					elseif($height==$width and $width>150){
						$new_height=150;
						$new_width=150;
					}
					else{
						$new_width=$width;
						$new_height=$height;
					}
				
					$tmp_image=imagecreatetruecolor($new_width,$new_height);
					imagecopyresampled($tmp_image,$newname,0,0,0,0,$new_width,$new_height,$width,$height);
					if(imagejpeg($tmp_image,$save,100)){
						$sql=mysqli_query($con,"INSERT INTO `inputs` VALUES(id=(id+1),'$prod','$saven','$det','$cost','$loc','$tm')");
						if($sql){
							echo 'success';
						}
						else{
							echo "Failed to Save farm input";
							unlink($save);
						}
					}
					imagedestroy($tmp_image);
					imagedestroy($newname);
				}
				else{
					echo "Image extension $ext is not supported";
				}
			}
			else{
				echo "Choose a valid Image file only";
			}
		}
	}
	
	//verify supply
	if(isset($_POST['vrid'])){
		$id=trim($_POST['vrid']);
		
		if(mysqli_query($con,"UPDATE `supplies` SET `status`='1' WHERE `id`='$id'")){
			echo "success";
		}
		else{
			echo "Failed to complete the request";
		}
	}
	
	if(isset($_POST['dopt'])){
		$opt=trim($_POST['dopt']);
		$id=trim($_POST['did']);
		
		if(mysqli_query($con,"UPDATE `demand` SET `status`='$opt' WHERE `id`='$id'")){
			echo "success";
		}
		else{
			echo "Failed to complete the request";
		}
	}
	
	//change demand values
	if(isset($_POST['dtp'])){
		$tbl=trim($_POST['dtp']);
		$id=trim($_POST['did']);
		$val=clean($_POST['dval']);
		
		if(!is_numeric($val)){
			echo "Error! Enter a numeric value";
		}
		else{
			if(mysqli_query($con,"UPDATE `demand` SET `$tbl`='$val' WHERE `id`='$id'")){
				echo "success";
			}
			else{
				echo "Failed to complete the request";
			}
		}
	}
	
	//activate/deactivate farmer account
	if(isset($_POST['acst'])){
		$st=trim($_POST['acst']);
		$id=trim($_POST['fid']);
		
		$qry=($st==0) ? "DELETE FROM `farmers` WHERE `idno`='$id'":"UPDATE `farmers` SET `status`='1' WHERE `idno`='$id'";
		if(mysqli_query($con,$qry)){
			echo "success";
		}
		else{
			echo "Failed to complete the request";
		}
	}
	
	//save payment
	if(isset($_POST['ptp'])){
		$tp=clean($_POST['ptp']);
		$mode=trim($_POST['pay']);
		$tm=time();
		
		function gencode(){
			$code="TR".rand(1234567,7654321);
			return $code;
		}
		
		if($tp=="all"){
			$sids="";
			$sql=mysqli_query($con,"SELECT *FROM `supplies` WHERE `paid`='0'");
			while($row=mysqli_fetch_assoc($sql)){$sids.=$row['farmer'].",";}
		}
		else{$sids=$tp;}
		
		$ids=explode(",",rtrim($sids,","));
		
		if(isset($_GET['pcode'])){
			$code=trim($_GET['pcode']);
			
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
					$desc="Payment for : "; $total=0;
					$sql=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$tp' AND `paid`='0'");
					while($row=mysqli_fetch_assoc($sql)){
						$prod=$row['produce']; $qnty=$row['quantity']; $tot=$row['total'];
						$desc.="$qnty $prod @KES $tot, ";  $total+=$tot;
					}
					mysqli_query($con,"UPDATE `supplies` SET `paid`='$tm' WHERE `farmer`='$tp' AND `paid`='0'");
					mysqli_query($con,"INSERT INTO `payments` VALUES(id=(id+1),'$tp','$desc','$mode','$code','$total','$tm')");
					query("UPDATE `kfpcpay` SET `status`='1' WHERE `code`='$code'");
					echo "success";
				}
			}
		}
		else{
			foreach($ids as $fid){
				$desc="Payment for : "; $total=0;
				$sql=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$fid' AND `paid`='0'");
				while($row=mysqli_fetch_assoc($sql)){
					$prod=$row['produce']; $qnty=$row['quantity']; $tot=$row['total'];
					$desc.="$qnty $prod @$tot, ";  $total+=$tot;
				}
				$code=gencode();
				mysqli_query($con,"UPDATE `supplies` SET `paid`='$tm' WHERE `farmer`='$fid' AND `paid`='0'");
				mysqli_query($con,"INSERT INTO `payments` VALUES(id=(id+1),'$fid','$desc','$mode','$code','$total','$tm')");
			}
			
			echo "success";
		}
	}
	
	//save demand
	if(isset($_POST['prodn'])){
		$name=clean(strtolower($_POST['prodn']));
		$ms=clean(strtolower($_POST['ms']));
		$prc=clean($_POST['price']);
		$qnty=clean($_POST['qnty']);
		
		if(mysqli_query($con,"INSERT INTO `demand` VALUES(id=(id+1),'$name','$ms','$prc','$qnty','0')")){
			echo "success";
		}
		else{
			echo "Failed to add demand";
		}
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
	
	
	if(isset($_POST['admin'])){
		$user=clean($_POST['admin']);
		$pass=clean($_POST['pass']);
		$sid=trim($_POST['sid']);
		
		if(mysqli_query($con,"UPDATE `admin` SET `username`='$user',`password`='$pass' WHERE `id`='$sid'")){
			echo 'Details updated successfull';
		}
		else{
			echo 'Failed to update account';
		}
	}

?>