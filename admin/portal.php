<?php
	session_start();
	ob_clean();
	include "dbcon.php";
	
	if(isset($_SESSION['admin'])){
		$sid=$_SESSION['admin'];
		$sql=mysqli_query($con,"SELECT *FROM `admin` WHERE `id`='$sid'");
		$usern=prepare(ucwords(mysqli_fetch_array($sql)['username']));
	
	?>
	<html>
	<title>Admin portal</title>
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
		Admin <?php echo $usern; ?></h2><br>
		<ul>
			<li><span class="lst" onclick="window.location=window.location"><i class="fa fa-dashboard"></i> Dashboard</span></li>
			<li><span class="lst" onclick="loadacc()"><i class="fa fa-address-book-o"></i> Account</span></li>
			<li><span class="lst" onclick="loadpage('farmers')"><i class="fa fa-users"></i> Farmers</span></li>
			<li><span class="lst" onclick="loadpage('pickups')"><i class="fa fa-map-marker"></i> Pickup Stations</span></li>
			<li><span class="lst" onclick="loadpage('sales')"><i class="fa fa-bar-chart"></i> Sales</span></li>
			<li><span class="lst" onclick="loadpage('inputs')"><i class="fa fa-balance-scale"></i> Farm Inputs</span></li>
			<li><span class="lst" onclick="loadpage('payments')"><i class="fa fa-money"></i> Payments</span></li>
			<li><span class="lst" onclick="loadpage('news')"><i class="fa fa-bookmark-o"></i> News</span></li>
			<li><span class="lst" onclick="loadpage('produce')"><i class="fa fa-lemon-o"></i> Produce</span></li>
			<li><span class="lst" onclick="loadpage('demand')"><i class="fa fa-retweet"></i> Demands</span></li>
			<li><span class="lst" onclick="loadpage('addprod')"><i class="fa fa-leaf"></i> Products</span></li>
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
				<h3 style="color:#191970">Welcome <?php echo $usern; ?></h3><br>
					<div style="max-width:650px;margin:0 auto">
					<?php
					$data="";
					$sql=mysqli_query($con,"SELECT *FROM `farmers` WHERE `status`='0' ORDER BY `time` DESC");
					while($row=mysqli_fetch_assoc($sql)){
						$name=ucwords(prepare($row['name'])); $fon=$row['phone']; $idno=$row['idno']; $loc=$row['location']; $st=$row['status'];
						$day=date("d-m-Y, h:i a",$row['time']);
						$cond="<p style='color:grey'><i class='fa fa-clock-o'></i> Unapproved</p>
						<p><span class='lnk'style='color:#ff4500' onclick=\"accstatus('0','$idno')\">Decline</span> &nbsp; 
						<span class='lnk' style='color:blue'onclick=\"accstatus('1','$idno')\">Approve</span></p>";
		
						$data.="<tr valign='top'><td>$name</td><td>0$fon</td><td>$idno</td><td>$loc</td><td>$cond</td><td>$day</td></tr>";
					}
					echo "<br><h4 style='color:blue'>TSPF Farmers Account Creation request</h4>";
					echo ($data=="") ? "<p style='color:grey;line-height:50px'>No requests</p>":"<table cellpadding='10'style='border:1px solid #ccc;
					border-collapse:collapse;width:100%' border='1'><tr style='font-weight:bold'><td>Name</td><td>Phone</td><td>ID No</td><td>County</td>
					<td>Status</td><td>Registration</td></tr>$data</table><br>";
					
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
					
					echo "<br>
					<h4 style='color:blue'>TSPF Produce demands from Farmers</h4>
					<table cellpadding='3'style='border:1px solid #ccc;float:right;border-collapse:collapse;text-align:center;width:100%'border='1'>
					<caption><button class='btn'style='float:right;padding:4px'onclick=\"loadpage('addproduce')\"><i class='fa fa-plus'></i> produce</button></caption>
					<tr style='font-weight:bold'><td>Produce</td><td>Measure</td><td>Price rate</td><td>Quantity demanded</td><td>Status</td></tr>$data</table><br>";
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
	
	//delete station
	function delstation(id){
		if(confirm("Remove Station from the system?")){
			progressd("Deleting...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{dstn:id}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage("pickups");}
				else{toast(res);}
			});
		}
	}
	
	//save station
	function savestation(e){
		e.preventDefault();
		progressd("Adding please wait...");
		var data=$("#pfom").serialize();
		$.ajax({
			method:"POST",url:"datasaver.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("pickups");}
		});
	}
	
	function demandopr(val,id){
		progressd("Processing please wait...");
		$.ajax({
			method:"POST",url:"datasaver.php",data:{dopt:val,did:id}
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("demand");}
		});
	}
	
	function changedemand(tp,id){
		var cond=(tp=="rate") ? "New price rate":"Maximum demand quantity";
		var nw=prompt("Enter "+cond).trim();
		if(nw!=""){
			progressd("Processing please wait...");
			$.ajax({
				method:"POST",url:"datasaver.php",data:{dtp:tp,did:id,dval:nw}
			}).done(function(res){
				progressd(""); toast(res);
				if(res.trim()=="success"){loadpage("demand");}
			});
		}
	}
	
	//verify supply
	function verifysup(id){
		if(confirm("Sure to verify the supply from farmer?")){
			$.ajax({
				method:"post",url:"datasaver.php",data:{vrid:id}
			}).done(function(res){
				progressd(""); toast(res);
				if(res.trim()=="success"){loadpage("produce");}
			});
		}
	}
	
	//approve/dissaprove account
	function accstatus(st,id){
		var cond=(st==0) ? "Sure to Decline account creation? This farmer will be removed from system.":"Activate farmer account? Wont be reversed.";
		if(confirm(cond+" Continue?")){
			$.ajax({
				method:"post",url:"datasaver.php",data:{acst:st,fid:id}
			}).done(function(res){
				progressd(""); toast(res);
				if(res.trim()=="success"){loadpage("farmers");}
			});
		}
	}
	
	//save demand
	function savedemand(e){
		e.preventDefault();
		progressd("Adding please wait...");
		var data=$("#pfom").serialize();
		$.ajax({
			method:"POST",url:"datasaver.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("demand");}
		});
	}
	
	//save news
	function savenews(e){
		e.preventDefault();
		progressd("Posting News...");
		var data=$("#nfom").serialize();
		$.ajax({
			method:"POST",url:"datasaver.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("news");}
		});
	}
	
	//save payment
	function savepay(e,amnt,tp){
		e.preventDefault();
		var data=$("#payfom").serialize(); var mode=_("pay").value; var len=tp.split(",").length;
		
		if(tp=="all"){
			if(mode=="MPESA"){
				toast("MPESA Payment API does not support paying multiple Farmers at the moment,simulate Cash Instead");
			}
			else{
				progressd("Saving payment...");
				$.ajax({
					method:"POST",url:"datasaver.php",data:data
				}).done(function(res){
					progressd(""); toast(res); loadpage("payments");
				});
			}
		}
		else{
			if(mode=="MPESA"){
				if(len>1){
					toast("MPESA Payment API does not support paying multiple Farmers at the moment,simulate Cash Instead");
				}
				else{
					if(confirm("Request MPESA payment of KES "+amnt)){
						progressd("Sending STK Push...");
						$.ajax({
							method:"POST",url:"../payment.php",data:{pay:amnt,uid:tp}
						}).done(function(res){
							progressd(""); 
							if(res.trim()=="success"){
								setTimeout(function(){
									var code=prompt("Request sent. Enter the MPESA Payment Code.","NK76G45W");
									if(code){
										progressd("Validating payment...");
										$.ajax({
											method:"POST",url:"datasaver.php?pcode="+code,data:data
										}).done(function(res){
											progressd(""); 
											if(res.trim()!="success"){
												setTimeout(function(){
													alert(res);
												},400);
											}
											else{toast(res); loadpage("payments");}
										});
									}
								},400);
							}
							else{alert(res);}
						});
					}
				}
			}
			else{
				progressd("Saving payment...");
				$.ajax({
					method:"POST",url:"datasaver.php",data:data
				}).done(function(res){
					progressd(""); toast(res); loadpage("payments");
				});
			}
		}
	}
	
	//save user account
	function saveacc(e){
		e.preventDefault();
		progressd("Saving changes...");
		var data=$("#ufom").serialize();
		$.ajax({
			method:"POST",url:"datasaver.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
		});
	}
	
	//delete input
	function delprod(id){
		if(confirm("Delete product?")){
			progressd("Removing...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{dprod:id}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage("addprod");}
				else{toast(res);}
			});
		}
	}
	
	//delete input
	function delinput(id){
		if(confirm("Delete farm Input?")){
			progressd("Removing...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{dinput:id}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage("inputs");}
				else{toast(res);}
			});
		}
	}
	
	//save product
	function saveprod(e){
		e.preventDefault();
		var img=_("foto").files[0];
		progressd("Uploading...please wait");
		var data=new FormData(_("pfom"));
		data.append("file",img);
		var x=new XMLHttpRequest();
		x.onreadystatechange=function(){
			if(x.status==200 && x.readyState==4){
				progressd("");
				if(x.responseText.trim()=="success"){
					loadpage("addprod");
				}
				else{
					alert(x.responseText);
				}
			}
		}
		x.open("post","datasaver.php",true);
		x.send(data);
	}
	
	//save inputs
	function saveinput(e){
		e.preventDefault();
		var img=_("pic").files[0];
		progressd("Uploading...please wait");
		var data=new FormData(_("upfom"));
		data.append("file",img);
		var x=new XMLHttpRequest();
		x.onreadystatechange=function(){
			if(x.status==200 && x.readyState==4){
				progressd("");
				if(x.responseText.trim()=="success"){
					loadpage("inputs");
				}
				else{
					alert(x.responseText);
				}
			}
		}
		x.open("post","datasaver.php",true);
		x.send(data);
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