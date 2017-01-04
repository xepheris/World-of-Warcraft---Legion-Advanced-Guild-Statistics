<?php

if($_POST['r'] == 'EU' || $_POST['r'] == 'US') {
	
	$pw = md5($_POST['gpw']);

	// CHECK IF GUILD ALREADY EXISTS
	include('dbcon.php');
	$check = mysqli_fetch_array(mysqli_query($stream, "SELECT `id` FROM `guilds` WHERE `g` = '" .$_POST['gn']. "' AND `r` = '" .$_POST['r']. "' AND `s` = '" .$_POST['s']. "'"));

	// CHECK IF GUILD EXISTS
	if($check['id'] != '' || !empty($check['id'])) {
		echo '<h2 id="cent" style="color: red;">Guild already exists! Please ask your guild/guild master about access.</h2>';
	}
	else {
		$s = $_POST['s'];
		$g = $_POST['gn'];
		if(strpos($_POST['s'], ' ') !== false) {
			$s = str_replace(' ', '-', $_POST['s']);
		}
		if(strpos($_POST['gn'], ' ') !== false) {
			$g = str_replace(' ', '%20', $_POST['gn']);
		}
		$url = 'https://' .$_POST['r']. '.api.battle.net/wow/guild/' .$s. '/' .$g. '?fields=members&locale=en_GB&apikey=KEY_HERE';

		// ENABLE SSL
		$arrContextOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, ),);  
		
		$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));

		if($data != '') {
			
			$data = json_decode($data, true);
			
			if($data['name'] == $_POST['gn']) {
				mysqli_query($stream, "INSERT INTO `guilds` (`g`, `r`, `s`, `l`, `p`) VALUES ('" .$_POST['gn']. "', '" .$_POST['r']. "', '" .$_POST['s']. "', '" .time('now'). "', '" .$pw. "');");
				echo '<h2 id="cent" style="color: green;">Guild inserted, you may login now!</h2>';
			}
		}
	}
}
	
?>

