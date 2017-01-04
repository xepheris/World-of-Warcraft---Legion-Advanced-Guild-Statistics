<?php


// ENCRYPT USER
$g = $_POST['g'];
$cd = md5($_POST['cd']);

include('dbcon.php');

// CHECK USER AGAINST DB

$check = mysqli_fetch_array(mysqli_query($stream, "SELECT `g`, `r`, `s` FROM `guilds` WHERE `id` = '" .$g. "' AND `p` = '" .$cd. "'"));

if(!empty($check)) {
	
	// SESSION GUILD
	$_SESSION['g'] = $check['g'];
	$_SESSION['t'] = $g;
	$_SESSION['s'] = $check['s'];
	$_SESSION['r'] = $check['r'];
	
	// LOG
	$log = mysqli_query($stream, "UPDATE `guilds` SET `l` = '" .time('now'). "' WHERE `id` = '" .$g. "'");
	 
}
else {
	echo '<p id="error">Password incorrect.</p>';
}
	
?>

