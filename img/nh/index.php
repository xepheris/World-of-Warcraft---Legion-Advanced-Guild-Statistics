<style type="text/css">
	div:hover {
		opacity: 0.6;
		transition: 0.2s ease all;
	}
</style>
	


<?php

echo '<body style="background-color: #7e7e7e; margin: 0 auto;">';

$guides = array('01_Skorpyron.jpg', '02_ChromAnom.jpg', '03_Trilliax.jpg', '04_SpellbladeAluriel.jpg', '05_StarAugurEtraeus.jpg', '06_Krosus.jpg', '07_HighBotTelarn.jpg', '08_Tichondrius.jpg', '09_Elisande.jpg', '10_Guldan.jpg');

foreach($guides as $img) {
	echo '<span title="credits to KREMLING KREW https://www.kremlingkrew.com/heroic-nighthold-guide/"><div style="float: left; width: 33.33%;">
	<a href="' .$img. '" alt="404"><img src="' .$img. '" alt="LOADING. IF YOU STILL SEE THIS AFTER MORE THAN 10 SECONDS, REFRESH THE PAGE" style="width: 100%;"/></a>
	</div></span>';
}

echo '</body>';


?>