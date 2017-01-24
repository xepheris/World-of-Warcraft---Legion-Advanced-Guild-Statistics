<?php

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
	<p style="text-align: center;">The following settings will change your guilds item colorization depending on the individual characters itemlevel<br/>Standard values are: gear bad: 0.5 | gear good: 0.75 | ap low: 0.45 | ap high: 0.65</p>
	<form action="?sett" method="POST">
	<div class="t">
	<div class="tr" style="text-align: center;">gear <u>worse than</u> (itemlevel-800)* <input type="text" maxlength="4" value="' .$g_thresholds['g_low']. '" name="g_low" style="width: 30px;" required /> of your highest will be considered <span style="color: red;">bad</span></div>
	<div class="tr" style="text-align: center;">gear between these two values will appear <span style="color: orange;">orange</span></div>
	<div class="tr" style="text-align: center;">gear <u>higher equal than</u> (itemlevel-800)* <input type="text" maxlength="4" value="' .$g_thresholds['g_high']. '" name="g_high" style="width: 30px;" required /> of your highest will be considered <span style="color: green;">good</span></div>
	<div class="tr" style="text-align: center;"><br >EXAMPLE: the best items of your guild will be the legendaries (currently 940) We will compare 910, 870 and 840.<br /><span style="color: green;">(910-800)/(940-800) = 0.79 => green</span> | <span style="color: orange;">(870-800)/(940-800) = 0.5 => orange</span> | <span style="color: red;">(840-800)/(940-800) = 0.29 => red</span></div>
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

?>