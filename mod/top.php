<?php

include('dbcon.php');

$count_rows = mysqli_fetch_array(mysqli_query($stream, "SELECT COUNT(`ap`) AS `users` FROM `" .$_SESSION['t']. "`;"));
if($count_rows['users'] != '') {
	$info = '' .$count_rows['users']. ' characters currently tracked';
	$updatetext = '<a href="?u=all">UPDATE ALL</a> | <a href="?g">Graphs</a>';
}

if((!isset($_SESSION['showequip']) || $_SESSION['showequip'] == '0') && $count_rows['users'] != '')  {
	$equip = '| <a href="?showequip=1" style="color: orange;">SHOW EQUIP</a>';
}
elseif($_SESSION['showequip'] == '1') {
	$equip = '| <a href="?showequip=0" style="color: orange;">HIDE EQUIP</a>';
}
	
if((!isset($_SESSION['showhide']) || $_SESSION['showhide'] == '0') && $_SESSION['showequip'] == '1') {
	$showhide = '| <a href="?sh=1" style="color: orange;">HIDE CORRECT GEMS & ENCHANTS TO HIGHLIGHT MISSING</a>';
}
elseif($_SESSION['showhide'] == '1' && $_SESSION['showequip'] == '1') {
	$showhide = '| <a href="?sh=0" style="color: orange;">SHOW ALL ENCHANTS & GEMS</a>';
}

if(!isset($_SESSION['guest'])) {
	$sl = mysqli_fetch_array(mysqli_query($stream, "SELECT `sl` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
	$import = '<a href="?i">Import</a> | ';
	$settings = ' | <a href="?sett">Settings</a>';
	$share = ' | <span style="color: gold;" onClick="return confirm(\'Share this link to allow guests: http://guild.artifactpower.info/?sl=' .$sl['sl']. '\nThey can see everything you see but may not Delete or Import.\nAlternatively, tell them to login on the front page without a password.\')">Share</span>';
	$mail = ' | <a href="?mail">Contact Form</a>';
}
elseif(isset($_SESSION['guest'])) {
	$guest = '<br />Guest View';
}
	
echo '<div id="core"><h1 id="cent"><a href="http://guild.artifactpower.info/"><u>A</u>dvanced <u>G</u>uild <u>S</u>tatistics</a>' .$guest. '</h1>
	<p id="cent">
	<a href="http://' .$_SESSION['r']. '.battle.net/wow/en/guild/' .$_SESSION['s']. '/' .$_SESSION['g']. '/">' .$_SESSION['g']. '</a>
	– <a href="http://www.wowprogress.com/guild/' .$_SESSION['r']. '/' .$_SESSION['s']. '/' .$_SESSION['g']. '">Wowprogress</a>
	– <a href="https://www.warcraftlogs.com/search/?term=' .$_SESSION['g']. '">Warcraftlogs</a>
	- TEMPORARY LINK: <a href="http://guild.artifactpower.info/img/nh/"><span title="credit goes to Oaken of <The Kremling Krew> of US-Mannoroth">Heroic Nighthold Guide</span></a><br />
	' .$info. '</p>
	<p id="cent">currently no known issues
	<br />to manually update - logout (character selection is enough; armory _should_ update instantly)<br /><br />
	<img src="img/me.png" alt="404" /> = missing enchant
	| <img src="img/mg.png" alt="404" /> = missing gem<br />
	click on a column or class/role to sort | hover over Mythics number to see different amounts</p>
	<hr>
	<center>' .$import. ' ' .$updatetext. ' ' .$equip. ' ' .$showhide. '' .$settings. '' .$share. '' .$mail. ' | <a href="?die">Logout</a></center>
	<hr>';
?>