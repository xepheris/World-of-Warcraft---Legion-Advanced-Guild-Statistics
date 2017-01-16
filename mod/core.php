ap<script defer src="http://wow.zamimg.com/widgets/power.js"></script>
<script>
var wowhead_tooltips = {
	"hide": {
		"droppedby": true,
		"dropchance": true,
		"sellprice": true,
		"maxstack": true,
		"iconizelinks": true		
	}
}
</script>

<?php

if(isset($_GET['r']) && is_numeric($_GET['r'])) {
	include('dbcon.php');
	$name = mysqli_fetch_array(mysqli_query($stream, "SELECT `ch` FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$_GET['r']. "'"));
	
	mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$name['ch']. "'");
	mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$name['ch']. "'");
	mysqli_query($stream, "DELETE FROM `gg` WHERE `char` = '" .$name['ch']. "'");
}

if($_GET['u'] == 'all') {
	require_once('func.php');
	include('dbcon.php');
	$current_chars = mysqli_query($stream, "SELECT DISTINCT(`ch`) FROM `" .$_SESSION['t']. "`");
	while($char = mysqli_fetch_array($current_chars)) {
		import($char['ch']);
	}
}

if(isset($_GET['sett'])) {
	include('top.php');
	require_once('dbcon.php');
		
	// UPDATE PASSWORD
	if(isset($_POST['oldpw']) && isset($_POST['newpw1']) && isset($_POST['newpw2'])) {
		// COMPARE NEWPW1 AND NEWPW2
		if($_POST['newpw1'] != $_POST['newpw2']) {
			echo '<p style="color: red; text-align: center;">Your new password repetition was incorrect, try again.</p>';
		}
		elseif($_POST['newpw1'] == $_POST['newpw2']) {
			// FETCH OLD, COMPARE THAT TO GIVEN
			$oldpw = mysqli_fetch_array(mysqli_query($stream, "SELECT `p` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
			
			if(md5($_POST['oldpw']) != $oldpw['p']) {
				echo '<p style="color: red; text-align: center;">Your old password is incorrect, try again.<br />Redirecting in 2 seconds.</p>';
				echo '<meta http-equiv="refresh" content="2;url=http://guild.artifactpower.info/?sett" />';
			}
			elseif(md5($_POST['oldpw']) == $oldpw['p']) {
				$update = mysqli_query($stream, "UPDATE `guilds` SET `p` = '" .md5($_POST['newpw1']). "' WHERE `id` = '" .$_SESSION['t']. "'");
				if($update) {
					echo '<p style="color: green; text-align: center;">Your password has been updated - please remember: THIS CHANGE IS IMMEDIATE!</p>';
				}
				else {
					echo '<p style="color: red; text-align: center;">Sorry, server did not respond. Please try again later.</p>';
				}
			}
		}		
	}
	
	// UPDATE GEAR THRESHOLD
	if(isset($_POST['g_low']) && isset($_POST['g_high'])) {
		if((strlen($_POST['g_low']) > '4') || (strlen($_POST['g_high']) > '4')) {
			echo '<p style="color: red; text-align: center;">Maximum threshold length is 4 characters (ex. 0.65).</p>';
		}
		elseif((strlen($_POST['g_low']) <= '4') || (strlen($_POST['g_high']) <= '4')) {
			if($_POST['g_low'] < '1' && $_POST['g_high'] >= '0.05') {
				if($_POST['g_low'] > $_POST['g_high']) {
					echo '<p style="color: red; text-align: center;">Having a higher threshold for bad gear than for good does not make sense, please insert proper values.</p>';
				}
				else {
					$update = mysqli_query($stream, "UPDATE `guilds` SET `g_low` = '" .$_POST['g_low']. "', `g_high` = '" .$_POST['g_high']. "' WHERE `id` = '" .$_SESSION['t']. "'");
					if($update) {
						echo '<p style="color: green; text-align: center;">Your personal threshold for gear has been updated. This change is already in effect.</p>';
					}
					else {
						echo '<p style="color: red; text-align: center;">Sorry, server did not respond. Please try again later.</p>';
					}
				}
			}
			else {
				echo '<p style="color: red; text-align: center;">Values must be between 0.05 and 1.</p>';
			}
		}
	}
	
	// UPDATE AP THRESHOLD
	if(isset($_POST['ap_low']) && isset($_POST['ap_high'])) {
		if((strlen($_POST['ap_low']) > '4') || (strlen($_POST['ap_high']) > '4')) {
			echo '<p style="color: red; text-align: center;">Maximum threshold length is 4 characters (ex. 0.65).</p>';
		}
		elseif((strlen($_POST['ap_low']) <= '4') || (strlen($_POST['ap_high']) <= '4')) {
			if($_POST['ap_low'] < '1' && $_POST['ap_high'] >= '0.05') {
				if($_POST['ap_low'] > $_POST['ap_high']) {
					echo '<p style="color: red; text-align: center;">Having a higher threshold for low Artifact Power than for high does not make sense, please insert proper values.</p>';
				}
				else {
					$update = mysqli_query($stream, "UPDATE `guilds` SET `ap_low` = '" .$_POST['ap_low']. "', `ap_high` = '" .$_POST['ap_high']. "' WHERE `id` = '" .$_SESSION['t']. "'");
					if($update) {
						echo '<p style="color: green; text-align: center;">Your personal threshold for Artifact Power has been updated. This change is already in effect.</p>';
					}
					else {
						echo '<p style="color: red; text-align: center;">Sorry, server did not respond. Please try again later.</p>';
					}
				}
			}
			else {
				echo '<p style="color: red; text-align: center;">Values must be between 0.05 and 1.</p>';
			}
		}
	}
	
	// GEAR THRESHOLD CHANGE
	if(!isset($_POST['g_low']) && !isset($_POST['g_mid']) && !isset($_POST['g_high'])) {
		$g_thresholds = mysqli_fetch_array(mysqli_query($stream, "SELECT `g_low`, `g_high` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
		echo '
		<p style="text-align: center;">The following settings will change your guilds item colorization depending on the individual characters itemlevel<br/>Standard values are: gear bad: 0.6 | gear good: 0.8 | ap low: 0.45 | ap high: 0.65</p>
		<form action="?sett" method="POST">
		<div class="t">
		<div class="tr" style="text-align: center;">gear <u>worse than</u> (itemlevel-800)* <input type="text" maxlength="4" value="' .$g_thresholds['g_low']. '" name="g_low" style="width: 30px;" required /> of your highest will be considered <span style="color: red;">bad</span></div>
		<div class="tr" style="text-align: center;">gear between these two values will appear <span style="color: orange;">orange</span></div>
		<div class="tr" style="text-align: center;">gear <u>higher equal than</u> (itemlevel-800)* <input type="text" maxlength="4" value="' .$g_thresholds['g_high']. '" name="g_high" style="width: 30px;" required /> of your highest will be considered <span style="color: green;">good</span></div>
		<div class="tr" style="text-align: center;"><button type="submit">Change Equipment thresholds</button></center></div>
		</div>
		</form>';
	}
	
	// AP THRESHOLD CHANGE
	if(!isset($_POST['ap_low']) && !isset($_POST['ap_mid']) && !isset($_POST['ap_high'])) {
		$ap_thresholds = mysqli_fetch_array(mysqli_query($stream, "SELECT `ap_low`, `ap_high` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
		echo '<br />
		<form action="?sett" method="POST">
		<div class="t">
		<div class="tr" style="text-align: center;">AP <u>lower than</u> <input type="text" maxlength="4" value="' .$ap_thresholds['ap_low']. '" name="ap_low" style="width: 30px;" required /> of your highest members AP will be considered <span style="color: red;">bad</span></div>
		<div class="tr" style="text-align: center;">AP between these two values will appear <span style="color: orange;">orange</span></div>
		<div class="tr" style="text-align: center;">AP <u> higher or equal than</u> <input type="text" maxlength="4" value="' .$ap_thresholds['ap_high']. '" name="ap_high" style="width: 30px;" required /> of your highest members AP will be considered <span style="color: green;">good</span></div>
		<div class="tr" style="text-align: center;"><button type="submit">Change Artifact Power thresholds</button></div>
		</div>
		</form>';
	}
	
	// PASSWORD CHANGE
	if(!isset($_POST['oldpw']) && !isset($_POST['newpw1']) && !isset($_POST['newpw2'])) {
		echo '<hr>
		<form action="?sett" method="POST">
		<div class="t">
		<div class="tr" style="text-align: center;"><div class="td"><input type="password" placeholder="old password" name="oldpw" style="width: 230px;" required /></div></div>
		<div class="tr" style="text-align: center;"><div class="td"><input type="password" placeholder="new password" name="newpw1" style="width: 230px;" required /></div></div>
		<div class="tr" style="text-align: center;"><div class="td"><input type="password" placeholder="repeat new password" name="newpw2" style="width: 230px;" required /></div></div>
		<div class="tr" style="text-align: center;"><button type="submit">Change password - CAUTION: INSTANT!</button></div>
		</div>
		</form>';
	}
	
	die();
}

if(isset($_GET['u']) && is_numeric($_GET['u'])) {
	require_once('func.php');
	include('dbcon.php');
	$char = mysqli_fetch_array(mysqli_query($stream, "SELECT `ch` FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$_GET['u']. "'"));
	import($char['ch']);
	echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
}

if(!empty($_SESSION['g'])) {
	include('top.php');		
}
		
if(isset($_POST['gr'])) {
	
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
}
	
if(isset($_POST['c'])) {
	
	foreach($_POST['c'] as $char) {
		require_once('func.php');
		import($char);
	}
	
	if(strpos($_POST['c'], ',') !== FALSE) {
		$chars = explode(',', $_POST['c']);
		foreach($chars as $char) {
			$char = str_replace(' ', '', $char);
			require_once('func.php');
			import($char);		
		}
	}
	elseif(strpos($_POST['c'], ',') === FALSE) {
		$char = $_POST['c'];
		require_once('func.php');
		import($char);
	}
	echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
}
	
if(isset($_GET['i'])) {
	if($count_rows['users'] == '' || $count_rows['users'] == '0') {
		echo '<p id="cent">Welcome! You have no characters listed yet.</p>';
	}
	
	$guild = $_SESSION['g'];
	if(strpos($guild, ' ') !== false) {
		$guild = str_replace(' ', '%20', $guild);
	}
	$server = $_SESSION['s'];
	if(strpos($server, ' ') !== false) {
		$server = str_replace(' ', '-', $server);
	}
	
	echo '<p id="cent">Select characters you would like to import:</p>
	<form action="" method="POST" style="text-align: center;">
	<select multiple name="c[]" style="width: 250px; height: 250px;">';
	$url = 'https://' .$_SESSION['r']. '.api.battle.net/wow/guild/' .$server. '/' .$guild. '?fields=members&locale=en_GB&apikey=KEY_HERE';

	$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));
	if($data != '') {
		$data = json_decode($data, true);
		$chararray = array();
				
		for($rank = '0'; $rank <= '9'; $rank++) {
			${'' .$rank. 'array'} = array();
			foreach($data['members'] as $members) {
				if($members['rank'] == $rank) {
					if($members['character']['level'] == '110') {
						array_push(${'' .$rank. 'array'}, $members['character']['name']);
					}
				}
			}
		
			sort(${'' .$rank. 'array'});
			
			echo '<optgroup label="Guildrank ' .($rank+1). '">';
		
			foreach(${'' .$rank. 'array'} as $char) {
				echo '<option value="' .$char. '">' .$char. '</option>';
			}
			
			echo '</optgroup>';
			
		}
	}
	
	echo '</select><br />
	<button type="submit">Import</button>
	</form>
	<hr>
	<form action="" method="POST">
	<p id="cent">Import characters based on guild rank (1 = guild leader only, 10 = whole guild - <span style="color: red;">WARNING:</span> 10 takes ages on big guilds, use with caution!)
	<select name="gr">';
	for($i = '1'; $i <= '10'; $i++) {
		echo '<option value="' .($i-1). '">' .$i. ' rank(s)</option>';
	}
	echo '</select><button type="submit">Import</button></p>
	</form>
	<hr>
	<form action="" method="POST">
	<p id="cent">Import specific characters only (separated by commas, case-sensitive)
		<input type="text" name="c" placeholder="a,b,c,d" />
	<button type="submit">Import</button>
	</form>';
}
	
if(!isset($_GET['i'])) {			
	if($count_rows['users'] == '' || $count_rows['users'] == '0') {
		echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/?i" />';
	}	
	else {
		echo '<div class="t">
		<div class="tb">
		<div class="tr">';
		
		if(!isset($_GET['s']) || substr($_GET['s'], '-1') == '2') {
			$num = '1';
		}
		elseif(substr($_GET['s'], '-1') == '1') {
			$num = '2';
		}		
		$columnarray = array('' => '', 'Class' => 'cl' .$num. '', 'Role' => 'ro' .$num. '', 'Total AP' => 'to' .$num. '', 'Artifact Level (AK)' => 'al' .$num. '', 'Equipped' => 'eq' .$num. '', 'Bags' => 'ba' .$num. '', 'Weapon' => 'wa' .$num. '', 'Head' => 'he' .$num. '', 'Neck' => 'ne' .$num. '', 'Shoulder' => 'sh' .$num. '', 'Back' => 'ba' .$num. '', 'Chest' => 'ch' .$num. '', 'Wrist' => 'wr' .$num. '', 'Hands' => 'ha' .$num. '', 'Waist' => 'wi' .$num. '', 'Legs' => 'le' .$num. '', 'Feet' => 'fe' .$num. '', 'Ring1' => 'r1' .$num. '', 'Ring2' => 'r2' .$num. '', 'Trinket1' => 't1' .$num. '', 'Trinket2' => 't2' .$num. '', 'Mythics' => 'my' .$num. '', 'Highest M+' => 'mp' .$num. '', 'EN' => 'en' .$num. '', 'ToV' => 'tov' .$num. '', 'NH' => 'nh' .$num. '', '' => '', '' => '');
			
		foreach($columnarray as $column => $sort) {
			if($column == 'Head' || $column == 'Neck' || $column == 'Shoulder' || $column == 'Back' || $column == 'Chest' || $column == 'Wrist' || $column == 'Hands' || $column == 'Waist' || $column == 'Legs' || $column == 'Feet' || $column == 'Ring1' || $column == 'Ring2' || $column == 'Trinket1' || $column == 'Trinket2') {
				echo '<div class="tc" id="equip" style="border-bottom: 1px solid grey;"><a href="?s=' .$sort. '">' .$column. '</a></div>';
			}
			else {
				echo '<div class="tc" style="border-bottom: 1px solid grey;"><a href="?s=' .$sort. '">' .$column. '</a></div>';
			}
		}
		echo '</div>
		</div>';
		
		
		if(!isset($_GET['fs']) && !isset($_GET['fc'])) {
			if(isset($_GET['s'])) {
				if(substr($_GET['s'], '0', '2') == 'cl') { $sortby = '`c`'; }
				if(substr($_GET['s'], '0', '2') == 'ro') { $sortby = '`s`'; }
				if(substr($_GET['s'], '0', '2') == 'to') { $sortby = '`ap`'; }
				if(substr($_GET['s'], '0', '2') == 'al') { $sortby = '`alvl`'; }
				if(substr($_GET['s'], '0', '2') == 'eq') { $sortby = '`ilvlavg`'; }
				if(substr($_GET['s'], '0', '2') == 'ba') { $sortby = '`ilvlbags`'; }
				if(substr($_GET['s'], '0', '2') == 'wa') { $sortby = '`mh_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'he') { $sortby = '`he_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'ne') { $sortby = '`n_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'sh') { $sortby = '`s_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'bh') { $sortby = '`b_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'ch') { $sortby = '`c_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'wr') { $sortby = '`wr_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'ha') { $sortby = '`ha_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'wi') { $sortby = '`wa_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'le') { $sortby = '`l_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'fe') { $sortby = '`f_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'r1') { $sortby = '`f1_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'r2') { $sortby = '`f2_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 't1') { $sortby = '`t1_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 't2') { $sortby = '`t2_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'my') { $sortby = '`sum`'; }
				if(substr($_GET['s'], '0', '2') == 'mp') { $sortby = '`mplus`'; }
				if(substr($_GET['s'], '0', '2') == 'en') { $sortby = '`en`'; }
				if(substr($_GET['s'], '0', '3') == 'tov') { $sortby = '`tov`'; }
				if(substr($_GET['s'], '0', '2') == 'nh') { $sortby = '`nh`'; }
				
						
				if(substr($_GET['s'], '-1') == '1') { $sortby.= ' DESC'; }
				elseif(substr($_GET['s'], '-1') == '2') { $sortby.= ' ASC'; }			
			}
		
			if(!isset($_GET['s'])) {
				$sortby = '`ch` ASC';
			}
		
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` ORDER BY " .$sortby. "");
		}
		elseif(isset($_GET['fs'])) {
			$classspecc = mysqli_fetch_array(mysqli_query($stream, "SELECT `id`, `s` FROM `weapons` WHERE `w` = '" .$_GET['fs']. "'"));
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` WHERE `s` = '" .$classspecc['s']. "' AND `c` = '" .$classspecc['id']. "' ORDER BY `ch` ASC");			
		}
		elseif(isset($_GET['fc'])) {
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` WHERE `c` = '" .$_GET['fc']. "' ORDER BY `c` ASC");
		}

		$server = $_SESSION['s'];
		if(strpos($server, ' ') !== false) {
			$server = str_replace(' ', '-', $server);
		}
		
		$rowarray = array('he', 'n', 's', 'b', 'c', 'wr', 'ha', 'wa', 'l', 'f', 'f1', 'f2', 't1', 't2');
		foreach($rowarray as $max) {
			${'' .$max. '_color'} = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`" .$max. "_ilvl`) AS `" .$max. "_max` FROM `" .$_SESSION['t']. "`"));
		}
		
		$apcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ap`) AS `apcap` FROM `" .$_SESSION['t']. "`"));
		$alvlcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`alvl`) AS `alvlcap` FROM `" .$_SESSION['t']. "`"));
		$eqcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ilvlavg`) AS `avgcap` FROM `" .$_SESSION['t']. "`"));
		$bagscap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ilvlbags`) AS `bagmax` FROM `" .$_SESSION['t']. "`"));
			
		$num = '0';		
		while($data = mysqli_fetch_array($characterdata)) {
			if ($num % 2 != 0) {
				$style = 'style="background-color: darkslategrey;"';
			}
			else {
				$style = '';
			}

			$compare_old = mysqli_fetch_array(mysqli_query($stream, "SELECT `ap`, `sum`, `ilvlavg`, `ilvlbags`, `alvl` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$data['ch']. "' ORDER BY `lupd` DESC LIMIT 1"));
			
			if(!empty($compare_old)) {
				
				if(($data['ap']-$compare_old['ap']) != '0') {
					$ap_incr = '<span style="color: grey;">(+' .(round($data['ap']/$compare_old['ap']-1, 3)*100). '%)</span>';
				}
				
				if(($data['sum']-$compare_old['sum']) != '0') {
					$sum_old = '<span style="color: grey;">(+' .($data['sum']-$compare_old['sum']). ')</span>';
				}
				
				if(($data['ilvlavg']-$compare_old['ilvlavg']) != '0') {
					$ilvlavg_old = '<span style="color: grey;">(+' .($data['ilvlavg']-$compare_old['ilvlavg']). ')</span>';
				}
								
				if(($data['ilvlbags']-$compare_old['ilvlbags']) != '0') {
					$bags_old = '<span style="color: grey;">(+' .($data['ilvlbags']-$compare_old['ilvlbags']). ')</span>';
				}
				
				if(($data['alvl']-$compare_old['alvl']) != '0') {
					$alvl_old = '<span style="color: grey;">(+' .($data['alvl']-$compare_old['alvl']). ')</span>';
				}
			}
			
			if(time('now')-$data['lupd'] > '86400') {
				$update = 'style="color: red; font-size: 10px;"';
			}
			elseif(time('now')-$data['lupd'] < '86400' && time('now')-$data['lupd'] >= '43200') {
				$update = 'style="color: orange; font-size: 10px;"';
			}
			elseif(time('now')-$data['lupd'] < '43200') {
				$update = 'style="color: green; font-size: 10px;"';
			}
			
			
			$weapon_id = mysqli_fetch_array(mysqli_query($stream, "SELECT `w` FROM `weapons` WHERE `s` = '" .$data['s']. "' AND `id` = '" .$data['c']. "'"));
			$class = mysqli_fetch_array(mysqli_query($stream, "SELECT `class`, `color` FROM `classes` WHERE `id` = '" .$data['c']. "'"));
			echo '<div class="tr" ' .$style. '>
			<div class="tc"><a href="http://' .$_SESSION['r']. '.battle.net/wow/en/character/' .$server. '/' .$data['ch']. '/simple" title="Logged out: ' .round(((time('now')-$data['llog'])/3600), 2). ' hrs. ago – Last update: ' .round(((time('now')-$data['lupd'])/3600), 2). ' hrs. ago">' .$data['ch']. '</a> <span ' .$update. '>upd: ' .round(((time('now')-$data['lupd'])/3600), 2). ' hrs. ago <a href="http://www.wowprogress.com/character/' .$_SESSION['r']. '/' .$server. '/' .$data['ch']. '"><img src="img/wpr.ico" alt="404" /></a> <a href="http://check.artifactpower.info/?r=' .$_SESSION['r']. '&s=' .$server. '&c=' .$data['ch']. '"><img src="img/aaa.png" alt="404" /></a></div>
			<div class="tc" style="background:' .$class['color']. ';"><a href="?fc=' .$data['c']. '">' .$class['class']. '</a></div>
			<div class="tc"><a href="?fs=' .$weapon_id['w']. '">' .$data['s']. '</a></div>';
			
			$thresholds = mysqli_fetch_array(mysqli_query($stream, "SELECT `ap_low`, `ap_high`, `g_low`, `g_high` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
			
			$apcheck = round(($data['ap'])/($apcap['apcap']), 2);
			if($apcheck >= $thresholds['ap_high']) { $ap = 'style="color: green;"'; }
			if($apcheck >= $thresholds['ap_low'] && $apcheck < $thresholds['ap_high']) { $ap = 'style="color: orange;"'; }
			if($apcheck < $thresholds['ap_low']) { $ap = 'style="color: red;"'; }
			
			$alvlcheck = round(($data['alvl'])/($alvlcap['alvlcap']), 2);
			if($alvlcheck >= '0.8') { $alvl = 'style="color: green;"'; }
			if($alvlcheck >= '0.6' && $alvlcheck < '0.8') { $alvl = 'style="color: orange;"'; }
			if($alvlcheck < '0.6') { $alvl = 'style="color: red;"'; }
			
			$avgcheck = round(($data['ilvlavg']-800)/($eqcap['avgcap']-800), 2);
			if($avgcheck >= $thresholds['g_high']) { $avg = 'style="color: green;"'; }
			if($avgcheck >= $thresholds['g_low'] && $avgcheck < $thresholds['g_high']) { $avg = 'style="color: orange;"'; }
			if($avgcheck < $thresholds['g_low']) { $avg = 'style="color: red;"'; }
				
			$bagcheck = round(($data['ilvlbags']-800)/($bagscap['bagmax']-800), 2);
			if($bagcheck >= $thresholds['g_high']) { $bags = 'style="color: green;"'; }
			if($bagcheck >= $thresholds['g_low'] && $bagcheck < $thresholds['g_high']) { $bags = 'style="color: orange;"'; }
			if($bagcheck < $thresholds['g_low']) { $bags = 'style="color: red;"'; }				
			
			echo '<div class="tc"><span ' .$ap. '>' .number_format($data['ap']). ' ' .$ap_incr. '</span></div>
			<div class="tc"><span ' .$alvl. '>' .$data['alvl']. ' ' .$alvl_old. ' (' .$data['ak']. ')</span></div>
			<div class="tc"><span ' .$avg. '>' .$data['ilvlavg']. ' ' .$ilvlavg_old. '</span></div>
			<div class="tc"><span ' .$bags. '>' .$data['ilvlbags']. ' ' .$bags_old. '</span></div>';

			
			if(empty($data['oh_bonus'])) {
				$weapon = '<a href="http://wowhead.com/item=' .$weapon_id['w']. '&bonus=' .$data['mh_b']. '" rel="gems=' .$data['mh_r1']. ':' .$data['mh_r2']. ':' .$data['mh_r3']. '">' .$data['mh_ilvl']. '</a>';
			}
			else {
				$weapon = '<a href="http://wowhead.com/item=' .$weapon_id['w']. '&bonus=' .$data['oh_bonus']. '" rel="gems=' .$data['oh_r1']. ':' .$data['oh_r2']. ':' .$data['oh_r3']. '">' .$data['oh_ilvl']. '</a>';
			}
			
			echo '<div class="tc">' .$weapon. '</div>';
			$rowarray = array('he', 'n', 's', 'b', 'c', 'wr', 'ha', 'wa', 'l', 'f', 'f1', 'f2', 't1', 't2');
			foreach($rowarray as $row) {
							
				$qualitycheck = round(($data['' .$row. '_ilvl']-800)/(${'' .$row. '_color'}['' .$row. '_max']-800), 2);			
				if($qualitycheck >= '0.8') { $quality = 'style="color: green;"'; }
				if($qualitycheck >= '0.6' && $qualitycheck < '0.8') { $quality = 'style="color: orange;"'; }
				if($qualitycheck < '0.6') { $quality = 'style="color: red;"'; }
				
							
				if(!isset($_SESSION['showhide']) || $_SESSION['showhide'] == '0') {
					$socketcheck = strpos($data['' .$row. '_b'], '1808');
					if($data['' .$row. '_g'] != '0') {
						$swaparray = array('130218' => 'gps', '130221' => 'gbb', '130220' => 'gyb', '130216' => 'gys', '130246' => 'gsb', '130222' => 'gpb', '130219' => 'gob', '130248' => 'gsb', '130247' => 'gsb', '130215' => 'grs');
						foreach($swaparray as $old => $new) {
							if($data['' .$row. '_g'] == $old) {
								$gc = $new;
							}
						}
						$gem = '<a href="http://wowhead.com/item=' .$data['' .$row. '_g']. '&lvl=110"><img src="img/' .$gc. '.gif" /></a>';
					}			
					elseif($data['' .$row. '_g'] == '0' && $socketcheck > '-1') {
						$gem = '<img src="img/mg.png" alt="404" />';
					}
					
					// IF ROW = ENCHANTABLE
					if($row == 'n' || $row == 's' || $row == 'b' || $row == 'f1' || $row == 'f2') {
						// IF ENCHANT = MISSING
						if($data['' .$row. '_e'] == '0') {
							$enchant = '<img src="img/me.png" alt="404" />';
						}
						// ELSE IF ENCHANT EXISTS
						elseif($data['' .$row. '_e'] != '0') {
							// VALID ENCHANTS
							$swaparray = array('5437' => '128551', '5439' => '128553', '5883' => '140219', '5889' => '141908', '5890' => '141909', '5891' => '141910', '5431' => '128545', '5432' => '128546', '5433' => '128547', '5434' => '128548', '5435' => '128549', '5436' => '128550', '5423' => '128537', '5424' => '128538', '5425' => '128539', '5426' => '128540', '5427' => '128541', '5428' => '128542', '5429' => '128543', '5430' => '128544', '5442' => '140214', '5882' => '140218', '5440' => '128554', '5883' => '140219', '5441' => '140213', '5443' => '140215', '5881' => '140217');
							
							// 7.1.5 ENCHANTS TO MERGE WITH SWAPARRAY ABOVE
							/*
							'MARK OF THE MASTER BLIZZ ID' => '144304', 'MARK OF THE VERSATILE BLIZZ ID' => '144304', 'MARK OF THE QUICK BLIZZ ID' => '144306', 'MARK OF THE DEADLY BLIZZ ID' => '144307');
							
							*/
							// CHECK FOR VALID ENCHANTMENT
							foreach($swaparray as $old => $new) {
								// IF YES
								if($data['' .$row. '_e'] == $old) {
									
									// CHECK IF ROW = SHOULDER
									if($row == 's') {
										
										// SPECIAL SHOULDER ENCHANTMENTS
										$shoulder_e = array('140215' => 'eb', '140218' => 'em', '140219' => 'ebh', '140213' => 'eg', '140217' => 'es', '140214' => 'eh');

										// CHECK FOR SPECIAL SHOULDER ENCHANTMENT
										foreach($shoulder_e as $shoulder_id => $e_img) {
											// IF SHOULDER ENCHANTMENT = SPECIAL
											if($shoulder_id == $new) {
												// SWAP
												$enchant_img = $e_img;
												
											}											
										}
									}
									$enchant = '<a href="http://wowhead.com/item=' .$new. '"><img src="img/' .$enchant_img. '.gif" /></a>';
								}
							}
						}
					}
				}
				if($_SESSION['showhide'] == '1') {
					if($row == 'he' || $row == 'n' || $row == 's' || $row == 'b' || $row == 'c' || $row == 'ha' || $row == 'wa' || $row == 'l' || $row == 'f' || $row == 'f1' || $row == 'f2' || $row == 't1' || $row == 't1') {
						$socketcheck = strpos($data['' .$row. '_b'], '1808');
						if($socketcheck > '-1') {
							$gemarray = array('130246', '130247', '130220', '130222', '130219', '130248', '130221');
							if($data['' .$row. '_g'] == '0' || !in_array($data['' .$row. '_g'], $gemarray)) {
								$gem = '<img src="img/mg.png" alt="404" />';
							}
						}
					}
					
					if($row == 'n' || $row == 's' || $row == 'b' || $row == 'f1' || $row == 'f2') {
						$enchantarray = array('5442', '5882', '5440', '5883', '5441', '5443', '5881', '5427', '5428', '5429', '5430', '5435', '5434', '5436', '5437', '5439', '5883', '5889', '5890', '5891');
						
						if($data['' .$row. '_e'] == '0' || !in_array($data['' .$row. '_e'], $enchantarray)) {
							$enchant = '<img src="img/me.png" alt="404" />';
						}
					}
				}
				
				echo '<div class="tc" id="equip"><a href="http://wowhead.com/item=' .$data['' .$row. '_id'].'&bonus=' .$data['' .$row. '_b']. '" rel="gems=' .$data['' .$row. '_g']. '&ench=' .$data['' .$row. '_e']. '" ' .$quality. '>' .$data['' .$row. '_ilvl']. '</a> ' .$gem. ' ' .$enchant. '</div>';
				unset($gem);
				unset($enchant);
				unset($gc);
				unset($enchant_img);
				
			}
			
			if($data['mplus'] == '15') { $mplus = '<span style="color: green;">15</span>'; }
			elseif($data['mplus'] == '10') { $mplus = '<span style="color: orange;">10</span>'; }
			elseif($data['mplus'] == '5') { $mplus = '<span style="color: red;">5</span>'; }
			elseif($data['mplus'] < '5') { $mplus = '<span style="color: grey;">' .$data['mplus']. '</span>'; }
			
			echo '<div class="tc"><span title="ARC ' .$data['arc']. ' BRH ' .$data['brh']. ' COS ' .$data['cos']. ' DHT ' .$data['dht']. ' EOA ' .$data['eoa']. ' HOV ' .$data['hov']. ' MOS ' .$data['mos']. ' NEL ' .$data['nel']. ' VOW ' .$data['vow']. ' VH ' .$data['vh']. '">' .$data['sum']. ' ' .$sum_old. '</span></div>
			<div class="tc">' .$mplus. '</div>';
			
			if($data['en'] == '0') { $color_en = 'style="color: red;"'; } elseif($data['en'] == '7') { $color_en = 'style="color: green;"'; } elseif($data['en'] > '0') { $color_en = 'style="color: orange;"'; }			
			if($data['tov'] == '0') { $color_tov = 'style="color: red;"'; } elseif($data['tov'] == '3') { $color_tov = 'style="color: green;"'; } elseif($data['tov'] > '0') { $color_tov = 'style="color: orange;"'; }
			if($data['nh'] == '0') { $color_nh = 'style="color: red;"'; } elseif($data['nh'] == '10') { $color_nh = 'style="color: green;"'; } elseif($data['nh'] > '0') { $color_nh = 'style="color: orange;"'; }
			
			echo '<div class="tc"><span ' .$color_en. '>' .$data['en']. '/7</span></div>
			<div class="tc"><span ' .$color_tov. '>' .$data['tov']. '/3</span></div>
			<div class="tc"><span ' .$color_nh. '>' .$data['nh']. '/10</span></div>
			<div class="tc"><a href="?u=' .$data['id']. '"><img src="img/upd.png" alt="404" style="width: 13px;"/></a></div>
			<div class="tc"><a href="?r=' .$data['id']. '"><img src="img/rmv.png" alt="404" style="width: 13px;"/></a></div>
			</div>';
			$num++;
			unset($ap_incr); unset($sum_old); unset($bags_old); unset($ilvlavg_old); unset($alvl_old);
		}
		echo '</div>';
	}
}

include('bot.php');
?>