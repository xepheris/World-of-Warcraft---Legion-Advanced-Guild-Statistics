<?php

include('dbcon.php');

$timestamp = time('now')-14*24*60*60;

$zu_alt = mysqli_query($stream, "SELECT `id` FROM `guilds` WHERE `l` <= '" .$timestamp. "'");
	
while($guild = mysqli_fetch_array($zu_alt)) {
	
	echo $guild['id']; echo '<br />';
	
	mysqli_query($stream, "DROP TABLE `" .$guild['id']. "`");
	mysqli_query($stream, "DROP TABLE `" .$guild['id']. "_archive`");
	mysqli_query($stream, "DELETE FROM `guilds` WHERE `id` = '" .$guild['id']. "'");
	
}

?>