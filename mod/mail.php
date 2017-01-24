<?php

echo '<p style="color: red; text-align: center;">Please do not misuse!<br />If you wish a reply, please consider manually writing a mail to <a href="mailto:xepheris.dh.tank@gmail.com">xepheris.dh.tank@gmail.com</a>.</p>';

if(!isset($_POST['mail'])) {
	echo '<div style="text-align: center;">
	<form action="" method="POST">
	<p style="color: orange; text-align: center;">Subject: Ticket - ' .$_SESSION['g']. ' (' .$_SESSION['r']. '-' .$_SESSION['s']. ')</p>
	<input type="text" name="subject" value="Ticket - ' .$_SESSION['t']. ' - ' .$_SESSION['g']. ' (' .$_SESSION['r']. '-' .$_SESSION['s']. ')" required readonly hidden />
	<textarea type="text" name="mail" style="width: 500px; height: 250px;" placeholder="if you wish to include images, please upload them to an external source first, for example imgur.com"></textarea>
	<br />
	<button type="submit">Send</button>
	</div>';
}
elseif(isset($_POST['mail'])) {
	
	require('PHPMailer5216/class.phpmailer.php');
	require('PHPMailer5216/class.pop3.php');

	$mail = new PHPMailer;

	$mail->CharSet = 'UTF-8';
	
	$mail->setFrom('no-reply@artifactpower.info', 'Advanced Guild Statistics');
	
	$mail->Subject = '' .$_POST['subject']. '';
	
	$mail->addAddress('xepheris.dh.tank@gmail.com');

	$mail->msgHTML('<!doctype html>
	<html>
	<head>
	<style type="text/css">
	#t {
		display: table;
		border-collapse:collapse; 
		}

	#tr {
		display: table-row;
		}

	#td {
		display: table-cell;
		padding: 3px; 
		}
		</style>
		<meta charset="UTF-8">
		</head>

	<body style="background-color: #DCD0C0; color: #373737;">
	<div id="t">
		<div id="tr">
			<div id="td"><span style="font-size: 17px;">Hey dev,</span></div>
		</div>
		<div id="tr">
			<div id="td"><br />' .$_POST['mail']. '</div>
		</div>	
		<div id="tr">
			<div id="td"><span style="font-size: 17px;"><br />- ' .$_SESSION['g']. ' (' .$_SESSION['r']. '-' .$_SESSION['s']. ')</span></div>
		</div>
	</div>
	</body>
	</html>
	');
	
	if(!$mail->send()) {
    	echo '<p style="color: red;">Mail failed to send:<br />' . $mail->ErrorInfo. '</p>';
	}
	else {
   		echo '<p style="color: green; text-align: center;">Your mail has been sent, thanks! Will be looking into it shortly.</p>';
	}
}

?>