<?php

include('dbcon.php');

$count_rows = mysqli_fetch_array(mysqli_query($stream, "SELECT COUNT(`ap`) AS `users` FROM `" .$_SESSION['t']. "`;"));
if($count_rows['users'] != '') {
	$info = '' .$count_rows['users']. ' characters currently tracked';
	$updatetext = '| <a href="?u=all">UPDATE ALL</a> | <a href="?g">Graphs</a>';
}

if((!isset($_SESSION['showequip']) || $_SESSION['showequip'] == '0') && $count_rows['users'] != '')  {
	$equip = '| <a href="?showequip=1">SHOW EQUIP</a>';
}
elseif($_SESSION['showequip'] == '1') {
	$equip = '| <a href="?showequip=0">HIDE EQUIP</a>';
}
	
if((!isset($_SESSION['showhide']) || $_SESSION['showhide'] == '0') && $_SESSION['showequip'] == '1') {
	$showhide = '| <a href="?sh=1">HIDE CORRECT GEMS & ENCHANTS TO HIGHLIGHT MISSING</a>';
}
elseif($_SESSION['showhide'] == '1' && $_SESSION['showequip'] == '1') {
	$showhide = '| <a href="?sh=0">SHOW ALL ENCHANTS & GEMS</a>';
}


	
echo '<div id="core"><h1 id="cent"><a href="http://guild.artifactpower.info/"><u>A</u>dvanced <u>G</u>uild <u>S</u>tatistics</a></h1>
	<p id="cent"><a href="http://' .$_SESSION['r']. '.battle.net/wow/en/guild/' .$_SESSION['s']. '/' .$_SESSION['g']. '/">' .$_SESSION['g']. '</a> – <a href="http://www.wowprogress.com/guild/' .$_SESSION['r']. '/' .$_SESSION['s']. '/' .$_SESSION['g']. '">Wowprogress</a> – <a href="https://www.warcraftlogs.com/search/?term=' .$_SESSION['g']. '">Warcraftlogs</a><br />
	' .$info. '</p>
	<p id="cent">known issues: armory still thinks legendaries are 895 and ToV loot = -5 ilvl & Wowhead cannot properly calculate weapon itemlevel on tooltip.</p>
	<p id="cent" style="font-size: 20px;"><img src="img/me.png" alt="404" /> = missing enchant | <img src="img/mg.png" alt="404" /> = missing gem<br />click on a column or class/role to sort | hover over Mythics number to see different amounts</p>
	<hr>
	<center><a href="?i">Import</a> ' .$updatetext. ' ' .$equip. ' ' .$showhide. ' | <a href="?changepw">Change Password</a> | <a href="?die">Logout</a></center>
	<hr>';
?>