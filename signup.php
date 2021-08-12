	<?php
		
		$opts="";
		$subs=array("Narok North","Narok East","Narok South","Narok West","Kilgoris","Emurua Dikirr");
		foreach($subs as $sub){
			$opts.="<option value='$sub'>$sub</option>";
		}
	
	?>
	
	<!DOCTYPE html>
	<html>
	<title>Farmer registration</title>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, height=device-height,  initial-scale=1.0, user-scalable=no, user-scalable=0"/>
	<link rel="shortcut icon" href="images/favi.ico">
	<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
	<script src="js/jquery.js"></script>
	<style type="text/css">
	*{margin:0px;}
	body{
		background:#ffdead;background:linear-gradient(to right,#f8f8f8,#f8f0f0,#f0f8f8);font-family:cambria;
	}
	::placeholder{color:#008080;}
	.main{
		min-height:400px;margin:0 auto;background:#fff;width:96%;max-width:400px;margin-top:20px;box-shadow:0px 3px 5px rgba(0,0,0,0.3);
	}
	input[type=text],[type=password],[type=number],select{
		padding:5px;background:transparent;width:96%;border:0px;outline:none;border-bottom:1.5px solid #2f4f4f;font-family:arial;
		color:#008080;font-size:17px;font-family:FontAwesome;
	}
	
	.btn{
		padding:10px;border:0px;width:47%;background:#2E8B57;border:1px solid #2f4f4f;cursor:pointer;color:#fff;font-weight:bold;outline:none;
		text-shadow:0px 1px 1px #000;
	}
	.err{
		padding:7px;border:1px solid #FA8072;color:#ff4500;background:#FFE4C4;
	}
	.succ{
		padding:7px;border:1px solid green;color:#2E8B57;background:#E0FFFF;
	}
	.prog{
		width:90%;margin:0 auto;position:fixed;top:30%;left:5%;right:5%;height:100px;background:#fff;z-index:9;max-width:300px;border-radius:5px;
		overflow:auto;display:none;
	}
	.wrap{background:rgba(0,0,0,0.8);position:fixed;height:100%;width:100%;z-index:8;top:0;display:none;}
	a{text-decoration:none;}
	</style>
	</head>
	<body>
		<div class="wrap"></div><div class="prog"></div>
		<div class="main">
		<center><img src="images/logo.png"style="margin-top:10px;height:80px;"></center>
		<p style="font-family:rockwell;color:#191970;text-align:center;font-size:18px">TSPF Farmer Signup</p><br>
			<div style="padding:20px;font-family:tahoma;color:#191970">
			<form method="post" id="fom" onsubmit="save(event)">
			<p><input type="text"name="fname"placeholder="&#xf007 Full name"autocomplete="off"maxlength="20"autofocus required></p><br>
			<p style="margin-top:10px"><input type="number"name="fon"placeholder="&#xf095 Phone number"autocomplete="off"required></p><br>
			<p style="margin-top:10px"><input type="number"name="idno"placeholder="&#xf022 Id Number"autocomplete="off"required></p><br>
			<p style="margin-top:10px"><input type="password"name="pass"placeholder="&#xf084 Password"autocomplete="off"required></p><br>
			<p style="margin-top:10px"><select name="loc"><?php echo $opts; ?><select></p><br><br>
			<p><button class="btn"type="submit">Create account</button> <a href="login.php"style="float:right">Account Login</a></p>
			</form>
			</div>
		</div>

	</body>
	</html>
	
	<script>
	
	function save(e){
		e.preventDefault();
		var data=new FormData(_("fom"));
		progressd("Processing...please wait");
		var x=new XMLHttpRequest();
		x.onreadystatechange=function(){
			if(x.status==200 && x.readyState==4){
				if(x.responseText.trim()=="success"){
					progressd("Creation successful. Redirecting...");
					setTimeout(function(){window.location.replace("login.php");},1000);
				}
				else{
					progressd(""); setTimeout(function(){alert(x.responseText.trim()); },800);
				}
			}
		}
		x.open("post","accountsaver.php",true);
		x.send(data);
	}
	
	function progressd(t){
		if(t!=""){
			$(".wrap").fadeIn(); $(".prog").fadeIn();
			$(".prog").html("<p style='color:blue;line-height:100px;text-align:center'>"+t+"</p>");
		}else{
			$(".wrap").fadeOut(); $(".prog").fadeOut(); $(".prog").html("");
		}
	}
	
	function _(el){
		return document.getElementById(el);
	}
	</script>