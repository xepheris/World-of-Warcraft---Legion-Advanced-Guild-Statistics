<?php

echo '<body style="background-color: #7e7e7e; margin: 0 auto;">';

$guides = array('01_Skorpyron.png' => '01.png',
				'02_ChromAnom.png' => '02.png',
				'03_Trilliax.png' => '03.png',
				'04_SpellbladeAluriel.png' => '04.png',
				'05_StarAugurEtraeus.png' => '05.png',
				'06_Krosus.png' => '06.png',
				'07_HighBotTelarn.png' => '07.png',
				'08_Tichondrius.png' => '08.png',
				'09_Elisande.png' => '09.png');

foreach($guides as $img => $preview) {
	echo '<div style="float: left;">
	<a href="' .$img. '" alt="404"><img src="' .$preview. '" alt="404" /></a>
	</div>';
}

echo '</body>';


?>