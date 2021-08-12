	<?php
	include "dbcon.php";
	
	if(isset($_GET['sen'])){
		$data="";
		$ctb=trim($_GET['tbl']); 
		$uid=trim($_GET['sen']); $rid=trim($_GET['rec']); $sid=explode("-",$uid)[1];
		
		mysqli_query($con,"UPDATE `chats` SET `status`='1' WHERE `sender`='$uid' AND `receiver`='$rid' AND `status`='0'");
		$sql=mysqli_query($con,"SELECT *FROM `$ctb` WHERE `id`='$sid'");
		$name=prepare(ucwords(mysqli_fetch_array($sql)['name']));
	
	?>
	
	<html>
	<title>Chats</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" href="images/favi.ico">
	<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="js/jquery.js"></script>
	
	<style>
	*{margin:0px;}
	.main{
		max-width:500px;width:100%;margin:0 auto;background:#fff;height:83%;box-shadow:inset 0px 3px 4px rgba(0,0,0,0.3);overflow:auto;
	}
	nav{height:55px;position:fixed;top:0;left:0;right:0;max-width:500px;margin:0 auto;background:#6495ed;color:#fff;}
	#bicon{font-size:30px;margin-right:30px;margin-top:10px;float:left;margin-left:20px;text-shadow:0px 1px 1px #000;cursor:pointer;}
	.bott{height:100px;position:fixed;bottom:0;left:0;right:0;max-width:490px;margin:0 auto;background:#fff;}
	#mssg{
		padding:10px;font-family:arial;width:75%;height:80px;resize:none;outline:none;font-size:17px;float:left;border:0px;
	}
	.btn{background:#4682b4;color:#fff;outline:none;border:0px;padding:10px;border-radius:3px;text-shadow:0px 1px 1px #000;cursor:pointer;}
	.wrap{top:0;left:0;width:100%;position:fixed;z-index:9;background:rgba(0,0,0,0.6);height:100%;font-family:sitka text;display:none;}
	.wrapper{top:0;left:0;width:100%;position:fixed;z-index:19;background:rgba(0,0,0,0.6);height:100%;font-family:sitka text;display:none;}
	.prog{line-height:50px;background:#fff;border-radius:5px;top:15%;left:5%;right:5%;position:fixed;margin:0 auto;max-width:300px;}
	.card{padding:10px;font-size:16px;color:#191970;font-family:sitka text;clear:both;margin:10px 0px;border-radius:4px;max-width:80%;box-shadow:0px 1px 3px grey}
	.caption{
		width:94%;max-width:350px;margin:0 auto;position:fixed;left:1%;right:1%;top:5%;background:#fff;min-height:200px;border-radius:5px;z-index:20;
		display:none;overflow:auto;
	}
	#pbc{height:22px;width:100%;border-radius:10px;background:#F5F5DC;display:none;box-shadow:inset 0px 0px 4px grey;max-width:300px;}
	#progress{max-width:100%;width:0%;background:green;height:100%;border-radius:10px;color:#fff;font-family:helvetica;text-align:center;}
	#text{font-size:13px;line-height:22px;}
	</style>
	<div class="wrap"><div class="prog">Posting...</div></div> <div class="wrapper"></div>
	
	<div class="caption">
		<input type="hidden" value="" id="temp">
		<div style="padding:10px" class="prelod">
		<center><img id="prvimg"style="max-height:200px;max-width:100%"></center>
		<h3 style='text-align:center;color:#191970'>Add Message to the Image</h3>
		<form method="post" id="picfom" enctype="multipart/form-data">
		<input type="hidden"name="cto"value="<?php echo $uid;?>"id="cto">
		<input type="hidden"name="cfro"value="<?php echo $rid;?>"id="cfro">
		<input type="file"accept="image/*"id="pic"name="photo"style="display:none;" onchange="loadcaption()">
		<p><textarea name="dtxt" id="mssg" onkeyup="resizeim('s')" onblur="resizeim('h')" style="width:100%;height:100px;margin:10px 0px"
		placeholder="Type Message"autofocus></textarea></p>
		<p style='text-align:right;'><button class="btn"style="background:brown;margin-right:20px"onclick="cancelupd(event)">Cancel</button>
		<button class="btn" onclick="savepic(event)">Send</button></p><br>
		</form>
		</div>
		<div class="postlod" style="display:none;margin:20px"><br><br>
		<h4 style='text-align:center;color:#191970'>Uploading Photo</h4><br>
		<div id="pbc"style="margin:0 auto"><div id="progress"><span id="text"></span></div></div>
		</div>
	</div>
	
	<nav>
	<h3 style="line-height:50px;font-size:22px;padding:0px 20px;font-family:sitka text;text-shadow:0px 1px 1px #000;"><?php echo $name;?></h3>
	</nav>
	<div class="main">
	<div id="activity" style="height:100%;overflow:auto;"><div style="width:100%;height:70px"></div>
	<div style="padding:20px;overflow:auto">
	<?php
		$qry=mysqli_query($con,"SELECT *FROM `chats` WHERE (`sender`='$uid' AND `receiver`='$rid') OR (`sender`='$rid' AND `receiver`='$uid')");
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
	?>
	<div style="height:40px;float:right;width:100%"></div></div>
	</div></div>
	
	<div class="bott"><div style="padding:20px 10px">
	<form method="post"id="cform"onsubmit="sendmssg(event)">
	<input type="hidden"name="cto"value="<?php echo $uid;?>"id="cto">
	<input type="hidden"name="cfro"value="<?php echo $rid;?>"id="cfro">
	<textarea name="chat" id="mssg" placeholder="Type Message"onkeyup="scrolbottom()" autofocus required></textarea>
	<p style="text-align:right;margin-bottom:10px">
	<label for="pic"><i class="fa fa-camera"style="font-size:28px;color:#008080;cursor:pointer"title="Select Photo"></i></label></p>
	<p><button class="btn"style="float:right"><i class="fa fa-arrow-right"></i> Send</button></p>
	</form>
	</div></div>
	</html>
	
	<script>
	
	function resizeim(v){
		if(v=="s"){
			$("#prvimg").animate({
				height:"50px"
			},500);
			$(".caption").animate({
				top:"1%"
			},500);
		}else{
			$("#prvimg").animate({
				height:"150px"
			},500);
			$(".caption").animate({
				top:"5%"
			},500);
		}
	}
	
	function scrolbottom(){
		var div=_("activity");
		$("#activity").animate({
			scrollTop:div.scrollHeight-div.clientHeight
		},500);
	}
	scrolbottom();
	
	function cancelupd(e){
		e.preventDefault();
		if(confirm("Cancel Image Upload?")){
			$(".wrapper").fadeOut(); $(".caption").fadeOut(); _("picfom").reset();
		}
	}
	
	function delmssg(tp,d,id){
		if(confirm("Delete "+tp+"?")){
			$.ajax({
				method:"post",url:"chatsaver.php",data:{dmssg:d},
				beforeSend:function(){$(".wrap").fadeIn(); $(".prog").html("<center>Deleting...please wait</center>");},
				complete:function(){$(".wrap").fadeOut();}
			}).fail(function(){
				alert("Failed: Check internet Connection");
			}).done(function(res){
				$(".wrap").fadeOut(); $("#"+id).hide();
			});
		}
	}
	
	function closeprog(){
		$(".wrapper").fadeOut(); $(".caption").fadeOut(); _("picfom").reset();
	}
	
	function loadcaption(){
		var img=_("pic").files[0]; $(".prelod").show(); $(".postlod").hide();
		if(img!=null){
			var type=img.type;
			type=type.toLowerCase();
			if(type=="image/jpg" || type=="image/jpeg" || type=="image/png" || type=="image/gif"  ){
				$(".caption").fadeIn(); $(".wrapper").fadeIn();
				var reader=new FileReader();
				reader.onload=function(){
					var preview=document.querySelector('#prvimg');
					preview.style.display='block';
					preview.src=reader.result;
				}
				reader.readAsDataURL(event.target.files[0]);
			}else{
				alert("Invalid File Type. Choose valid Image");
			}
		}
	}
	
	function _(e){
		return document.getElementById(e);
	}
	
	function savepic(e){
		$("#pbc").fadeIn(); $(".prelod").hide(); $(".postlod").show();
		var fom=_("picfom");
		var data=new FormData(fom);
		var img=_("pic");
		data.append("file",img);
		var x=new XMLHttpRequest();
		x.onreadystatechange=function(){
		x.upload.addEventListener("progress",progressHandle,false);
		x.addEventListener("load",completeHandle,false);
		x.addEventListener("error",errorHandle,false);
		x.addEventListener("abort",abortHandle,false);
			if(x.status==200 && x.readyState==4){
				if(x.responseText.trim()=="success"){
					loadmssg();
				}else{
					alert(x.responseText.trim()); 
				}
			}
		}
		x.open("POST","chatsaver.php",true);
		x.send(data);
		e.preventDefault();
	}
	
	function progressHandle(event){
		var percent=(event.loaded / event.total) * 100;
		_("progress").style.width= Math.round(percent)+"%";
		_("text").innerHTML= Math.round(percent)+"%";
		if(percent==100){
			_("text").innerHTML="processing...please wait";
			_("progress").style.background="#008fff";
		}
	
	}
	function completeHandle(event){
		loadmssg(); closeprog();
		_("progress").style.background="green";
		_("progress").style.width="0%";
		_("pbc").style.display="none";
	}
	function errorHandle(event){
		closeprog(); alert("Upload Failed");
		_("progress").style.width="0%";
		_("pbc").style.display="none";
	}
	function abortHandle(event){
		closeprog(); alert("Upload aborted");
		_("progress").style.width="0%";
		_("pbc").style.display="none";
	}
	
	function sendmssg(e){
		e.preventDefault();
		var data=$("#cform").serialize();
		$.ajax({
			method:"post",url:"chatsaver.php",data:data,
			beforeSend:function(){$(".wrap").fadeIn(); $(".prog").html("<center>Posting...please wait</center>");},
			complete:function(){$(".wrap").fadeOut();}
		}).fail(function(){
			alert("Failed: Check internet Connection");
		}).done(function(res){
			loadmssg(); $(".wrap").fadeOut(); document.getElementById("cform").reset();
			document.getElementById("mssg").focus();
		});
	}
	setInterval(checkmssg,3000);
	
	function checkmssg(){
		var mx=_("maxtm").value.trim();
		$.ajax({
			method:"post",url:"chatsaver.php",data:{maxtm:mx}
		}).done(function(res){
			if(res.trim()>mx){loadmssg();}
		});
	}
	
	function loadmssg(){
		$("#activity").load("chatsaver.php?showchats=<?php echo "$uid:$rid";?>"); setTimeout(scrolbottom,500); 
	}
	</script>
	
	<?php
	}
	?>