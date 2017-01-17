<?php

if(is_numeric($_POST['gr']) && $_POST['gr'] <= '10') {
	// ENABLE SSL
	$arrContextOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => flase, ),);
		
	$guild = $_SESSION['g'];
	if(strpos($guild, ' ') !== false) {
		$guild = str_replace(' ', '%20', $guild);
	}
	$server = $_SESSION['s'];
	if(strpos($server, ' ') !== false) {
		$server = str_replace(' ', '-', $server);
	}
			
	$url = 'https://' .$_SESSION['r']. '.api.battle.net/wow/guild/' .$server. '/' .$guild. '?fields=members&locale=en_GB&apikey=KEY_HERE';

	$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));
	if($data === FALSE) {
		echo '<p id="error">Sorry, according to the <a href="http://' .$_SESSION['r']. '.battle.net/wow/guild/' .$_SESSION['s']. '/' .$guild. '/">armory your guild</a> does not exist yet or anymore. Please wait until the armory has updated.';
	}
	elseif($data != '') {
		$data = json_decode($data, true);
		$chararray = array();
					
		foreach($data['members'] as $member) {
			if($member['rank'] <= $_POST['gr']) {
				array_push($chararray, $member['character']['name']);
			}
		}
			
		foreach($chararray as $char) {
			require_once('func.php');
			import($char);
			echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
		}
	}
}


?>