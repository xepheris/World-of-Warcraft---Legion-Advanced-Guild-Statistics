<?php

if($count_rows['users'] == '' || $count_rows['users'] == '0') {
	echo '<p id="cent">Welcome! You have no characters listed yet.</p>';
}

$guild = $_SESSION['g'];
if(strpos($guild, ' ') !== false) {
	$guild = str_replace(' ', '%20', $guild);
}
$server = $_SESSION['s'];
if(strpos($server, ' ') !== false) {
	$server = str_replace(' ', '-', $server);
}

echo '<p id="cent">Select characters you would like to import:</p>
<form action="" method="POST" style="text-align: center;">
<select multiple name="c[]" style="width: 250px; height: 250px;">';
$url = 'https://' .$_SESSION['r']. '.api.battle.net/wow/guild/' .$server. '/' .$guild. '?fields=members&locale=en_GB&apikey=KEY_HERE';

$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));
if($data != '') {
	$data = json_decode($data, true);
	$chararray = array();
			
	for($rank = '0'; $rank <= '9'; $rank++) {
		${'' .$rank. 'array'} = array();
		foreach($data['members'] as $members) {
			if($members['rank'] == $rank) {
				if($members['character']['level'] == '110') {
					array_push(${'' .$rank. 'array'}, $members['character']['name']);
				}
			}
		}
	
		sort(${'' .$rank. 'array'});
		
		echo '<optgroup label="Guildrank ' .($rank+1). '">';
	
		foreach(${'' .$rank. 'array'} as $char) {
			echo '<option value="' .$char. '">' .$char. '</option>';
		}
		
		echo '</optgroup>';
		
	}
}
	
echo '</select><br />
<button type="submit">Import</button>
</form>
<hr>
<form action="" method="POST">
<p id="cent">Import characters based on guild rank (1 = guild leader only, 10 = whole guild - <span style="color: red;">WARNING:</span> 10 takes ages on big guilds, use with caution!)
<select name="gr">';
for($i = '1'; $i <= '10'; $i++) {
	echo '<option value="' .($i-1). '">' .$i. ' rank(s)</option>';
}
echo '</select><button type="submit">Import</button></p>
</form>
<hr>
<form action="" method="POST">
<p id="cent">Import specific characters only (separated by commas, case-sensitive)
	<input type="text" name="c" placeholder="a,b,c,d" />
<button type="submit">Import</button>
</form>';


?>