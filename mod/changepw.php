<?php

include('top.php');

if(!isset($_POST['oldpw']) && !isset($_POST['newpw1']) && !isset($_POST['newpw2'])) {
	echo '<form action="?changepw" method="POST">
	<div class="t">
	<div class="tr"><div class="td"><input type="password" placeholder="old password" name="oldpw" style="width: 230px;" required /></div></div>
	<div class="tr"><div class="td"><input type="password" placeholder="new password" name="newpw1" style="width: 230px;" required /></div></div>
	<div class="tr"><div class="td"><input type="password" placeholder="repeat new password" name="newpw2" style="width: 230px;" required /></div></div>
	<button type="submit">Change password - CAUTION: INSTANT!</button>
	</div>
	</form>';
}
elseif(isset($_POST['oldpw']) && isset($_POST['newpw1']) && isset($_POST['newpw2'])) {
	// COMPARE NEWPW1 AND NEWPW2
	if($_POST['newpw1'] != $_POST['newpw2']) {
		echo '<p style="color: red; text-align: center;">Your new password repetition was incorrect, try again.<br />Redirecting in 2 seconds.</p>';
		echo '<meta http-equiv="refresh" content="2;url=http://guild.artifactpower.info/?changepw" />';
	}
	elseif($_POST['newpw1'] == $_POST['newpw2']) {
		// FETCH OLD, COMPARE THAT TO GIVEN
		include('dbcon.php');
		$oldpw = mysqli_fetch_array(mysqli_query($stream, "SELECT `p` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
		
		if(md5($_POST['oldpw']) != $oldpw['p']) {
			echo '<p style="color: red; text-align: center;">Your old password is incorrect, try again.<br />Redirecting in 2 seconds.</p>';
			echo '<meta http-equiv="refresh" content="2;url=http://guild.artifactpower.info/?changepw" />';
		}
		elseif(md5($_POST['oldpw']) == $oldpw['p']) {
			$sql = "UPDATE `guilds` SET `p` = '" .md5($_POST['newpw1']). "' WHERE `id` = '" .$_SESSION['t']. "'";
			$update = mysqli_query($stream, $sql);
			if($update) {
				echo '<p style="color: green; text-align: center;">Your password has been updated - please remember: THIS CHANGE IS IMMEDIATE!</p>';
			}
			
		}		
	}	
}

?>

