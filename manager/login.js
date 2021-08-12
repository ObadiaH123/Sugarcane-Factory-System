	function _(el){
		return document.getElementById(el);
	}
	
	function checkkeys(e){
		var keycode=e.charCode? e.charCode : e.keyCode;
		if(keycode==13){
			login();
		}
	}
	
	function login(){
		if(_("usern").value==""){showerror(); _('lgrespo').innerHTML="&#9888 Username is empty"; _("usern").focus();}
		else if(_("upass").value==""){ showerror(); _('lgrespo').innerHTML="&#9888 Password is missing"; _("upass").focus();}
		else{
			_("lgrespo").style.display="none"; _("successd").style.display="block";
			_("successd").innerHTML="Processing...please wait";
			var fom=_("logform");
			var data=new FormData(fom);
			var x=new XMLHttpRequest();
			x.onreadystatechange=function(){
				if(x.status==200 && x.readyState==4){
					if(x.responseText.trim()=="success"){
						_("successd").innerHTML="Success login...redirecting";
						window.location="portal.php";
					}
					else{
						showerror(); _('lgrespo').innerHTML=x.responseText;
					}
				}
			}
			x.open("post","validate.php",true);
			x.send(data);
		}
	}
	
	function showerror(){
		_("lgrespo").style.display="block";
		_("successd").style.display="none";
		setTimeout(function(){_("lgrespo").style.display="none";},8000);
	}