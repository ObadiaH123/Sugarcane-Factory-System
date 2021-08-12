<?php
	session_start();
	ob_clean();
	include "dbcon.php";
	
	if(isset($_SESSION['farmer'])){
		$sid=$_SESSION['farmer'];
		$sql=mysqli_query($con,"SELECT *FROM `farmers` WHERE `id`='$sid'");
		$row=mysqli_fetch_assoc($sql);
		$usern=strip_tags(html_entity_decode(stripslashes(ucwords($row['name']))));
		$fon=$row['phone'];
	
	?>
	<html>
	<title>My Account</title>
	<meta charset="utf-8">
	<meta name="viewport"content="width=device-width,initial-scale=1.0,user-scalable=no,user-scalable=0">

	<head>
		<link rel="shortcut icon"href="images/favi.ico">
		<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
		<script src="js/jquery.js"></script>
		
		<style>
		*{margin:0px;}
		a{text-decoration:none;}
		body{background:#fff;max-width:1600px;margin:0 auto;font-family:cambria;}
		input[type=number],[type=password],[type=text],[type=email],[type=date],[type=time],select{
			padding:7px;width:250px;border:1px solid grey;color:#2f4f4f;font-size:17px;border-radius:3px;outline:none;font-family:arial;
		}
		.main{width:100%;max-width:1000px;margin:0 auto;height:100%;}
		button:hover{box-shadow:0px 2px 3px rgba(0,0,0,0.3);}
		@media screen and (min-width:701px){
			aside{width:25%;float:left;height:100%;background:#fff;overflow:auto}
			section{width:75%;float:left;height:100%;background:#fff;overflow:auto;}
			nav,.stop{display:none;}
			.ltop{display:block;}
		}
		@media screen and (max-width:700px){
			aside{display:none;width:0px;position:fixed;left:0;top:0;z-index:3;background:#fff;height:100%}
			section{width:100%;}
			nav{width:100%;height:100px;position:fixed;top:0;z-index:2;background:#fff;border-bottom:1px solid #ccc;}
			.ext{height:110px;width:100%;}
			.stop{display:block;height:50px}
			.ltop{display:none;}
		}
		.btn{background:#2E8B57;padding:7px;border:0px;border-radius:3px;color:#fff;outline:none;text-shadow:0px 1px 1px #000;cursor:pointer;min-width:80px;}
		ul li{list-style:none;color:#4682b4;}
		.lst:hover{color:blue;cursor:pointer}
		.notbtn{height:25px;width:25px;border:1px solid #fff;background:#ff4500;color:#fff;outline:none;border-radius:50%;font-size:13px}
		#progdv,#toast,#progr{
			max-width:300px;background:#000;position:fixed;z-index:99;top:40%;left:2%;right:2%;margin:0 auto;height:auto;padding:10px;color:#fff;text-align:center;
			border-radius:5px;display:none;font-family:berlin sans fb;
		}
		#progdv{width:100px;background:#DA70D6;top:37%;}
		#progr{background:#2F4F4F;top:35%;}
		.hd{padding:8px;background:#F0FFFF;color:#2E8B57;border:1px solid #8FBC8F;text-align:center}
		.udiv{height:70px;padding:5px;max-width:300px;border:1px solid #fff;}
		.udiv:hover{background:#f0f0f0;cursor:pointer}
		.pcard{width:100px;text-align:center;background:#F4A460;padding:10px;border-top-left-radius:25px;border-bottom-right-radius:25px;
		text-shadow:0px 1px 1px #000;color:#fff;}
		</style>
	</head>
	<body>
	
	<div id="progdv">Loading...</div>
	<div id="toast"><div id="notif"></div></div>
	<div id="progr"><div id="progt"></div></div>
	
	<div class="main">
		<nav>
		<i class="fa fa-bars" style="float:left;margin:40px 10px 0px 20px;color:#008080;font-size:25px" onclick="sidebar('show')"></i>
		<img src="images/logo.png" style="margin:10px 15px;height:80px;float:left">
		<h3 style="line-height:100px">Transmara SPF</h3>
		</nav>
		<aside>
			<div style="padding:10px;max-width:300px"><br>
			<div class="stop"><i class="fa fa-times" style="float:right;font-size:25px;color:brown"onclick="sidebar('hide')"></i><br></div>
			<center><div class="ltop"><img src="images/logo.png"height="100">
			<h4 style="color:#2E8B57">Transmara Sugar Processing TSPF</h4><br></div>
			<div style="padding:10px 5px;box-shadow:inset 0px 0px 4px #ccc;background:#F0FFFF"><h4 style="color:#4682b4;"><?php echo $usern;?></h4>
			<p style="text-align:right;margin:10px"><button class="btn"onclick="window.location.replace('logout.php?sess=farmer')">
			<i class="fa fa-power-off"></i> Logout</button></p>
			</div></center><br>
			<ul>
				<li><span class="lst"onclick="window.location=window.location"><i class="fa fa-home"></i> Home</span></li><br>
				<li><span class="lst"onclick="loadpage('myacc')"><i class="fa fa-user-o"></i> My account</span></li><br>
				<li><span class="lst"onclick="loadpage('chats')"><i class="fa fa-comments-o"></i> Chats</span><span id="notar"></span></li><br>
				<li><span class="lst"onclick="loadpage('payments')"><i class="fa fa-money"></i> Payments<span></li><br>
				<li><span class="lst"onclick="loadpage('produce')"><i class="fa fa-lemon-o"></i> Farm Produce</span></li><br>
				<li><span class="lst"onclick="loadpage('inputs')"><i class="fa fa-balance-scale"></i> Farm Inputs</span></li><br>
				<li><span class="lst"onclick="loadpage('products')"><i class="fa fa-leaf"></i> Products</span></li><br>
			</ul>
			</div>
		</aside>
		<section><div class="ext"></div>
			<div class="mainactivity" style="padding:20px">
			<div style="max-width:450px;margin:0 auto">
			<br><h3>Welcome <?php echo $usern; ?></h3><br>
			<h4 style='color:blue'>TSPF Product Demands</h4>
			<?php
				$data="";
				$sql=mysqli_query($con,"SELECT *FROM `demand`");
				while($row=mysqli_fetch_assoc($sql)){
					$prod=prepare(ucwords($row['product'])); $ms=prepare($row['measure']); $rate=fnum($row['rate']);
					$mxs=$row['maxsupply']; $st=$row['status'];
					$cond=($st==0) ? "Open":"Closed";
					$data.="<tr valign='top'><td><p>$prod</p><p style='color:#008080'>$ms @Ksh $rate</p></td><td>$mxs</td><td>$cond</td></tr>";
				}
				echo "<table cellpadding='10' style='width:100%;border:1px solid #ccc;border-collapse:collapse' border='1'>
				<tr style='font-weight:bold'><td>Product</td><td>Demand</td><td>Status</td></tr>$data</table>";
				echo "<br><h4 style='color:blue'>County News</h4><br>";
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
				
				echo ($res=="") ? "<p style='color:grey;line-height:80px'>No News</p>":"<table cellpadding='10'>$res</table>";
			?>
			</div>
			</div>
		</section>
	</div>
	</body>
	</html>
	
	<?php
	}
	else{
		header("location:login.php");
	}
	ob_end_flush();

	?>
	
	<script>
	
	function addcart(id,tp){
		var tm=_("temps").value.trim();
		var nw=(tm!="") ? tm+","+id:id;
		_("temps").value=nw; var no=nw.split(",").length;
		toast("Item added to Cart");
		var tot=(no==1) ? "1 Item":no+" Items";
		if(tp=="inputs"){
			_("cbtn").innerHTML="<p><button class='btn'style='float:right' onclick='buyinputs()'>Buy "+tot+"</button></p><br>";
		}
		else{
			_("cbtn").innerHTML="<p><button class='btn'style='float:right' onclick='buyprods()'>Buy "+tot+"</button></p><br>";
		}
	}
	
	function buyprods(){
		var tm=_("temps").value.trim();
		loadpage("buyprods="+tm);
	}
	
	function buyinputs(){
		var tm=_("temps").value.trim();
		loadpage("buyinputs="+tm);
	}
	
	function showprod(id){
		loadpage("produce="+id);
	}
	
	function gotochat(sen,u){
		window.open("chat.php?sen="+sen+"&rec="+u+"&tbl=agronomists");
	}
	
	function cancelsup(tm,p){
		if(confirm("Sure to Cancel the supplied Produce?")){
			progressd("Canceling...please wait");
			$.ajax({
				method:"post",url:"operations.php",data:{delsup:tm,pid:p}
			}).done(function(res){
				progressd("");
				if(res.trim()=="Deleted"){loadpage('produce');}
				else{toast(res);}
			});
		}
	}
	
	function sidebar(t){
		if(t=="show"){
			$("aside").show(); 
			$("aside").animate({
				width:"100%"
			},600);
		}else{
			$("aside").animate({
				width:"0px"
			});
			setTimeout(function(){$("aside").hide();},300);
		}
	}
	
	function payitems(ids,a,u,f){
		if(confirm("Request MPESA payment of KES "+a)){
			progressd("Sending STK Push...");
			$.ajax({
				method:"POST",url:"payment.php",data:{mpay:a,phone:"<?php echo $fon; ?>"}
			}).done(function(res){
				progressd(""); 
				if(res.trim()=="success"){
					setTimeout(function(){
						var code=prompt("Request sent to 0<?php echo $fon;?>. Enter the MPESA Payment Code.","NK76G45W");
						if(code){
							if(f=="saveitems"){saveitems(ids,u,code);}
							else{saveprods(ids,u,code);}
						}
					},400);
				}
				else{alert(res);}
			});
		}
	}
	
	//save purchased products
	function saveprods(ids,u,code){
		progressd("Validating...please Wait");
		$.ajax({
			method:"POST",url:"datasaver.php",data:{prids:ids,pfid:u,pcode:code}
		}).done(function(res){
			progressd("");
			if(res.trim()=="success"){loadpage("products");}
			else{
				setTimeout(function(){
					var cod=prompt(res+". Re-Enter the Code","NK76G45W");
					if(cod){
						saveprods(ids,u,cod);
					}
				},400);
			}
		});
	}
	
	//save items
	function saveitems(ids,u,code){
		progressd("Validating...please wait");
		$.ajax({
			method:"POST",url:"datasaver.php",data:{pids:ids,fid:u,pcode:code}
		}).done(function(res){
			progressd("");
			if(res.trim()=="success"){loadpage("inputs");}
			else{
				setTimeout(function(){
					var cod=prompt(res+". Re-Enter the Code","NK76G45W");
					if(cod){
						saveitems(ids,u,cod);
					}
				},400);
			}
		});
	}
	
	//save produce
	function saveproduce(e){
		e.preventDefault();
		progressd("Saving changes...");
		var data=$("#pfom").serialize();
		$.ajax({
			method:"POST",url:"datasaver.php",data:data
		}).done(function(res){
			progressd(""); toast(res);
			if(res.trim()=="success"){loadpage("produce");}
		});
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
	
	setInterval(checkchats,3000);
	
	function checkchats(){
		$.ajax({
			method:"post",url:"datasaver.php",data:{getcom:"f-<?php echo $sid;?>"}
		}).done(function(res){
			if(res.trim()>0){_("notar").innerHTML='<button class="notbtn">'+res.trim()+'</button>';}
			else{_("notar").innerHTML='';}
		});
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