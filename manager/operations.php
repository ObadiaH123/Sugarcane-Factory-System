<?php
	include "dbcon.php";
	
	//save agronomist
	if(isset($_POST['agname'])){
		$name=clean(strtolower($_POST['agname']));
		$loc=trim($_POST['aloc']);
		$fon=trim($_POST['afon']);
		$pass=clean($_POST['passw']);
		
		if(strlen($fon)!=10){
			echo "Error! Phone number must be 10 numbers";
		}
		else{
			if(mysqli_query($con,"INSERT INTO `agronomists` VALUES(id=(id+1),'$name','$fon','$pass','none','$loc')")){
				echo "success";
			}
			else{
				echo "Failed to add agronomist";
			}
		}
	}
	
	//sales`
	if(isset($_GET['sales'])){
		$data="";
		$sql=mysqli_query($con,"SELECT DISTINCT `day` FROM `purchases` ORDER BY `time` DESC");
		while($rw=mysqli_fetch_assoc($sql)){
			$dy=$rw['day']; $dat="";
			$qry=mysqli_query($con,"SELECT *FROM `purchases` WHERE `day`='$dy'");
			while($row=mysqli_fetch_assoc($qry)){
				$pic=$row['photo']; $item=ucfirst(prepare($row['item'])); $desc=nl2br(html_entity_decode(prepare($row['details'])));
				$cost=fnum($row['cost']); $fid=$row['farmer']; $day=date("h:i a",$row['time']); $pd=$row['paid'];
				$paid=($pd!=0) ? "Paid on ".date("d-m-Y",$pd):"Unpaid";
				$sq=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
				$name=prepare(ucwords(mysqli_fetch_assoc($sq)['name']));
				$dat.="<tr valign='top'><td><img src='../photos/$pic' height='100'></td><td><h4>$item</h4><p>$desc<p><br><p><i>Ksh $cost</i></p></td>
				<td><h4>$name</h4><p style='color:grey;padding-top:10px'>$day<br><i>$paid</i></p></</td></tr>";
			}
			
			$data.="<fieldset style='border:1px solid #dcdcdc'><legend><h3 style='color:blue'>$dy</h3></legend><br>
			<table cellpadding='10'>$dat</table></fieldset><br>";
		}
		
		echo "<div style='max-width:800px;margin:0 auto'>
		<h3 style='color:blue'>Purchases from Farmers <button class='btn' style='padding:5px;float:right;background:#F08080' onclick=\"genpdf('sales')\">
		Gen PDF</button></h3><br>$data
		</div>";
		
	}
	
	//add agronomist
	if(isset($_GET['addagron'])){
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr");; $opts="";
		foreach($cos as $pl){
			$opts.="<option value='$pl'>$pl</option>";
		}
		
		?>
		<div style='max-width:250px;margin:0 auto'>
		<h3 style='color:blue'>Add New Agronomist</h3><br>
		<form method="post" id="afom" onsubmit="saveagron(event)">
		<p>Name<br><input type="text" name="agname" required autofocus></p><br>
		<p>Phone<br><input type="number" name="afon" required></p><br>
		<p>Password<br><input type="text" name="passw" required></p><br>
		<p>County<br><select name='aloc'><?php echo $opts; ?></select></p><br>
		<p style='text-align:right'><button class="btn">Add</button></p><br>
		</form>
		</div>
		<?php
	}
	
	//agronomist
	if(isset($_GET['agronomist'])){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `agronomists` ORDER BY `location` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare(ucwords($row['name'])); $pic=$row['photo']; $fon=$row['phone']; $loc=$row['location'];
			$img=($pic=="none") ? "../images/user.png":"../photos/$pic"; $id=$row['id'];
			$data.="<tr valign='top'><td><img src='$img' height='60'><br>$name</td><td>0$fon</td><td>$loc<br>
			<span class='lnk'onclick=\"delagron('$id')\">Delete</span></td></tr>";
		}
		
		echo "<div style='max-width:600px;margin:0 auto'>
		<h3 style='color:blue'>Agronomists <button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('agronomists')\">
		<i class='fa fa-file-pdf-o'></i> PDF</button><button class='btn'style='float:right;padding:5px;margin-right:20px'onclick=\"loadpage('addagron')\">
		<i class='fa fa-plus'></i> Add New</button></h3></br>
		<table cellpadding='5' style='width:100%;border:1px solid #ccc;border-collapse:collapse;text-align:center' border='1'>
		<tr style='font-weight:bold'><td>Agronomist</td><td>Phone</td><td>Location</td></tr>$data</table><br>
		</div>";
	}
	
	//delete agronomist
	if(isset($_POST['dagron'])){
		$id=trim($_POST['dagron']);
		if(mysqli_query($con,"DELETE FROM `agronomists` WHERE `id`='$id'")){
			mysqli_query($con,"DELETE FROM `chats` WHERE `receiver`='a-$id' OR `sender`='a-$id'");
			echo "Deleted";
		}
		else{
			echo "Failed to delete agronomist";
		}
	}
	
	//delete news 
	if(isset($_POST['dnews'])){
		$tm=trim($_POST['dnews']);
		if(mysqli_query($con,"DELETE FROM `news` WHERE `time`='$tm'")){
			echo "Deleted";
		}
		else{
			echo "Failed to delete news";
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
	
	if(isset($_POST['manager'])){
		$user=clean($_POST['manager']);
		$pass=clean($_POST['pass']);
		$sid=trim($_POST['sid']);
		
		if(mysqli_query($con,"UPDATE `manager` SET `username`='$user',`password`='$pass' WHERE `id`='$sid'")){
			echo 'Details updated successfull';
		}
		else{
			echo 'Failed to update account';
		}
	}
	
	//produce
	if(isset($_GET['produce'])){
		$data="";
		$sql=mysqli_query($con,"SELECT DISTINCT `farmer` FROM `supplies`");
		while($rw=mysqli_fetch_assoc($sql)){
			$fid=$rw['farmer']; $tr=""; $no=2; $totd=0; $totp=0;
			$sq=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
			$name=prepare(ucwords(mysqli_fetch_assoc($sq)['name']));
			$qry=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$fid'");
			while($row=mysqli_fetch_assoc($qry)){
				$prod=prepare(ucwords($row['produce'])); $qnty=$row['quantity']; $rate=$row['rate']; 
				$tot=$row['total']; $st=$row['status']; $id=$row['id'];
				$cond=($st==0) ? "<p style='color:grey'><i class='fa fa-clock-o'></i> Unverified</p>":"<p style='color:green'><i class='fa fa-check'></i> Verified</p>"; 
				$tm=$row['time']; $day=date("d-m-Y, h:i a",$tm); $no+=1; $totp+=$tot;
				$tr.="<tr><td>$day</td><td>$prod</td><td>$qnty x ".fnum($rate)."</td><td>$cond</td><td>".fnum($tot)."</td></tr>";
			}
			
			$tr.="<tr valign='top' style='font-weight:bold;text-align:right'><td colspan='4'>Totals</td><td>".fnum($totp)."</td></tr>";
			$data.="<tr valign='top'><td rowspan='$no'>$name</td>$tr</tr>";
		}
		echo "<h3 style='color:blue'>Produce collection report <button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('produce')\">
		<i class='fa fa-file-pdf-o'></i> PDF</button></h3><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:70px;'>No supplies made</p>":"<table cellpadding='10'style='border:1px solid #ccc;width:100%;
		border-collapse:collapse' border='1'><tr style='font-weight:bold'><td>Farmer</td><td>Date</td><td>Supply</td><td>Quantity</td><td>Status</td>
		<td>Total</td></tr>$data</table>";
		
	}
	
	//payments
	if(isset($_GET['payments'])){
		echo "<br><h3 style='color:blue'>Payment Histoty <button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('payments')\">
		<i class='fa fa-file-pdf-o'></i> PDF</button></h3><br>"; $data="";
		$sql=mysqli_query($con,"SELECT DISTINCT `farmer` FROM `payments` ORDER BY `time` DESC");
		while($rw=mysqli_fetch_assoc($sql)){
			$fid=$rw['farmer']; $tr=""; $no=1;
			$qry=mysqli_query($con,"SELECT *FROM `payments` WHERE `farmer`='$fid'");
			while($row=mysqli_fetch_assoc($qry)){
				$mode=$row['mode']; $code=$row['transaction']; $amnt=fnum($row['amount']); $no+=1;
				$day=date("d-m-Y, h:i a",$row['time']); $desc=nl2br(prepare($row['description']));
				$tr.="<tr><td>$day</td><td>$mode</td><td>$code</td><td>$desc</td></tr>";
			}
			$sq=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
			$name=prepare(ucwords(mysqli_fetch_assoc($sq)['name']));
			$data.="<tr valign='top'><td rowspan='$no'>$name</td>$tr</tr>";
		}
		
		echo "<table cellpadding='5'style='border:1px solid #ccc;width:100%;border-collapse:collapse' border='1'>
		<tr style='font-weight:bold'><td>Farmer</td><td>Date</td><td>Payment</td><td>Transaction</td><td>Details</td></tr>$data</table><br>";
	}
	
	//farm inputs
	if(isset($_GET['inputs'])){
		$sid=trim($_GET['sid']); $data="";
		$sql=mysqli_query($con,"SELECT *FROM `inputs` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare($row['name']); $det=nl2br(prepare($row['details'])); $pic=$row['photo']; $id=$row['id'];
			$cost=fnum($row['cost']); $loc=prepare($row['county']); $post=date("M d, h:i a",$row['time']);
			$data.="<tr valign='top'><td><img src='../photos/$pic' style='max-width:100%;max-height:120px'></td><td><h4 style='color:#008080'>$name @Ksh $cost</h4>
			<p style='padding:6px 0px'>$det</p><p style='color:#008fff;font-size:14px'>For $loc county</p>
			<p style='padding:10px 0px;text-align:right;color:grey;'><i>$post</i></p></td></tr>";
		}
		
		$tr=""; $tot=0;
		$qry=mysqli_query($con,"SELECT DISTINCT `product`,`photo`,`cost`,`farmer` FROM `sales` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($qry)){
			$name=prepare($row['product']); $pic=$row['photo']; $cost=fnum($row['cost']); $fid=$row['farmer'];
			$sq=mysqli_query($con,"SELECT *FROM `sales` WHERE `product`='".$row['product']."' AND `photo`='".$row['photo']."' AND `farmer`='$fid'");
			$no=mysqli_num_rows($sq);
			$qri=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
			$fname=prepare(ucwords(mysqli_fetch_array($qri)['name']));
			while($rw=mysqli_fetch_assoc($sq)){
				$paid=$rw['paid']; $dy=date("M d, H:i",$rw['time']);
				if($paid==0){$tot+=$rw['cost'];} $pd=($paid==0) ? "Unpaid":"Paid on ".date("d-m-Y, h:i a",$paid);
			}
			$tr.="<tr valign='top'><td><img src='../photos/$pic'height='100px'></td><td><h4>$fname</h4><p>$name</p><p style='padding:6px 0px'>($no) Ksh $cost</p>
			<p style='font-size:14px;color:#2f4f4f'>$pd</p><p style='color:blue;text-align:right'><i>$dy</i></p></td></tr>";
		}
		
		echo "<div style='max-width:600px;margin:0 auto'><h3 style='color:blue'>Farm Inputs 
		<button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('inputs')\"><i class='fa fa-file-pdf-o'></i> PDF</button></h3><br>";
		echo ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Farm Inputs found for farmers</p>":
		"<table cellpadding='10'>$data</table>";
		echo "<br><br><h3 style='color:blue'>Purchased Inputs</h3><br><table cellpadding='10'>$tr</table><br></div>";
	}
	
	//farmers
	if(isset($_GET['farmers'])){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `farmers` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=ucwords(prepare($row['name'])); $fon=$row['phone']; $idno=$row['idno']; $loc=$row['location']; $st=$row['status'];
			$day=date("d-m-Y, h:i a",$row['time']);
			$cond=($st==0) ? "<p style='color:grey'><i class='fa fa-clock-o'></i> Unapproved</p>":"<p style='color:green'><i class='fa fa-check'></i> Approved</p>";
			$data.="<tr valign='top'><td>$name</td><td>0$fon</td><td>$idno</td><td>$loc</td><td>$cond</td><td>$day</td></tr>";
		}
		echo "<h3 style='color:blue'>TSPF Farmers <button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('farmers')\">
		<i class='fa fa-file-pdf-o'></i> PDF</button></h3><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:100px'>No record found</p>":"<table cellpadding='10'style='border:1px solid #ccc;
		border-collapse:collapse;width:100%' border='1'><tr style='font-weight:bold'><td>Name</td><td>Phone</td><td>ID No</td><td>County</td>
		<td>Status</td><td>Registration</td></tr>$data</table>";
	}
	
	//get news
	if(isset($_GET['news'])){
		$res="";
		$sql=mysqli_query($con,"SELECT *FROM `news` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$mssg=nl2br(prepare($row['message'])); $loc=ucfirst($row['county']); $tm=$row['time'];
			$day=date("M d, h:i a",$tm); $sen=ucfirst(prepare($row['source']));
			$res.="<tr valign='top'><td><div style='width:50px;height:50px;border-radius:50%;background:#2E8B57;text-align:center'>
			<i class='fa fa-user'style='font-size:25px;color:#fff;margin-top:10px'></i></div></td>
			<td><h4>$loc</h4><p style='padding:5px 0px'>$mssg</p><p style='color:grey'><i><span style='color:#008fff'>$sen,</span> $day</i>
			<i class='fa fa-minus-circle'style='color:brown;float:right;font-size:20px;cursor:pointer'title='Delete'onclick=\"delnews('$tm')\"></i></p>
			</td></tr>";
		}
		
		echo "<br><div style='margin:0 auto;max-width:500px;'><h3 style='color:blue;'>County News 
		<button class='btn'style='float:right'onclick=loadpage('postnews')>Post News</button></h3><br>";
		echo ($res=="") ? "<p style='color:grey;line-height:80px'>No News</p>":"<table cellpadding='10'>$res</table>";
		echo "</div>";
	}
	
	//post news
	if(isset($_GET['postnews'])){
		$sid=trim($_GET['sid']);
		$sql=mysqli_query($con,"SELECT *FROM `manager` WHERE `id`='$sid'");
		while($row=mysqli_fetch_assoc($sql)){$name=$row['username'];}
		
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr");; $opts="";
		foreach($cos as $pl){
			$opts.="<option value='$pl'>$pl</option>";
		}
		?>
		<div style="max-width:400px;margin:0 auto">
		<h3 style="color:blue">Post News</h3><br>
		<form method="post" id="nfom" onsubmit="savenews(event)">
		<input type="hidden" name="src" value="manager <?php echo $name;?>">
		<p><textarea name="nmsg" id="nmsg"placeholder="Type Message" required autofocus></textarea></p><br>
		<p>News for county<br><select name="nloc"style="width:150px"><?php echo $opts?></select><button class="btn"style="float:right">Post</button></p><br>
		</form>
		</div><br>
		<?php
	}
	
	if(isset($_GET['tp'])){
		$tp=trim($_GET['tp']);
		
		//update account details
		if($tp=="myacc"){
			$uid=trim($_GET['vl']);
			$sql=mysqli_query($con,"SELECT *FROM `manager` WHERE `id`='$uid'");
			while($row=mysqli_fetch_array($sql)){
				$adm=ucwords(strip_tags(stripslashes($row['username'])));
				$pass=strip_tags(stripslashes($row['password']));
			}
			?>
			<div style="padding:10px;width:260px;margin:20px;">
			<form method="post" id="ufom" onsubmit="saveacc(event)">
			<input type="hidden" name="sid" value="<?php echo $uid;?>">
			<p style="font-size:22px;font-family:rockwell">Manager account</p><br>
			<p>Username<br><input type="text" name="manager" value="<?php echo $adm; ?>" required></p><br>
			Password<br><div style="width:245px;border:1px solid grey;border-radius:3px;background:#fff">
			<input type="password" name="pass" id="upass" value="<?php echo $pass; ?>"style="width:215px;border:0px;"required> 
			<p style="float:right;margin:7px"><i class="fa fa-eye"style="cursor:pointer" onclick="showps()" id="sps"></i><i class="fa fa-eye-slash"
			style="cursor:pointer;display:none" onclick="showps()" id="hdps"></i></p></div>
			<br><button class="btn" style="float:right">SAVE</button></form>
			</div>
			<?php
		}
	}
?>