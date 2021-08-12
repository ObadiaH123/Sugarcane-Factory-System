<?php

	$con=mysqli_connect("localhost","root","","transmara");
	if(!$con){
		echo "Failed to connect to database";
	}
	
	function clean($data){
		$con=mysqli_connect("localhost","root","","transmara");
		return htmlspecialchars(trim(htmlentities(mysqli_real_escape_string($con,addslashes($data))))); 
	}
		
	function prepare($data){
		return html_entity_decode(strip_tags(stripslashes($data)));
	}
	
	function fnum($v){
		$d=@explode(".",$v); $adn=@$d[1];
		if($adn==null){return strrev(rtrim(chunk_split(strrev($v),3,","),","));}
		else{return strrev(rtrim(chunk_split(strrev($d[0]),3,","),",")).".$adn";}
	}

?>