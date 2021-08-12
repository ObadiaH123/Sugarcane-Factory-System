<?php
	session_start();
	ob_clean();
	include "dbcon.php";
	
	if(isset($_SESSION['manager'])){
		$sid=$_SESSION['manager'];
		$sql=mysqli_query($con,"SELECT *FROM `manager` WHERE `id`='$sid'");
		$usern=prepare(ucwords(mysqli_fetch_array($sql)['username']));
	
	?>
	<html>
	<title>Manager portal</title>
	<meta charset="utf-8">
	<meta name="viewport"content="width=device-width,initial-scale=1.0,user-scalable=no,user-scalable=0">

	<head>
		<link rel="shortcut icon"href="../images/favi.ico">
		<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
		<script src="../js/jquery.js"></script>
		
		<style>
		*{margin:0px;}
		a{text-decoration:none;}
		body{background:#fff;max-width:1600px;margin:0 auto;font-family:cambria;}
		input[type=number],[type=password],[type=text],[type=email],[type=date],[type=time],select{
			padding:7px;width:250px;border:1px solid grey;color:#2f4f4f;font-size:17px;border-radius:3px;outline:none;font-family:arial;
		}
		button:hover{box-shadow:0px 2px 3px rgba(0,0,0,0.3);}
		aside{
			background:#fff;width:24%;float:left;height:100%;overflow:auto;background:#2E8B57;max-width:300px;color:#fff;box-shadow:inset 1px 1px 8px #00FF7F;
			text-shadow:0px 1px 1px #000;
		}
		section{
			background:#fff;width:76%;float:left;height:100%;overflow:auto;background:#fff;
		}
		@media screen and (max-width:850px){
			aside,section{display:none;}
		}
		.lst:hover{color:yellow;cursor:pointer;}
		aside li{margin-top:19px;font-family:sitka text;font-size:18px;color:#fff;list-style:none;}
		#progdv,#toast,#progr{
			max-width:300px;background:#000;position:fixed;z-index:99;top:40%;left:2%;right:2%;margin:0 auto;height:auto;padding:10px;color:#fff;text-align:center;
			border-radius:5px;display:none;font-family:berlin sans fb;
		}
		#progdv{width:100px;background:#DA70D6;top:37%;}
		#progr{background:#2F4F4F;top:35%;}
		#calin{
			width:160px;height:40px;background-color:#2E8B57;font-family:arial;cursor:pointer;line-height:40px;color:#fff;text-align:center;
			border-radius:5px;margin-top:2%;
		}
		#calin:hover{
			background-color:#191970;
		}
		
		.btn{
			cursor:pointer;color:#fff;padding:8px;border-radius:3px;background:#2E8B57;border:0px;outline:none;width:80px;text-shadow:0px 1px 1px #000;
		}
		.htbl td{height:150px;width:200px;border-radius:5px;font-family:lucida calligraphy;color:#fff;text-align:center;}
		.htbl td:hover{opacity:.6;cursor:pointer;}
		.tbl{width:100%;font-family:sitka text;border-collapse:collapse;}
		.tbl tr:nth-child(odd){background:#E6E6FA;}
		.tbl tr:nth-child(even){background:#F5F5DC;}
		.notbtn{height:25px;width:25px;border:1px solid #fff;background:#ff4500;color:#fff;outline:none;border-radius:50%;font-size:13px}
		.udiv{height:70px;padding:5px;width:300px;border:1px solid #fff;}
		.udiv:hover{background:#f0f0f0;cursor:pointer}
		#nmsg{width:100%;padding:10px;font-size:16px;color:#191970;font-family:cambria;resize:none;height:100px;}
		#nmsg:focus{outline:1px solid #008fff;}
		.bt{float:right;padding:8px;color:blue;border:1px solid #008080;margin-left:20px;cursor:pointer;}
		.lnk{color:blue;}
		.lnk:hover{text-decoration:underline;cursor:pointer}
		</style>

	</head>

	<body>
	
	<div id="progdv">Loading...</div>
	<div id="toast"><div id="notif"></div></div>
	<div id="progr"><div id="progt"></div></div>
	
		<aside>
		<div style="padding:20px">
		<h2 style="font-family:lucida calligraphy;font-size:20px;text-align:center;border-bottom:1px solid #fff;padding:10px 0px">
		Manager <?php echo $usern; ?></h2><br>
		<ul>
			<li><span class="lst" onclick="window.location=window.location"><i class="fa fa-dashboard"></i> Dashboard</span></li>
			<li><span class="lst" onclick="loadacc()"><i class="fa fa-address-book-o"></i> Account</span></li>
			<li><span class="lst" onclick="loadpage('farmers')"><i class="fa fa-users"></i> Farmers</span></li>
			<li><span class="lst" onclick="loadpage('sales')"><i class="fa fa-bar-chart"></i> Sales</span></li>
			<li><span class="lst" onclick="loadpage('inputs')"><i class="fa fa-balance-scale"></i> Farm Inputs</span></li>
			<li><span class="lst" onclick="loadpage('payments')"><i class="fa fa-money"></i> Payments</span></li>
			<li><span class="lst" onclick="loadpage('news')"><i class="fa fa-bookmark-o"></i> News</span></li>
			<li><span class="lst" onclick="loadpage('produce')"><i class="fa fa-lemon-o"></i> Produce</span></li>
			<li><span class="lst" onclick="loadpage('agronomist')"><i class="fa fa-user-md"></i> Agronomists</span></li>
		</ul>
		</div>
		</aside>
		
		<section>
			<div style="height:12%;width:100%;">
				<img src="../images/logo.png"style="height:88%;margin:5px 20px;float:left"><br>
				<h2 style="padding:10px;font-family:lucida calligraphy;font-size:19px;color:#2E8B57;">Transmara Sugar Processing Factory 
				<button style="float:right;background:#f08080"class="btn"onclick="window.location.replace('logout.php')"><i class="fa fa-power-off"></i> Logout</button></h2>
			</div>
			<div style="height:87%;overflow:auto;background:#fff;width:100%">
				<div class="mainactivity" style="padding:25px;">
				<h3 style="color:#191970;text-align:center">Welcome <?php echo $usern; ?></h3><br>
					<div style="max-width:650px;margin:0 auto"><br>
					<?php
					$data="";
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
					
					echo "<div style='max-width:600px;margin:0 auto'><h4 style='color:blue'>Farm Inputs 
					<button class='btn'style='float:right;background:#F08080;padding:5px'onclick=\"genpdf('inputs')\"><i class='fa fa-file-pdf-o'></i> PDF</button></h4><br>";
					echo ($data=="") ? "<p style='background:#f0f0f0;border:1px solid #ccc;padding:10px'>No Farm Inputs found for farmers</p>":
					"<table cellpadding='10'>$data</table>";
					echo "<br><br><h4 style='color:blue'>Purchased Inputs</h4><br><table cellpadding='10'>$tr</table><br></div>";
					?>
					
				</div>
			</div>
		</section>
	
	</body>
	</html>
	
	<?php
	}
	else{
		header("location:index.php");
	}
	ob_end_flush();
?>
	<script>
	
	//save agronomist
	function saveagron(e){
		e.preventDefault();
		progressd("Adding please wait...");
		var data=$("#afom").serialize();
		$.ajax({
			method:"POST",url:"operations.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("agronomist");}
		});
	}
	
	//save news
	function savenews(e){
		e.preventDefault();
		progressd("Posting News...");
		var data=$("#nfom").serialize();
		$.ajax({
			method:"POST",url:"operations.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("news");}
		});
	}

	//save user account
	function saveacc(e){
		e.preventDefault();
		progressd("Saving changes...");
		var data=$("#ufom").serialize();
		$.ajax({
			method:"POST",url:"operations.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
		});
	}
	
	function genpdf(rec){
		window.open("pdf/files/printout.php?tp="+rec);
	}
	
	//delete agronomist
	function delagron(id){
		if(confirm("Remove Agronomist from the system?")){
			progressd("Deleting...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{dagron:id}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage("agronomist");}
				else{toast(res);}
			});
		}
	}
	
	//delete news
	function delnews(t){
		if(confirm("Remove County News?")){
			progressd("Removing...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{dnews:t}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage("news");}
				else{toast(res);}
			});
		}
	}
	
	function toast(v){
		rest();
		$("#toast").fadeIn(); _("notif").innerHTML=v;
		tmo=setTimeout(function(){
			$("#toast").fadeOut();
		},5000);
	}
	
	var tmo;
	function rest(){
		clearTimeout(tmo);
	}
	
	function progressd(v){
		if(v !=""){
			$("#progr").fadeIn();
			_("progt").innerHTML=v;
		}
		else{
			$("#progr").fadeOut();
			_("progt").innerHTML="";
		}
	}
	
	function showps() {
		var x = document.getElementById("upass");
		if (x.type === "password") {
			_("sps").style.display="none";_("hdps").style.display="block";
			x.type = "text";
		} else {
			_("sps").style.display="block";_("hdps").style.display="none";
			x.type = "password";
		}
	}
	
	function loadpage(page){
		$("#progdv").fadeIn();
		if($(".stop").is(":visible")){sidebar("h");}
		$(".mainactivity").load("operations.php?"+page+"&sid=<?php echo $sid;?>",function(response,status,xhr){
			$("#progdv").fadeOut(); if(status=="error"){toast("Failed: Check Internet connection");}
		});
	}
	
	function loadacc(){
		loadpage("tp=myacc&vl=<?php echo $sid; ?>");
	}
	
	//validate number input
	function valid(id,v){
		var exp=/^[0-9.]+$/;
		if(! v.match(exp)){
			_(id).value="";
		}
	}
	
	function _(el){
		return document.getElementById(el);
	}
	</script>