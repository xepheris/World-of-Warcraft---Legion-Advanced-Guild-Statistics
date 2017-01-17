<?php

if(!isset($_SESSION['guest'])) {
	include('dbcon.php');
	$name = mysqli_fetch_array(mysqli_query($stream, "SELECT `ch` FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$_GET['r']. "'"));
	
	mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$name['ch']. "'");
	mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$name['ch']. "'");
	mysqli_query($stream, "DELETE FROM `gg` WHERE `char` = '" .$name['ch']. "'");
}

?>