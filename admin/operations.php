<?php
	include "dbcon.php";
	
	//delete product
	if(isset($_POST['dprod'])){
		$id=trim($_POST['dprod']);
		if(mysqli_query($con,"DELETE FROM `products` WHERE `id`='$id'")){
			echo "Deleted";
		}
		else{
			echo "Failed to delete product";
		}
	}
	
	//delete input
	if(isset($_POST['dinput'])){
		$id=trim($_POST['dinput']);
		if(mysqli_query($con,"DELETE FROM `inputs` WHERE `id`='$id'")){
			echo "Deleted";
		}
		else{
			echo "Failed to delete Input";
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
	
	//delete station
	if(isset($_POST['dstn'])){
		$id=trim($_POST['dstn']);
		if(mysqli_query($con,"DELETE FROM `stations` WHERE `id`='$id'")){
			echo "Deleted";
		}
		else{
			echo "Failed to delete station";
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
		<h3 style='color:blue'>Purchases from Farmers <button class='btn' style='padding:5px;float:right' onclick=\"loadpage('addprod')\">
		Products</button></h3><br>$data
		</div>";
		
	}
	
	//products
	if(isset($_GET['addprod'])){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `products` ORDER BY `time` DESC");
		while($row=mysqli_fetch_array($sql)){
			$img=$row['photo']; $desc=nl2br(prepare(ucfirst($row['details'])));  $id=$row['id'];
			$prod=ucwords(prepare($row['product'])); $cost=fnum($row['cost']);
			$data.="<tr valign='top'><td><img src='../photos/$img' style='max-width:100%;max-height:120px'></td>
			<td><h4 style='color:#008080'>$prod @Ksh $cost</h4><p style='padding:6px 0px'>$desc</p><br>
			<p style='text-align:right'><button class='btn'style='background:#DB7093'onclick=\"delprod('$id')\">Delete</button></p></td></tr>";
		}
		
		echo "<div style='max-width:600px;margin:0 auto'>
		<h3 style='color:blue'>Company Products</h3><br>
		<form method='post' id='pfom' onsubmit='saveprod(event)'>
			<table cellpadding='10'>
			<tr><td>Product Name<br><input type='text' name='prd' required></td>
			<td>Product Photo<br><input type='file' name='foto' id='foto' accept='image/*' required></td></tr>
			<tr><td>Product Details<br><input type='text' name='pdet'required></td><td>Cost<br><input type='number' name='pcost' required></td></tr>
			<tr><td colspan='2'><button style='float:right' class='btn'>Save</button></td></tr>
			</table>
		</form><br><br>
		
		<table cellpadding='10'>$data</table>
		</div>";
	}
	
	//pickup stations
	if(isset($_GET['pickups'])){
		$data=""; $no=0;
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr"); $opts="";
		foreach($cos as $pl){
			$opts.="<option value='$pl'>$pl</option>";
		}
		$sql=mysqli_query($con,"SELECT *FROM `stations` ORDER BY `station` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$stn=prepare(ucwords($row['station'])); $county=prepare(ucwords($row['county'])); $no+=1; $id=$row['id'];
			$data.="<tr><td>$no</td><td>$stn</td><td>$county</td><td style='width:80px'><span class='lnk'style='color:#ff4500' 
			onclick=\"delstation('$id')\">Delete</span></td></tr>";
		}
		
		echo "<div style='max-width:550px;margin:0 auto;'><h3 style='color:blue;'>Pickup Stations</h3><br>
			<form method='post' id='pfom' onsubmit='savestation(event)'>
			<table cellpadding='10'>
			<tr><td>Station<br><input type='text' style='width:200px' name='stn' required></td>
			<td>County<br><select name='ploc' style='width:180px'>$opts</select></td><td><br><button class='btn'>Save</button></td></tr>
			</table></form><br>
			<table cellpadding='10' style='border:1px solid #dcdcdc;width:100%;border-collapse:collapse' border='1'>$data</table>
		</div>";
	}
	
	//produce
	if(isset($_GET['produce'])){
		$data="";
		$sq=mysqli_query($con,"SELECT DISTINCT `station` FROM `supplies` WHERE `status`='0' ORDER BY `id` DESC");
		while($ro=mysqli_fetch_assoc($sq)){
			$stn=$ro['station']; $dat="";
			$sql=mysqli_query($con,"SELECT DISTINCT `farmer` FROM `supplies` WHERE `station`='$stn' AND `status`='0'");
			while($rw=mysqli_fetch_assoc($sql)){
				$fid=$rw['farmer']; $tr=""; $no=2; $totd=0; $totp=0;
				$sq=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
				$name=prepare(ucwords(mysqli_fetch_assoc($sq)['name']));
				$qry=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$fid' AND `status`='0'");
				while($row=mysqli_fetch_assoc($qry)){
					$prod=prepare(ucwords($row['produce'])); $qnty=$row['quantity']; $rate=$row['rate']; 
					$tot=$row['total']; $st=$row['status']; $id=$row['id'];
					$cond=($st==0) ? "Supply unverified<br><span class='lnk'onclick=\"verifysup('$id')\">Verify</span>":"Verified"; 
					$tm=$row['time']; $day=date("d-m-Y, h:i a",$tm); $no+=1; $totp+=$tot;
					$tr.="<tr><td>$day</td><td>$prod</td><td>$qnty x ".fnum($rate)."</td><td>$cond</td><td>".fnum($tot)."</td></tr>";
				}
				
				$tr.="<tr valign='top' style='font-weight:bold;text-align:right'><td colspan='4'>Totals</td><td>".fnum($totp)."</td></tr>";
				$dat.="<tr valign='top'><td rowspan='$no'>$name</td>$tr</tr>";
			}
			$data.="<table cellpadding='10'style='border:1px solid #ccc;width:100%;border-collapse:collapse' border='1'>
			<caption style='text-align:left;padding:10px 0px'><h3>".prepare(ucwords($stn))." pickup station</h3></caption>
			<tr style='font-weight:bold'><td>Farmer</td><td>Date</td><td>Supply</td><td>Quantity</td><td>Status</td>
			<td>Total</td></tr>$dat</table><br>";
		}
		
		echo "<h3 style='color:blue'>Collect Farm products</h3><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:70px;'>No supplies made</p>":$data;
		
	}
	
	//add product demand
	if(isset($_GET['addproduce'])){
		?>
		<div style="max-width:250px;margin:0 auto">
		<h3 style="color:blue">Add product Demand</h3><br>
		<form method="post" id="pfom" onsubmit="savedemand(event)">
		<p>Produce Name<br><input type="text" name="prodn" required></p><br>
		<p>Measure<br><input type="text" name="ms" required></p><br>
		<p>Price rate<br><input type="number" name="price" required></p><br>
		<p>Quantity<br><input type="number" name="qnty" required></p><br>
		<p style="text-align:right"><button class="btn">Save</button></p></br>
		</form>
		</div><br>
		<?php
	}
	
	//demands
	if(isset($_GET['demand'])){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `demand`");
		while($row=mysqli_fetch_assoc($sql)){
			$prod=prepare(ucwords($row['product'])); $ms=$row['measure']; $rate=$row['rate']; 
			$mxs=$row['maxsupply']; $st=$row['status']; $id=$row['id'];
			$cond=($st==0) ? "<p>Open<br><span class='lnk' onclick=\"demandopr('1','$id')\">Close</span></p>":
			"<p>Closed<br><span class='lnk' onclick=\"demandopr('0','$id')\">Open</span></p>";
			$data.="<tr><td>$prod</td><td>$ms</td><td>$rate <span class='lnk'onclick=\"changedemand('rate','$id')\">Change</span></td>
			<td>$mxs <span class='lnk' onclick=\"changedemand('maxsupply','$id')\">Change</span></td><td>$cond</td></tr>";
		}
		
		echo "<div style='max-width:600px;margin:0 auto'>
		<h3 style='color:blue'>TSPF Produce demands from Farmers</h3><br>
		<table cellpadding='3'style='border:1px solid #ccc;float:right;border-collapse:collapse;text-align:center;width:100%'border='1'>
		<caption><button class='btn'style='float:right;padding:4px'onclick=\"loadpage('addproduce')\"><i class='fa fa-plus'></i> produce</button></caption>
		<tr style='font-weight:bold'><td>Produce</td><td>Measure</td><td>Price rate</td><td>Quantity demanded</td><td>Status</td></tr>$data
		</div>";
	}
	
	//pay farmers
	if(isset($_GET['payfarmer'])){
		$tp=explode(":",trim($_GET['payfarmer']));
		$type=$tp[0]; $amnt=fnum($tp[1]);
		
		echo "<div style='max-width:250px;margin:0 auto'>
		<h3 style='color:blue'>Pay Ksh $amnt</h3><br>
		<form method='post' id='payfom' onsubmit=\"savepay(event,'$tp[1]','$tp[0]')\">
		<input type='hidden' name='ptp' value='$type'>
		<p>Payment Method<br><select name='pay' id='pay'><option value='Cash'>Cash</option><option value='MPESA'>MPESA</option></select></p><br>
		<p style='text-align:right'><button class='btn'>Confirm</button></p><br>
		</form>
		</div>";
	}
	
	//payments
	if(isset($_GET['payments'])){
		$data=""; $totald=$totpay=0;
		$sq=mysqli_query($con,"SELECT DISTINCT `station` FROM `supplies` WHERE `status`='0' ORDER BY `id` DESC");
		while($ro=mysqli_fetch_assoc($sq)){
			$stn=$ro['station']; $dat=""; $spay=0; $ids="";
			$sql=mysqli_query($con,"SELECT DISTINCT `farmer` FROM `supplies` WHERE `station`='$stn' AND `paid`='0'");
			while($rw=mysqli_fetch_assoc($sql)){
				$fid=$rw['farmer']; $tr=""; $no=2; $totd=0; $totp=0; $ids.="$fid,";
				$sqt=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
				$name=prepare(ucwords(mysqli_fetch_assoc($sqt)['name']));
				$sqr=mysqli_query($con,"SELECT *FROM `sales` WHERE `farmer`='$fid' AND `paid`='0'");
				while($gr=mysqli_fetch_assoc($sqr)){$totd+=$gr['cost'];}
				$qry=mysqli_query($con,"SELECT *FROM `supplies` WHERE `farmer`='$fid' AND `paid`='0'");
				while($row=mysqli_fetch_assoc($qry)){
					$prod=prepare(ucwords($row['produce'])); $qnty=$row['quantity']; $rate=$row['rate']; $tot=$row['total']; $st=$row['status'];
					$cond=($st==0) ? "Supply unverified":"Verified"; $tm=$row['time']; $day=date("d-m-Y, h:i a",$tm); $no+=1; $totp+=$tot;
					$tr.="<tr><td>$day</td><td>$prod</td><td>$qnty x ".fnum($rate)."</td><td>$cond</td><td>".fnum($tot)."</td></tr>";
				}
				$bal=$totp-$totd; $totald+=$totd; $totpay+=$totp; $spay+=$bal;
				$tr.="<tr valign='top' style='font-weight:bold;text-align:right'><td colspan='4'>Totals<br>Farm Inputs Debt<br>Due Payable</td>
				<td>".fnum($totp)."<br>".fnum($totd)."<br>".fnum($bal)."<br><span class='lnk'onclick=\"loadpage('payfarmer=$fid:$bal')\">Disburse</span></td></tr>";
				$dat.="<tr valign='top'><td rowspan='$no'>$name</td>$tr</tr>";
			}
			if($dat!=""){
			$ids=rtrim($ids,",");
			$data.="<table cellpadding='10'style='border:1px solid #ccc;width:100%;border-collapse:collapse' border='1'>
			<caption style='text-align:left;padding:10px 0px'><h4>".prepare(ucwords($stn))." pickup station <span style='float:right'>Total Ksh ".fnum($spay)."
			<button class='btn' onclick=\"loadpage('payfarmer=$ids:$spay')\">Pay All</button></span></h4></caption>
			<tr style='font-weight:bold'><td>Farmer</td><td>Date</td><td>Supply</td><td>Quantity</td><td>Status</td>
			<td>Total</td></tr>$dat</table><br>";
			}
			
		}
		$bal=$totpay-$totald;
		
		echo "<h3 style='color:blue'>Farmer supplies Payments </h3>";
		if($data!=""){
			echo "<table cellpadding='3'style='border:1px solid #ccc;float:right;border-collapse:collapse;text-align:center'border='1'>
			<tr><td>Farmers Debt<br><b>".fnum($totald)."</b></td><td>Supplies<br><b>".fnum($totpay)."</b></td><td>Disbursement<br><b>".fnum($bal)."</b></td>
			<td><span class='lnk'onclick=\"loadpage('payfarmer=all:$bal')\">Disburse All</span></td></tr></table>";
		}
		echo ($data=="") ? "<p style='color:grey;line-height:70px;'>All farmers are paid</p>":$data;
		
		$data="";
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
		<caption style='text-align:left;padding:20px 0px'><h3 style='color:blue;width:100%'>Payment Histoty </h3></caption>
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
			<p style='padding:10px 0px;text-align:right;color:grey;'><i>$post</i></p>
			<p style='text-align:right'><button class='btn'style='background:#DB7093'onclick=\"delinput('$id')\"><i class='fa fa-times'></i> Delete</button></p></td></tr>";
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
		<button class='btn'style='float:right' onclick=loadpage('uploadinput')>Upload</button></h3><br>";
		echo ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Farm Inputs found for farmers</p>":
		"<table cellpadding='10'>$data</table>";
		echo "<br><br><h3 style='color:blue'>Purchased Inputs</h3><br><table cellpadding='10'>$tr</table><br></div>";
	}
	
	//upload inputs
	if(isset($_GET['uploadinput'])){
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr"); $opts="";
		foreach($cos as $pl){
			$opts.="<option value='$pl'>$pl</option>";
		}
		?>
		<div style="max-width:550px;margin:0 auto;">
		<h3 style="color:blue">Upload Farm Inputs</h3><br>
		<form method="post" id="upfom" onsubmit="saveinput(event)">
		<table cellpadding="10">
		<tr><td>Name of Input<br><input type="text" name="pname"required autofocus></td><td>Photo<br><input type="file" name="pic" id="pic" accept="image/*"required></td></tr>
		<tr valign="top"><td>Usage Instructions<br><textarea name="det" id="nmsg" style="height:70px"required></textarea></td><td>Cost<br><input type="number" name="icost"required></td></tr>
		<tr><td>County to be used<br><select name="cnty"><?php echo $opts;?></select></td><td><br><button class="btn"style="float:right">Upload</button></td></tr>
		</table></form>
		</div>
		<?php
	}
	
	//farmers
	if(isset($_GET['farmers'])){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `farmers` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=ucwords(prepare($row['name'])); $fon=$row['phone']; $idno=$row['idno']; $loc=$row['location']; $st=$row['status'];
			$day=date("d-m-Y, h:i a",$row['time']);
			if($st==0){
				$cond="<p style='color:grey'><i class='fa fa-clock-o'></i> Unapproved</p>
				<p><span class='lnk'style='color:#ff4500' onclick=\"accstatus('0','$idno')\">Decline</span> &nbsp; 
				<span class='lnk' style='color:blue'onclick=\"accstatus('1','$idno')\">Approve</span></p>";
			}
			else{
				$cond="<p style='color:green'><i class='fa fa-check'></i> Approved</p>";
			}
			$data.="<tr valign='top'><td>$name</td><td>0$fon</td><td>$idno</td><td>$loc</td><td>$cond</td><td>$day</td></tr>";
		}
		echo "<h3 style='color:blue'>TSPF Farmers</h3><br>";
		echo ($data=="") ? "<p style='color:grey;line-height:100px'>No record found</p>":"<table cellpadding='10'style='border:1px solid #ccc;
		border-collapse:collapse;width:100%' border='1'><tr style='font-weight:bold'><td>Name</td><td>Phone</td><td>ID No</td><td>County</td>
		<td>Status</td><td>Registration</td></tr>$data";
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
		$sql=mysqli_query($con,"SELECT *FROM `admin` WHERE `id`='$sid'");
		while($row=mysqli_fetch_assoc($sql)){$name=$row['username'];}
		
		$cos=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr"); $opts="";
		foreach($cos as $pl){
			$opts.="<option value='$pl'>$pl</option>";
		}
		?>
		<div style="max-width:400px;margin:0 auto">
		<h3 style="color:blue">Post News</h3><br>
		<form method="post" id="nfom" onsubmit="savenews(event)">
		<input type="hidden" name="src" value="admin <?php echo $name;?>">
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
			$sql=mysqli_query($con,"SELECT *FROM `admin` WHERE `id`='$uid'");
			while($row=mysqli_fetch_array($sql)){
				$adm=ucwords(strip_tags(stripslashes($row['username'])));
				$pass=strip_tags(stripslashes($row['password']));
			}
			?>
			<div style="padding:10px;width:260px;margin:20px;">
			<form method="post" id="ufom" onsubmit="saveacc(event)">
			<input type="hidden" name="sid" value="<?php echo $uid;?>">
			<p style="font-size:22px;font-family:rockwell"><?php echo $adm; ?> account</p><br>
			<p>Username<br><input type="text" name="admin" value="<?php echo $adm; ?>" required></p><br>
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