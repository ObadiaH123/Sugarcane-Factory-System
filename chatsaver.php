<?php
	include "dbcon.php";
	
	//delete chat
	if(isset($_POST['dmssg'])){
		$id=trim($_POST['dmssg']);
		if(is_numeric($id)){
			mysqli_query($con,"DELETE FROM `chats` WHERE `time`='$id'");
		}
		else{
			mysqli_query($con,"DELETE FROM `chats` WHERE `type`='$id'");
			unlink("photos/$id");
		}
	}
	
	//save chat
	if(isset($_FILES['photo'])){
		$tmp=$_FILES['photo']['tmp_name'];
		$name=strtolower($_FILES['photo']['name']);
		$capt=clean($_POST['dtxt']);
		$to=clean($_POST['cto']);
		$from=clean($_POST['cfro']);
		$ext=@end(explode(".",$name));
		$allowed=array("png","jpg","jpeg","gif");
		$tm=time();
		
		if(getimagesize($tmp)){
			if(in_array($ext,$allowed)){
				list($width,$height)=getimagesize($tmp);
				if($width<50){
					echo "Only images having width greater than 50px are allowed";
				}
				else{
					$newname="Chat-".date("dmY-his").".$ext";
					$saven=$newname;
					$save="photos/".$newname;
	
					if($ext=="png"){
						$newname=imagecreatefrompng($tmp);
					}
					if($ext=="gif"){
						$newname=imagecreatefromgif($tmp);
					}
					if($ext=='jpg' || $ext=='jpeg'){
						$newname=imagecreatefromjpeg($tmp);
					}
					if($width > $height and $width>600){
						$new_width=600;
						$new_height=($height/$width)*600;
					}
					elseif($height>$width and $height>600){
						$new_height=600;
						$new_width=($width/$height)*600;
					}
					elseif($height==$width and $width>600){
						$new_height=600;
						$new_width=600;
					}
					else{
						$new_width=$width;
						$new_height=$height;
					}
				
					$tmp_image=imagecreatetruecolor($new_width,$new_height);
					imagecopyresampled($tmp_image,$newname,0,0,0,0,$new_width,$new_height,$width,$height);
					if(imagejpeg($tmp_image,$save,100)){
						$sql=mysqli_query($con,"INSERT INTO `chats` VALUES(id=(id+1),'$from','$to','$capt','$saven','0','$tm')");
						if($sql){
							echo 'success';
						}
						else{
							echo "Failed to upload Photo";
							unlink($save);
						}
					}
					imagedestroy($tmp_image);
					imagedestroy($newname);
				}
			}
			else{
				echo "Image extension $ext is not supported";
			}
		}
		else{
			echo "Choose a valid Image file only";
		}
	}
	
	//show customer chats
	if(isset($_GET['showchats'])){
		$flow=trim($_GET['showchats']); $data="";
		$uid=explode(":",$flow)[0]; $rid=explode(":",$flow)[1]; 
		$sid=explode("-",$uid)[1];
		mysqli_query($con,"UPDATE `chats` SET `status`='1' WHERE `sender`='$uid' AND `receiver`='$rid' AND `status`='0'");
		$qry=mysqli_query($con,"SELECT *FROM `chats` WHERE (`sender`='$uid' AND `receiver`='$rid') OR (`sender`='$rid' AND `receiver`='$uid')");
		echo '<div style="width:100%;height:70px"></div><div style="padding:20px;overflow:auto">';
		while($row=mysqli_fetch_assoc($qry)){
			$mssg=nl2br(prepare(ucfirst($row['message']))); $tm=$row['time']; $day=date("M d, h:i a",$tm); 
			$sen=$row['sender']; $typ=$row['type'];
			if($sen==$rid){
				if($typ=="text"){
					echo "<div style='background:#EEE8AA;float:right;'class='card' id='$tm'>
					<p>$mssg</p><p style='padding:5px 0px;text-align:right;color:blue;font-size:14px'><i>$day</i>
					<i class='fa fa-trash-o'style='color:#ff4500;font-size:18px;cursor:pointer;margin-left:20px'onclick=\"delmssg('message','$typ','$tm')\"></i>
					</p></div>";
				}
				else{
					echo "<div style='background:#EEE8AA;float:right;'class='card' id='$tm'><img src='photos/$typ'width='100%'>
					<p style='padding-top:10px'>$mssg</p><p style='padding:5px 0px;text-align:right;color:blue;font-size:14px'><i>$day</i>
					<i class='fa fa-trash-o'style='color:#ff4500;font-size:18px;cursor:pointer;margin-left:20px'onclick=\"delmssg('image','$typ','$tm')\"></i>
					</p></div>";
				}
			}
			else{
				if($typ=="text"){
					echo "<div style='background:#F5DEB3;float:left;'class='card'>
					<p>$mssg</p><p style='padding:5px 0px;text-align:right;font-size:14px;color:blue'><i>$day</i></p></div>";
				}
				else{
					echo "<div style='background:#F5DEB3;float:left;'class='card'><img src='photos/$typ'width='100%'>
					<p style='padding-top:10px'>$mssg</p><p style='padding:5px 0px;text-align:right;font-size:14px;color:blue'><i>$day</i></p></div>";
				}
			}
		}
		
		$sql=mysqli_query($con,"SELECT *FROM `chats` ORDER BY `time` DESC LIMIT 1");
		$maxtm=mysqli_fetch_array($sql)['time']; echo "<input type='hidden' id='maxtm' value='$maxtm'>";
		echo '<div style="height:40px;float:right;width:100%"></div></div>';
	}
	
	//save chat
	if(isset($_POST['chat'])){
		$det=clean($_POST['chat']);
		$to=clean($_POST['cto']);
		$from=clean($_POST['cfro']);
		$tm=time();
		if(mysqli_query($con,"INSERT INTO `chats` VALUES(id=(id+1),'$from','$to','$det','text','0','$tm')")){
			echo "success";
		}
		else{
			echo "Failed to send reply message";
		}
	}
	
	if(isset($_POST['maxtm'])){
		$sql=mysqli_query($con,"SELECT *FROM `chats` ORDER BY `time` DESC LIMIT 1");
		$maxtm=mysqli_fetch_array($sql)['time'];
		echo $maxtm;
	}

?>