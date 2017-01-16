<?php

include('dbcon.php');

echo '<h1 id="cent"><a href="http://guild.artifactpower.info/"><u>A</u>dvanced <u>G</u>uild <u>S</u>tatistics</a></h1>';
$count = mysqli_num_rows(mysqli_query($stream, "SELECT `id` FROM `guilds`"));
echo '<p style="color: orange; text-align: center;">' .$count. ' guilds registered!</p>
<form id="cent" action="" method="POST">
<select name="g" id="cent">
<option selected disabled>select your guild</option>';

$alph = array('Russian', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

foreach($alph as $letter) {
	if($letter != 'Russian') {
		echo '<optgroup label="' .$letter. '">';
		$guilds = mysqli_query($stream, "SELECT `id`, `g`, `r`, `s` FROM `guilds` WHERE LEFT(`g`, 1) = '" .$letter. "' ORDER BY `g` ASC");
		while($guild = mysqli_fetch_array($guilds)) {			
			echo '<option value="' .$guild['id']. '">' .$guild['g']. ' (' .$guild['r']. '-' .$guild['s']. ')</option>';
		}
	}
	elseif($letter == 'Russian') {
		echo '<optgroup label="' .$letter. '">';
		
		$alph = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		foreach($alph as $russian) {
			if(!isset($sql)) {
				$sql = 'WHERE LEFT(`g`, 1) != "' .$russian. '"';
			}
			elseif(isset($sql)) {
				$sql.= ' AND LEFT (`g`, 1) != "' .$russian. '"';
			}
		}
				
		$guilds = mysqli_query($stream, "SELECT `id`, `g`, `r`, `s` FROM `guilds` " .$sql. " ORDER BY `g` ASC");
		while($guild = mysqli_fetch_array($guilds)) {
			echo '<option value="' .$guild['id']. '">' .$guild['g']. ' (' .$guild['r']. '-' .$guild['s']. ')</option>';
		}
	}
	echo '</optgroup>';
}
echo '</select><br />
<input type="password" name="cd" value="" placeholder="password"/>
<br />
<button type="submit" id="cent">Login</button>
</form>
<p id="cent"><b><a href="http://check.artifactpower.info/?c=Xepheris&r=EU&s=Blackmoore">DEMO with reduced functions</a><br />
<a href="http://guild.artifactpower.info/?sl=8cb923fe2227b58015520ea2aee020bb">GUILD DEMO (guest view)</a></b>
<p id="cent">
<b>What is included?</b><br />
• missing enchant/gem filter<br />
• guest access (login without password or via shared link as admin)<br />
• relative colorization of many columns<br />
• individual raid progress<br />
• graphs for all tracked characters aswell as the guild as whole<br />
• sorting<br />
<br />
<b>Who needs this?</b><br />
• any active raiding guild, especially on mythic<br />
• loot councils
<p id="cent"><b>Try it out below!</b></p>	
<form action="" method="POST" id="cent">
<input type="text" name="gn" value="" placeholder="guild name case sensitive" />
<input type="password" name="gpw" value="" placeholder="password" />
<select name="r" id="r"><option value="EU">EU</option><option value="US">US</option></select>
<select name="s" id="s">
</select>
<button type="submit">Insert</button>
</form>
<p id="cent">Your guild has been wrongfully claimed? Bugs? Suggestions?<br />Write me a mail to <a href="mailto:xepheris.dh.tank@gmail.com">xepheris.dh.tank@gmail.com</a>!<br />
<a href="https://github.com/xepheris/World-of-Warcraft---Legion-Advanced-Guild-Statistics" style="font-size: 12px;">source code</a></p>';

?>