<?php

include('dbcon.php');

echo '<h1 id="cent"><a href="http://guild.artifactpower.info/"><u>A</u>dvanced <u>G</u>uild <u>S</u>tatistics</a></h1>
<form id="cent" action="" method="POST">
<select name="g" id="cent">
<option selected disabled>select your guild</option>';

$guilds = mysqli_query($stream, "SELECT `id`, `g`, `r`, `s` FROM `guilds` ORDER BY `g` ASC");
while($guild = mysqli_fetch_array($guilds)) {
	echo '<option value="' .$guild['id']. '">' .$guild['g']. ' (' .$guild['r']. '-' .$guild['s']. ')</option>';
}
echo '</select><br />
<input type="password" name="cd" value="" placeholder="password"/>
<br />
<button type="submit" id="cent">Login</button>
</form>
<p id="cent"><b><a href="http://check.artifactpower.info/?c=Xepheris&r=EU&s=Blackmoore">DEMO with reduced functions</a></b>
<p id="cent">
<b>What is included?</b><br />
• sorting<br />
• missing enchant/gem filter<br />
• relative colorization of many columns<br />
• individual raid progress<br />
• graphs for all tracked characters aswell as the guild as whole<br />
<br />
<b>Who needs this?</b><br />
• any active raiding guild, especially on mythic<br />
• loot councils
<p id="cent"><b>Try it out below!</b><br />Your guild has been wrongfully claimed? Write me a mail to <a href="mailto:xepheris.dh.tank@gmail.com">xepheris.dh.tank@gmail.com</a>!</p>	
<form action="" method="POST" id="cent">
<input type="text" name="gn" value="" placeholder="guild name case sensitive" />
<input type="password" name="gpw" value="" placeholder="password" />
<select name="r" id="r"><option value="EU">EU</option><option value="US">US</option></select>
<select name="s" id="s">
</select>
<button type="submit">Insert</button>
</form>';

?>