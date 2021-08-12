<?php
	include "dbcon.php";
	
	//products
	if(isset($_GET['products'])){
		$sid=trim($_GET['sid']); $data="";
		$sql=mysqli_query($con,"SELECT *FROM `products` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare(ucfirst($row['product'])); $det=nl2br(prepare($row['details'])); $pic=$row['photo']; $id=$row['id'];
			$cost=fnum($row['cost']); $post=date("M d, h:i a",$row['time']);
			$data.="<tr valign='top'><td><img src='photos/$pic' style='max-width:100%;max-height:120px'></td><td><h4 style='color:#008080'>$name @Ksh $cost</h4>
			<p style='padding:6px 0px'>$det</p><p style='padding:10px 0px;text-align:right;color:grey;'><i>$post</i></p>
			<p style='text-align:right'><button class='btn'style='background:#E9967A'onclick=\"addcart('$id','products')\"><i class='fa fa-cart-plus'></i> Buy</button></p></td></tr>";
		}
		
		$tr=""; $tot=0;
		$qry=mysqli_query($con,"SELECT DISTINCT `item`,`photo`,`cost` FROM `purchases` WHERE `farmer`='$sid'");
		while($row=mysqli_fetch_assoc($qry)){
			$name=prepare($row['item']); $pic=$row['photo']; $cost=fnum($row['cost']);
			$sq=mysqli_query($con,"SELECT *FROM `purchases` WHERE `item`='".$row['item']."' AND `photo`='".$row['photo']."'");
			$no=mysqli_num_rows($sq);
			while($rw=mysqli_fetch_assoc($sq)){
				$paid=$rw['paid']; $dy=date("M d, H:i",$rw['time']);
				if($paid==0){$tot+=$rw['cost'];} $pd=($paid==0) ? "Unpaid":"Paid on ".date("d-m-Y, h:i a",$paid);
			}
			$tr.="<tr valign='top'><td><img src='photos/$pic'height='80px'></td><td><h4>$name</h4><p style='padding:6px 0px'>($no) Ksh $cost</p>
			<p style='font-size:14px;color:#2f4f4f'>$pd</p><p style='color:blue;text-align:right'><i>$dy</i></p></td></tr>";
		}
		
		echo "<h3 class='hd'>Company Products</h3><br><input type='hidden' id='temps' value=''><div id='cbtn'></div>";
		echo ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No products for sale from the company</p>":
		"<table cellpadding='10'>$data</table>";
		echo "<br><br><h3 style='color:blue'>Last Purchased</h3><br><table cellpadding='10'>$tr</table><br></div>";
	}
	
	//get news
	if(isset($_GET['news'])){
		$res="";
		$sql=mysqli_query($con,"SELECT *FROM `news` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$mssg=nl2br(prepare($row['message'])); $loc=ucfirst($row['county']); 
			$day=date("M d, h:i a",$row['time']); $sen=ucfirst(prepare($row['source']));
			$res.="<tr valign='top'><td><div style='width:50px;height:50px;border-radius:50%;background:#2E8B57;text-align:center'>
			<i class='fa fa-user'style='font-size:25px;color:#fff;margin-top:10px'></i></div></td>
			<td><h4>$loc</h4><p style='padding:5px 0px'>$mssg</p><p style='color:grey'><i><span style='color:#008fff'>$sen,</span> $day</i></p>
			</td></tr>";
		}
		
		echo "<br><div style='margin:0 auto;max-width:400px;'><h3 style='color:blue;'>County News 
		<button class='btn'style='float:right'onclick=loadpage('postnews')>Post News</button></h3><br>";
		echo ($res=="") ? "<p style='color:grey;line-height:80px'>No News</p>":"<table cellpadding='10'>$res</table>";
		echo "</div>";
	}
	
	//post news
	if(isset($_GET['postnews'])){
		$sid=trim($_GET['sid']);
		$sql=mysqli_query($con,"SELECT *FROM `agronomists` WHERE `id`='$sid'");
		while($row=mysqli_fetch_assoc($sql)){
			$name=$row['name']; $loc=$row['location'];
		}
		?>
		<div style="max-width:400px;margin:0 auto">
		<h3 style="color:blue">Post News</h3><br>
		<form method="post" id="nfom" onsubmit="savenews(event)">
		<input type="hidden" name="src" value="<?php echo $name;?>"><input type="hidden" name="nloc" value="<?php echo $loc;?>">
		<p><textarea name="nmsg" id="nmsg"placeholder="Type Message" required autofocus></textarea></p><br>
		<p style="text-align:right"><button class="btn">Post</button></p><br>
		</form>
		</div><br>
		<?php
	}
	
	//load agronomist chats
	if(isset($_GET['agronchats'])){
		$sid=trim($_GET['sid']);
		$data=""; 
		$flow="a-$sid";
		$sql=mysqli_query($con,"SELECT DISTINCT `sender` FROM `chats` WHERE NOT `sender`='$flow' AND `receiver`='$flow' ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$sen=trim($row['sender']);
			$senda=explode("-",$sen)[1];
			$sq=mysqli_query($con,"SELECT *FROM `chats` WHERE `sender`='$sen' AND `receiver`='$flow' AND `status`='0'");
			$no=mysqli_num_rows($sq);
			$sqt=mysqli_query($con,"SELECT *FROM `chats` WHERE (`sender`='$sen' AND `receiver`='$flow') OR (`sender`='$flow' AND `receiver`='$sen')");
			$nu=mysqli_num_rows($sqt);
			$cond=($nu==1) ? "1 Message":"$nu Messages";
			if($no>0){$cnd="<button class='notbtn'style='background:green' >$no</button>";}else{$cnd="";}
			$sqr=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$senda'");
			while($rw=mysqli_fetch_assoc($sqr)){
				$name=prepare(ucwords($rw['name'])); 
				$data.="<div class='udiv'onclick=\"gotochat('$sen','$flow')\">
				<img src='images/user.png'style='height:100%;float:left;margin-right:20px;border-radius:50%'><h4>$name $cnd</h4>
				<p style='color:grey;padding:5px 0px'>$cond</p>
				</div><br>";
			}
		}
		echo '<div style="max-width:500px;margin:0 auto">
		<h3 style="padding:10px 0px;color:blue;text-align:center">Farmer Chats </h3><br>
		'.$data.' </div>';
	}
	
	//buy products
	if(isset($_GET['buyprods'])){
		$ids=trim($_GET['buyprods']);
		$sid=trim($_GET['sid']);
		$data=""; $tot=0; $all=explode(",",$ids);
		foreach($all as $id){
			$sql=mysqli_query($con,"SELECT *FROM `products` WHERE `id`='$id'");
			while($row=mysqli_fetch_assoc($sql)){
				$name=prepare(ucfirst($row['product'])); $pic=$row['photo']; $cost=fnum($row['cost']); $tot+=$row['cost'];
				$data.="<tr valign='top'><td><img src='photos/$pic'height='60px'></td><td><p>$name</p><p>Ksh $cost</p></td></tr>";
			}
		}
		echo "<div style='max-width:450px;margin:0 auto'><h3 class='hd'>Cart Items</h3><br><table cellpadding='10'>$data</table><br>
		<h4 style='text-align:right'>Total Ksh ".fnum($tot)."</h4><br><p style='text-align:right'>
		<button class='btn' style='background:#FF7F50'onclick=\"loadpage('products')\">Cancel</button> 
		<button class='btn' onclick=\"payitems('$ids','$tot','$sid','saveprods')\">Buy Items</button></p><br></div>";
	}
	
	//buy farm inputs //careers@powergovernors.co.ke
	if(isset($_GET['buyinputs'])){
		$ids=trim($_GET['buyinputs']);
		$sid=trim($_GET['sid']);
		$data=""; $tot=0; $all=explode(",",$ids);
		foreach($all as $id){
			$sql=mysqli_query($con,"SELECT *FROM `inputs` WHERE `id`='$id'");
			while($row=mysqli_fetch_assoc($sql)){
				$name=prepare($row['name']); $pic=$row['photo']; $cost=fnum($row['cost']); $tot+=$row['cost'];
				$data.="<tr valign='top'><td><img src='photos/$pic'height='60px'></td><td><p>$name</p><p>Ksh $cost</p></td></tr>";
			}
		}
		echo "<div style='max-width:450px;margin:0 auto'><h3 class='hd'>Cart Items</h3><br><table cellpadding='10'>$data</table><br>
		<h4 style='text-align:right'>Total Ksh ".fnum($tot)."</h4><br><p style='text-align:right'>
		<button class='btn' style='background:#FF7F50'onclick=\"loadpage('inputs')\">Cancel</button> 
		<button class='btn' onclick=\"payitems('$ids','$tot','$sid','saveitems')\">Buy Items</button></p><br></div>";
	}
	
	//farm inputs
	if(isset($_GET['inputs'])){
		$sid=trim($_GET['sid']); $data="";
		$sql=mysqli_query($con,"SELECT *FROM `inputs` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare($row['name']); $det=nl2br(prepare($row['details'])); $pic=$row['photo']; $id=$row['id'];
			$cost=fnum($row['cost']); $loc=prepare($row['county']); $post=date("M d, h:i a",$row['time']);
			$data.="<tr valign='top'><td><img src='photos/$pic' style='max-width:100%;max-height:120px'></td><td><h4 style='color:#008080'>$name @Ksh $cost</h4>
			<p style='padding:6px 0px'>$det</p><p style='color:#008fff;font-size:14px'>For $loc county</p>
			<p style='padding:10px 0px;text-align:right;color:grey;'><i>$post</i></p>
			<p style='text-align:right'><button class='btn'style='background:#E9967A'onclick=\"addcart('$id','inputs')\"><i class='fa fa-cart-plus'></i> Buy</button></p></td></tr>";
		}
		
		$tr=""; $tot=0;
		$qry=mysqli_query($con,"SELECT DISTINCT `product`,`photo`,`cost` FROM `sales` WHERE `farmer`='$sid'");
		while($row=mysqli_fetch_assoc($qry)){
			$name=prepare($row['product']); $pic=$row['photo']; $cost=fnum($row['cost']);
			$sq=mysqli_query($con,"SELECT *FROM `sales` WHERE `product`='".$row['product']."' AND `photo`='".$row['photo']."'");
			$no=mysqli_num_rows($sq);
			while($rw=mysqli_fetch_assoc($sq)){
				$paid=$rw['paid']; $dy=date("M d, H:i",$rw['time']);
				if($paid==0){$tot+=$rw['cost'];} $pd=($paid==0) ? "Unpaid":"Paid on ".date("d-m-Y, h:i a",$paid);
			}
			$tr.="<tr valign='top'><td><img src='photos/$pic'height='80px'></td><td><h4>$name</h4><p style='padding:6px 0px'>($no) Ksh $cost</p>
			<p style='font-size:14px;color:#2f4f4f'>$pd</p><p style='color:blue;text-align:right'><i>$dy</i></p></td></tr>";
		}
		
		echo "<h3 class='hd'>Farm Inputs</h3><br><input type='hidden' id='temps' value=''><div id='cbtn'></div>";
		echo ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Farm Inputs found for farmers</p>":
		"<table cellpadding='10'>$data</table>";
		echo "<br><br><h3 style='color:blue'>Last Purchased</h3><br><table cellpadding='10'>$tr</table><br></div>";
	}
	
	//cancel supply
	if(isset($_POST['delsup'])){
		$tm=trim($_POST['delsup']);
		$pid=explode(":",trim($_POST['pid']));
		$prod=$pid[0]; $qnty=$pid[1];
		if(mysqli_query($con,"DELETE FROM `supplies` WHERE `time`='$tm'")){
			mysqli_query($con,"UPDATE `demand` SET `maxsupply`=(maxsupply+$qnty) WHERE `product`='$prod'");
			echo "Deleted";
		}
		else{
			echo "Failed to Cancel supply";
		}
	}
	
	//farm produce
	if(isset($_GET['produce'])){
		$sid=trim($_GET['sid']); $data="";
		$tp=trim($_GET['produce']);
		$sql=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$sid' ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$prd=$row['produce']; $qnty=$row['quantity']; $rate=fnum($row['rate']); $status=$row['status']; $tm=$row['time']; 
			$day=date("M d, H:i",$tm); $paid=($row['paid']==0) ? "Supply Unpaid":"Paid on ".date("d-m-Y,h:i a",$row['paid']);
			$tot=fnum($row['total']); $qn=$row['total']/$row['rate']; $prod=prepare(ucwords($prd));
			$cond=($status==0) ? "<p style='color:grey'><i class='fa fa-clock'></i> <i>Unverified</i></p>":"<p style='color:green'><i class='fa fa-check'></i> <i>Verified</i></p>";
			$cnd=($status==0) ? "<span class='lst'onclick=\"cancelsup('$tm','$prd:$qn')\">Cancel Supply</span>":"";
			$data.="<tr valign='top'><td><div style='background:#f0f0f0;max-width:130px;padding:10px;border-top-right-radius:25px;border-bottom-left-radius:25px;'>
			<p>$day</p><p>$cond</p><p style='padding:5px 0px;color:#008080'>$cnd</p></div></td>
			<td><p>$prod</p><p>$qnty x $rate = <b>$tot</b></p><br><p><i style='color:blue'>$paid</i></p></td></tr>";
		}
		
		$prods="";
		$sql=mysqli_query($con,"SELECT *FROM `demand` WHERE `status`='0' AND `maxsupply`>0 ORDER BY `id` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$prod=prepare(ucwords($row['product']));  $id=$row['id']; $cond=($id==$tp) ? "selected":"";
			$prods.="<option value='$id' $cond>$prod</option>";
		}
		
		echo "<div style='max-width:500px;margin:0 auto'><h3 class='hd'>Farm Produce</h3><br>";
		if($prods==""){
			echo "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Product demands from the Company</p>";
		}
		else{
			$qry=($tp!="") ? "SELECT *FROM `demand` WHERE `id`='$tp'":"SELECT *FROM `demand` WHERE `maxsupply`>0 ORDER BY `id` ASC LIMIT 1";
			$sq=mysqli_query($con,$qry);
			while($row=mysqli_fetch_assoc($sq)){
				$ms=prepare(ucwords($row['measure'])); $mxs=$row['maxsupply']; $cost=$row['rate'];
			}
			
			$qry=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$sid' ");
			$loc=mysqli_fetch_assoc($qry)['location']; $opts="";
			
			$sql=mysqli_query($con,"SELECT *FROM `stations` WHERE `county`='$loc'");
			while($row=mysqli_fetch_assoc($sql)){
				$opts.="<option value='".$row['station']."'>".prepare(ucfirst($row['station']))."</option>";
			}
			$opts=($opts=="") ? "<option value='none'>None</option>":$opts;
			
			echo "<form method='post' id='pfom' onsubmit='saveproduce(event)'><table cellpadding='6'>
			<input type='hidden' name='sid' value='$sid'>
			<tr><td colspan='2'>Collection Center<br><select name='pickup'>$opts</select></td></tr>
			<tr valign='top'><td>Product Demand<br><select name='prod'style='width:150px' onchange=\"showprod(this.value)\">$prods</select></td>
			<td>Ksh $cost @$ms</td></tr><tr><td>Supply <br><input type='number'name='qnty'style='width:100px' value='$mxs' required></td>
			<td><br><button class='btn'>Supply</button></td></tr></table>
			</form>";
		}
		
		echo "<br><h4 style='color:blue'>Last Supplies</h4><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:50px;'>No Supply History Found</p>":
		"<table cellpadding='10' style='width:100%;max-width:500px;'>$data</table></div>";
		
	}
	
	//payments
	if(isset($_GET['payments'])){
		$sid=trim($_GET['sid']); $data="";
		$sql=mysqli_query($con,"SELECT *FROM `payments` WHERE `farmer`='$sid' ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$mod=prepare($row['mode']); $amnt=fnum($row['amount']); $desc=nl2br(ucfirst(prepare($row['description']))); 
			$day=date("M d, Y - h:i a",$row['time']); $code=$row['transaction'];
			$data.="<tr valign='top'><td><div class='pcard'>$mod<br>$code<br>Ksh. $amnt</td>
			<td><p>$desc</p><br><p style='color:#008080;font-size:15px'><i>$day</i></p></td></tr>";
		}
		echo "<h3 class='hd'>Payment History</h3><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:100px;text-align:center'>No Payments Found</p>":
		"<table cellpadding='5' cellspacing='8'>$data</table><br>";
	}
	
	//chats
	if(isset($_GET['chats'])){
		$sid=trim($_GET['sid']); $data="";
		$sql=mysqli_query($con,"SELECT *FROM `agronomists`");
		while($row=mysqli_fetch_assoc($sql)){
			$name=ucwords(prepare($row['name'])); $loc=$row['location']; $fon=$row['phone']; 
			$pic=$row['photo']; $aid=$row['id']; $sen="a-$aid"; $flow="f-$sid";
			$sq=mysqli_query($con,"SELECT *FROM `chats` WHERE `sender`='$sen' AND `receiver`='$flow' AND `status`='0'");
			$sqt=mysqli_query($con,"SELECT *FROM `chats` WHERE (`sender`='$sen' AND `receiver`='$flow') OR (`sender`='$flow' AND `receiver`='$sen')");
			$nu=mysqli_num_rows($sqt); $no=mysqli_num_rows($sq);
			$cond=($nu==1) ? "1 Message":"$nu Messages";
			$cnd=($no>0) ? "<button class='notbtn'style='background:green' >$no</button>":"";
			$img=($pic=="none") ? "images/user.png":"photos/$pic";
			$data.="<div class='udiv' onclick=\"gotochat('$sen','$flow')\">
			<div style=\"width:60px;height:60px;border-radius:50%;background-image:url('$img');background-size:cover;float:left;margin-right:20px\"></div>
			<h4 style='color:#008080'>$name $cnd</h4><p style='color:#2f4f4f;font-size:14px'><i class='fa fa-map-marker'></i> <i>$loc</i></p>
			<p style='color:grey;padding:7px 0px'>$cond</p>
			</div><br>";
		}
		
		echo "<h3 class='hd'>Chat with Agronomist</h3><br><div style='max-width:400px;margin:20px auto;'>$data</div>";
	}
	
	//update agronomist account
		if(isset($_GET['agacc'])){
			$uid=trim($_GET['sid']);
			$sql=mysqli_query($con,"SELECT *FROM `agronomists` WHERE `id`='$uid'");
			while($row=mysqli_fetch_array($sql)){
				$adm=ucwords(strip_tags(stripslashes($row['name'])));
				$pass=strip_tags(stripslashes($row['password']));
				$loc=prepare($row['location']);
				$fon=strip_tags(stripslashes($row['phone']));
				$pic=$row['photo'];
				$logo=($pic=="none") ? "images/user.png":"photos/$pic";
			}
			
			$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr"); $opts="";
			foreach($cos as $pl){
				$cond=($pl==$loc) ? "selected":"";
				$opts.="<option value='$pl' $cond>$pl</option>";
			}
			?>
			<div style="padding:10px;width:260px;margin:0 auto;">
			<form method="post" id="ufom" onsubmit="saveacc(event)">
			<input type="hidden" name="aid" value="<?php echo $uid; ?>">
			<input type="file" style="display:none" id="logo" name="logo" accept="image/*" onchange="changeprof()">
			<center><label for="logo"style="cursor:pointer"title="Click to Change"><img src="<?php echo $logo;?>" height="80"></label></center><br>
			<p>Name<br><input type="text" name="argn" value="<?php echo $adm; ?>"maxlength="20" required></p><br>
			<p>Phone number<br><input type="text" name="fon" id="fon" onkeyup="valid('fon',this.value)" value="<?php echo $fon; ?>"maxlength="10"required></p><br>
			Password<br><div style="width:245px;border:1px solid grey;border-radius:3px;background:#fff">
			<input type="password" name="pass" id="upass" value="<?php echo $pass; ?>"style="width:215px;border:0px;"required> 
			<p style="float:right;margin:7px"><i class="fa fa-eye"style="cursor:pointer" onclick="showps()" id="sps"></i><i class="fa fa-eye-slash"
			style="cursor:pointer;display:none" onclick="showps()" id="hdps"></i></p></div><br>
			<p>Location<br><select name="loc"><?php echo $opts;?></select></p><br>
			<p style="text-align:right"><button class="btn">SAVE</button></p><br>
			</form>
			</div>
			<?php
		}
	
	//update farmer account details
	if(isset($_GET["myacc"])){
		$uid=trim($_GET['sid']);
		$sql=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$uid'");
		while($row=mysqli_fetch_array($sql)){
			$name=ucwords(prepare($row['name'])); $loc=$row['location'];
			$pass=prepare($row['password']); $fon=prepare($row['phone']);
		}
		
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr"); $opts="";
		foreach($cos as $pl){
			$cond=($pl==$loc) ? "selected":"";
			$opts.="<option value='$pl' $cond>$pl</option>";
		}
		
		?>
		<h3 class="hd">Farmer Account</h3><br>
		<div style="padding:10px;width:260px;margin:0 auto;">
			<form method="post" id="ufom" onsubmit="saveacc(event)">
			<input type="hidden" name="fid" value="<?php echo $uid; ?>">
			<p>Name<br><input type="text" name="user" value="<?php echo $name; ?>"maxlength="30" required></p><br>
			<p>Phone number<br><input type="text" name="fon" id="fon" onkeyup="valid('fon',this.value)" value="0<?php echo $fon; ?>"maxlength="10"required></p><br>
			Password<br><div style="width:245px;border:1px solid grey;border-radius:3px;background:#fff">
			<input type="password" name="pass" id="upass" value="<?php echo $pass; ?>"style="width:215px;border:0px;"required> 
			<p style="float:right;margin:7px"><i class="fa fa-eye"style="cursor:pointer" onclick="showps()" id="sps"></i><i class="fa fa-eye-slash"
			style="cursor:pointer;display:none" onclick="showps()" id="hdps"></i></p></div><br>
			<p>Location<br><select name="loc"><?php echo $opts;?></select></p><br>
			<p style="text-align:right"><button class="btn">SAVE</button></p><br>
			</form>
		</div>
		<?php
	}
	
	//change profile
	if(isset($_FILES['logo'])){
		$name=strtolower($_FILES['logo']['name']);
		$tmp=$_FILES['logo']['tmp_name'];
		$ext=end(explode(".",$name));
		$allowed=array("jpg","jpeg","png","gif");
		$bid=trim($_POST['bid']);
		
		if(getimagesize($tmp)){
			if(in_array($ext,$allowed)){
				$newname='prof-'.date("His").'.'.$ext;
				$save="photos/".$newname;
				$saven=$newname;
						
				list($width,$height)=getimagesize($tmp);
				if($ext=="png"){
					$newname=imagecreatefrompng($tmp);
				}
				if($ext=='jpg' || $ext=='jpeg'){
					$newname=imagecreatefromjpeg($tmp);
				}
				if($ext=="gif"){
					$newname=imagecreatefromgif($tmp);
				}
				$new_height=150;
				$new_width=($width/$height)*150;
				
				$sql=mysqli_query($con,"SELECT *FROM `agronomists` WHERE `id`='$bid'");
				$prof=mysqli_fetch_array($sql)['photo'];
						
				$tmp_image=imagecreatetruecolor($new_width,$new_height);
				imagecopyresampled($tmp_image,$newname,0,0,0,0,$new_width,$new_height,$width,$height);
				if(imagejpeg($tmp_image,$save,100)){
					if(mysqli_query($con,"UPDATE `agronomists` SET `photo`='$saven' WHERE `id`='$bid'")){
						echo "success";
						if($prof!="none"){ unlink("photos/$prof"); }
					}
					else{
						echo "Failed to update profile";
						unlink($save);
					}
				}
				imagedestroy($tmp_image);
				imagedestroy($newname);
			}
			else{
				echo "Choose a valid Profile Image";
			}
		}
		else{
			echo "File selected is not an image";
		}
	}

?>