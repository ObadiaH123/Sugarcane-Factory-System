<?php
	session_start();
	ob_clean();
	include "dbcon.php";
	
	if(isset($_SESSION['agron'])){
		$sid=$_SESSION['agron'];
		$sql=mysqli_query($con,"SELECT *FROM `agronomists` WHERE `id`='$sid'");
		$row=mysqli_fetch_assoc($sql); $img=$row['photo'];
		$usern=strip_tags(html_entity_decode(stripslashes($row['name'])));
		$pic=($img=="none") ? "images/user.png":"photos/$img";
	
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
		button:hover{box-shadow:0px 2px 3px rgba(0,0,0,0.3);}
		.main{width:100%;max-width:600px;margin:0 auto;height:100%;box-shadow:inset 0px 2px 3px #ccc;}
		button:hover{box-shadow:0px 2px 3px rgba(0,0,0,0.3);}
		nav{width:98%;height:15%;z-index:2;background:#fff;margin:0 auto;background:#fff;min-height:80px;box-shadow:0px 1px 0px #f0f0f0;}
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
		.udiv{height:70px;padding:5px;max-width:300px;border:1px solid #fff;margin:0 auto}
		.udiv:hover{background:#f0f0f0;cursor:pointer}
		.pic{width:65px;height:65px;border-radius:50px;background:#f0f0f0;float:left;background-image:url("<?php echo $pic;?>");background-size:cover}
		.tbl{width:100%;max-width:500px;margin:0 auto;}
		.tbl td{background:#4682b4;text-align:center;color:#fff;text-shadow:0px 1px 1px #000;cursor:pointer;width:25%;}
		.tbl td:hover{background:#2E8B57;}
		#nmsg{width:100%;padding:10px;font-size:16px;color:#191970;font-family:cambria;resize:none;height:100px;}
		#nmsg:focus{outline:1px solid #008fff;}
		</style>
	</head>
	<body>
	
	<div id="progdv">Loading...</div>
	<div id="toast"><div id="notif"></div></div>
	<div id="progr"><div id="progt"></div></div>
	
	<div class="main">
		<nav>
		<div class='pic'style="margin:10px 20px;"></div>
		<h4 style='padding:15px 10px 10px 0px'>Kalamba Fruit Company</h4>
		<h4 style='color:#008080'><?php echo ucwords($usern); ?> <button class='btn'style="float:right;margin-right:10px"onclick="window.location='logout.php?sess=agron'">
		<i class="fa fa-power-off"></i> Logout</button></h4>
		</nav>
		<div style="height:85%;overflow:auto;width:100%;margin:0 auto">
			<div style="padding:20px">
			<table cellpadding="5" cellspacing="5" class="tbl">
			<tr><td onclick="window.location=window.location"><i class="fa fa-home"style="font-size:23px"></i><br>Home</td>
			<td onclick="loadpage('agronchats')"><i class="fa fa-comments-o"style="font-size:23px"></i><span id="notar"></span><br>Chats</td>
			<td onclick="loadpage('news')"><i class="fa fa-bookmark-o"style="font-size:23px"></i><br>News</td>
			<td onclick="loadpage('agacc')"><i class="fa fa-user-o"style="font-size:23px"></i><br>Account</td></tr>
			</table><br>
			<div class="mainactivity">
			<?php
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
				
			?>
			</div>
			</div><br>
		</div>
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
	
	function changeprof(){
		var img=_("logo").files[0];
		if(img!=null){
			if(confirm("Replace current Photo with selected one")){
				progressd("Uploading...please wait");
				var data=new FormData(_("ufom"));
				data.append("file",img);
				var x=new XMLHttpRequest();
				x.onreadystatechange=function(){
					if(x.status==200 && x.readyState==4){
						progressd("");
						if(x.responseText.trim()=="success"){
							loadpage("agacc");
						}
						else{
							alert(x.responseText);
						}
					}
				}
				x.open("post","operations.php",true);
				x.send(data);
			}
		}
	}
	
	function gotochat(sen,u){
		window.open("chat.php?sen="+sen+"&rec="+u+"&tbl=farmers");
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
			method:"post",url:"datasaver.php",data:{getcom:"a-<?php echo $sid;?>"}
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