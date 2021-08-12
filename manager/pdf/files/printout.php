<?php
	include "../../dbcon.php";
	
	require_once __DIR__ . '/../vendor/autoload.php';
	$mpdf = new mPDF('c','A4');
	$mpdf->SetDisplayMode('fullwidth');
	$mpdf->mirrorMargins = 1;
	$mpdf->defaultPageNumStyle = '1';
	$mpdf->setHeader();
	$mpdf->AddPage('P');
	$mpdf->SetAuthor("Allan Libese");
	$mpdf->SetCreator("Libsdigitech");
	$tp=trim($_GET['tp']);
	$title=($tp=="inputs") ? "Farm Inputs Report":"$tp Report";
	$mpdf->setTitle($title);
	 
	$css="
		.tbl{width:100%;font-family:sitka text;border-collapse:collapse;}
		.tbl tr:nth-child(odd){background:#f0f0f0;}
		.tbl tr:nth-child(even){background:#F5F5DC;}
		";
		
	$mpdf->WriteHTML($css,1);
	
	$text="
		<p style='text-align:center'><img src='../../../images/logo.png' height='70'></p>
		<h1 style=\"font-family:rockwell;color:#2E8B57;text-align:center\">Transmara Sugar Processing Factory</h1>
		<h2 style='text-align:center;color:blue'>".ucwords($title)."</h2>
	";
	$mpdf->setFooter('<p style="text-align:center"> KFPC - '.ucwords($title).': Page {PAGENO}</p>');
	
	if($tp=="agronomists"){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `agronomists` ORDER BY `location` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare(ucwords($row['name'])); $pic=$row['photo']; $fon=$row['phone']; $loc=$row['location'];
			$img=($pic=="none") ? "../../../images/user.png":"../../../photos/$pic"; $id=$row['id'];
			$data.="<tr valign='top'><td><img src='$img' height='60'><br>$name</td><td>0$fon</td><td>$loc</td></tr>";
		}
		
		$text.= "<table cellpadding='5' style='width:100%;border:1px solid #ccc;border-collapse:collapse;text-align:center' border='1'>
		<tr><td><b>Agronomist</b></td><td><b>Phone</b></td><td><b>Location</b></td></tr>$data</table><br>";
	}
	
	if($tp=="sales"){
		$data="";
		$sql=mysqli_query($con,"SELECT DISTINCT `day` FROM `purchases` ORDER BY `time` DESC");
		while($rw=mysqli_fetch_assoc($sql)){
			$dy=$rw['day']; $tr="";
			$qry=mysqli_query($con,"SELECT *FROM `purchases` WHERE `day`='$dy'");
			while($row=mysqli_fetch_assoc($qry)){
				$pic=$row['photo']; $item=ucfirst(prepare($row['item'])); $desc=nl2br(html_entity_decode(prepare($row['details'])));
				$cost=fnum($row['cost']); $fid=$row['farmer']; $day=date("h:i a",$row['time']); $pd=$row['paid'];
				$paid=($pd!=0) ? "Paid on ".date("d-m-Y",$pd):"Unpaid";
				$sq=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$fid'");
				$name=prepare(ucwords(mysqli_fetch_assoc($sq)['name']));
				
				$tr.="<tr><td><img src='../../../photos/$pic' height='100'></td><td><h4>$item</h4><p>$desc<p><br><p><i>Ksh $cost</i></p></td>
				<td><h4>$name</h4><p style='color:grey;padding-top:10px'>$day<br><i>$paid</i></p></td></tr>";
			}
			
			$data.="<h3 style='color:blue;text-align:center'>$dy</h3><table cellpadding='10' style='width:100%' class='tbl'>$tr</table><br>";
		}
		
		$text.=$data;
	}
	
	if($tp=="produce"){
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
				$cond=($st==0) ? "<p style='color:#2f4f4f'>Unverified</p>":"<p style='color:green'>Verified</p>"; 
				$tm=$row['time']; $day=date("d-m-Y,H:i",$tm); $no+=1; $totp+=$tot;
				$tr.="<tr><td>$day</td><td>$prod</td><td>$qnty x ".fnum($rate)."</td><td>$cond</td><td>".fnum($tot)."</td></tr>";
			}
			
			$tr.="<tr style='background:#fff;text-align:right'><td colspan='4'>Totals</td><td>".fnum($totp)."</td></tr>";
			$data.="<tr style='background:#fff'><td rowspan='$no'>$name</td>$tr</tr>";
		}
		$text.= ($data=="") ? "<p style='color:grey;line-height:70px;'>No supplies made</p>":"<table cellpadding='7' class='tbl' style='border:1px solid #ccc;' border='1'>
		<tr style='background:#fff'><td><b>Farmer</b></td><td><b>Date</b></td><td><b>Supply</b></td><td><b>Quantity</b></td><td><b>Status</b></td>
		<td><b>Total</b></td></tr>$data</table>";
	}
	
	if($tp=="inputs"){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `inputs` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=prepare($row['name']); $det=nl2br(prepare($row['details'])); $pic=$row['photo']; $id=$row['id'];
			$cost=fnum($row['cost']); $loc=prepare($row['county']); $post=date("M d, h:i a",$row['time']);
			$data.="<tr valign='top'><td><img src='../../../photos/$pic' style='max-width:100%;max-height:120px'></td><td><h4 style='color:#008080'>$name @Ksh $cost</h4>
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
			$tr.="<tr valign='top'><td><img src='../../../photos/$pic'height='100px'></td><td><h4>$fname</h4><p>$name</p><p style='padding:6px 0px'>($no) Ksh $cost</p>
			<p style='font-size:14px;color:#2f4f4f'>$pd</p><p style='color:blue;text-align:right'><i>$dy</i></p></td></tr>";
		}
		
		$text.="<div style='border-top:1px solid grey'><h3 style='color:blue'>Farm Inputs</h3>";
		$text.= ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Farm Inputs found for farmers</p>":
		"<table cellpadding='10'>$data</table>";
		$text.= "<br><br><h3 style='color:blue'>Purchased Inputs</h3><br><table cellpadding='10'>$tr</table><br></div>";
	}
	
	if($tp=="farmers"){
		$data="";
		$sql=mysqli_query($con,"SELECT *FROM `farmers` ORDER BY `time` DESC");
		while($row=mysqli_fetch_assoc($sql)){
			$name=ucwords(prepare($row['name'])); $fon=$row['phone']; $idno=$row['idno']; $loc=$row['location']; $st=$row['status'];
			$day=date("d-m-Y, h:i a",$row['time']);
			$cond=($st==0) ? "<p style='color:grey'>Unapproved</p>":"<p style='color:green'>Approved</p>";
			$data.="<tr><td>$name</td><td>0$fon</td><td>$idno</td><td>$loc</td><td>$cond</td><td>$day</td></tr>";
		}
		$text.= "<table style='border:1px solid #ccc;width:100%' cellpadding='10' class='tbl' border='1'>
		<tr style='background:#fff'><td><b>Name</b></td><td><b>Phone</b></td><td><b>ID No</b></td><td><b>County</b></td><td><b>Status</b></td>
		<td><b>Registration</b></td></tr>$data</table>";
	}
	
	if($tp=="payments"){
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
			$data.="<tr style='background:#fff'><td rowspan='$no'>$name</td>$tr</tr>";
		}
		
		$text.= "<table cellpadding='5' class='tbl' style='border:1px solid #ccc;width:100%;' border='1'>
		<tr style='background:#fff'><td><b>Farmer</b></td><td><b>Date</b></td><td><b>Payment</b></td><td><b>Transaction</b></td>
		<td><b>Details</b></td></tr>$data</table><br>";
	}
	
	$mpdf->WriteHTML(html_entity_decode($text));
	$mpdf->Output('Report.pdf','I');
	exit;

?>