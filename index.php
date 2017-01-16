<?php
session_start();
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Xepheris (EU-Blackmoore)" />
<meta name="robots" content="index, nofollow" />
<meta name="language" content="en" />
<meta name="description" content="Advanced Guild Statistics including Artifact Power, Level, Mythics done, Highest M+, Equip comparison including tooltips, relative colorization and many more functions!" />
<meta name="keywords" lang="en" content="advanced guild statistics, guild, statistics, artifact power, artifact level, equip, wow, legion, addon, expansion, tracking, loot, council, loot council" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
<title>Advanced Guild Statistics</title>
</head>
<body>
<div id="content">';


if(isset($_GET['sl']) && strlen($_GET['sl']) == '32') {
	include('mod/dbcon.php');	
	$correct = mysqli_fetch_array(mysqli_query($stream, "SELECT `id`, `g`, `r`, `s` FROM `guilds` WHERE `sl` = '" .$_GET['sl']. "'"));
	
	if(!empty($correct['g'])) {
		$_SESSION['t'] = $correct['id'];
		$_SESSION['g'] = $correct['g'];
		$_SESSION['r'] = $correct['r'];
		$_SESSION['s'] = $correct['s'];
		$_SESSION['guest'] = '1';
	}	
}

if(isset($_GET['die'])) {
	$_SESSION['g'] = '';
	unset($_SESSION['g']);
	session_destroy();
	echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
}

if(isset($_GET['sh']) && is_numeric($_GET['sh']) && $_GET['sh'] <= '1' && $_GET['sh'] >= '0') {
	if($_GET['sh'] == '0') {
		$_SESSION['showhide'] = '0';
	}
	if($_GET['sh'] == '1') {
		$_SESSION['showhide'] = '1';
	}
}

if(isset($_GET['showequip']) && is_numeric($_GET['showequip']) && $_GET['showequip'] <= '1' && $_GET['showequip'] >= '0') {
	if($_GET['showequip'] == '0') {
		$_SESSION['showequip'] = '0';
	}
	if($_GET['showequip'] == '1') {
		$_SESSION['showequip'] = '1';
	}
}

if($_SESSION['showequip'] == '1') {
	echo '<link rel="stylesheet" href="css/core-weq.css" />';
}
elseif(!isset($_SESSION['showequip']) || $_SESSION['showequip'] == '0') {
	echo '<link rel="stylesheet" href="css/core.css" />';
}

if(isset($_GET['g']) && isset($_SESSION['g'])) {
	include('mod/graph.php');
}

if(isset($_POST['g']) && isset($_POST['cd'])) {
	include('mod/verifylogin.php');
}

if(isset($_POST['gn']) && isset($_POST['gpw']) && isset($_POST['r']) && isset($_POST['s'])) {
	include('mod/processreg.php');
}

if(!isset($_SESSION['g'])) {
	include('mod/login.php');
}
elseif(isset($_SESSION['g']) && !isset($_GET['g'])) {
	include('mod/core.php');
}


echo '</div>
</body>
</html>';
?>


<script type="text/javascript">
server_EU=new Array("Aegwynn","Aerie Peak","Agamaggan","Aggra","Aggramar","Ahn'Qiraj","Al'Akir","Alexstrasza","Alleria","Alonsus","Aman'Thul","Ambossar","Anachronos","Anetheron","Antonidas","Anub'arak","Arak-arahm","Arathi","Arathor","Archimonde","Area 52","Argent Dawn","Arthas","Arygos","Aszune","Auchindoun","Azjol-Nerub","Azshara","Azuremyst","Baelgun","Balnazzar","Blackhand","Blackmoore","Blackrock","Blade's Edge","Bladefist","Bloodfeather","Bloodhoof","Bloodscalp","Blutkessel","Boulderfist","Bronze Dragonflight","Bronzebeard","Burning Blade","Burning Legion","Burning Steppes","C'Thun","Chamber of Aspects","Chants \u00e9ternels","Cho'gall","Chromaggus","Colinas Pardas","Confr\u00e9rie du Thorium","Conseil des Ombres","Crushridge","Culte de la Rive Noire","Daggerspine","Dalaran","Dalvengyr","Darkmoon Faire","Darksorrow","Darkspear","Das Konsortium","Das Syndikat","Deathwing","Defias Brotherhood","Dentarg","Der abyssische Rat","Der Mithrilorden","Der Rat von Dalaran","Destromath","Dethecus","Die Aldor","Die Arguswacht","Die ewige Wacht","Die Nachtwache","Die Silberne Hand","Die Todeskrallen","Doomhammer","Draenor","Dragonblight","Dragonmaw","Drak'thul","Drek'Thar","Dun Modr","Dun Morogh","Dunemaul","Durotan","Earthen Ring","Echsenkessel","Eitrigg","Eldre'Thalas","Elune","Emerald Dream","Emeriss","Eonar","Eredar","Euskal Encounter","Executus","Exodar","Festung der St\u00fcrme","Forscherliga","Frostmane","Frostmourne","Frostwhisper","Frostwolf","Garona","Garrosh","Genjuros","Ghostlands","Gilneas","Gorgonnash","Grim Batol","Gul'dan","Hakkar","Haomarush","Hellfire","Hellscream","Hyjal","Illidan","Jaedenar","Kael'Thas","Karazhan","Kargath","Kazzak","Kel'Thuzad","Khadgar","Khaz Modan","Khaz'goroth","Kil'Jaeden","Kilrogg","Kirin Tor","Kor'gall","Krag'jin","Krasus","Kul Tiras","Kult der Verdammten","La Croisade \u00e9carlate","Laughing Skull","Les Clairvoyants","Les Sentinelles","Lightbringer","Lightning's Blade","Lordaeron","Los Errantes","Lothar","Madmortem","Magtheridon","Mal'Ganis","Malfurion","Malorne","Malygos","Mannoroth","Mar\u00e9cage de Zangar","Mazrigos","Medivh","Minahonda","Molten Core","Moonglade","Mug'thol","Nagrand","Nathrezim","Naxxramas","Nazjatar","Nefarian","Nemesis","Neptulon","Ner'zhul","Nera'thor","Nethersturm","Nordrassil","Norgannon","Nozdormu","Onyxia","Outland","Perenolde","Pozzo dell'Eternit\u00e0","Proudmoore","Quel'Thalas","Ragnaros","Rajaxx","Rashgarroth","Ravencrest","Ravenholdt","Rexxar","Runetotem","Sanguino","Sargeras","Saurfang","Scarshield Legion","Sen'jin","Shadowmoon","Shadowsong","Shattered Halls","Shattered Hand","Shattrath","Shen'dralar","Silvermoon","Sinstralis","Skullcrusher","Spinebreaker","Sporeggar","Steamwheedle Cartel","Stonemaul","Stormrage","Stormreaver","Stormscale","Sunstrider","Suramar","Sylvanas","Taerar","Talnivarr","Tarren Mill","Teldrassil","Temple noir","Terenas","Terokkar","Terrordar","The Maelstrom","The Sha'tar","The Venture Co","Theradras","Thrall","Throk'Feroth","Thunderhorn","Tichondrius","Tirion","Todeswache","Trollbane","Turalyon","Twilight's Hammer","Twisting Nether","Tyrande","Uldaman","Uldum","Un'Goro","Varimathras","Vashj","Vek'lor","Vek'nilash","Vol'jin","Warsong","Wildhammer","Wrathbringer","Xavius","Ysera","Ysondre","Zenedar","Zirkel des Cenarius","Zul'jin","Zuluhed","\u0410\u0437\u0443\u0440\u0435\u0433\u043e\u0441","\u0411\u043e\u0440\u0435\u0439\u0441\u043a\u0430\u044f \u0442\u0443\u043d\u0434\u0440\u0430","\u0412\u0435\u0447\u043d\u0430\u044f \u041f\u0435\u0441\u043d\u044f","\u0413\u0430\u043b\u0430\u043a\u0440\u043e\u043d\u0434","\u0413\u043e\u043b\u0434\u0440\u0438\u043d\u043d","\u0413\u043e\u0440\u0434\u0443\u043d\u043d\u0438","\u0413\u0440\u043e\u043c","\u0414\u0440\u0430\u043a\u043e\u043d\u043e\u043c\u043e\u0440","\u041a\u043e\u0440\u043e\u043b\u044c-\u043b\u0438\u0447","\u041f\u0438\u0440\u0430\u0442\u0441\u043a\u0430\u044f \u0431\u0443\u0445\u0442\u0430","\u041f\u043e\u0434\u0437\u0435\u043c\u044c\u0435","\u0420\u0430\u0437\u0443\u0432\u0438\u0439","\u0420\u0435\u0432\u0443\u0449\u0438\u0439 \u0444\u044c\u043e\u0440\u0434","\u0421\u0432\u0435\u0436\u0435\u0432\u0430\u0442\u0435\u043b\u044c \u0414\u0443\u0448","\u0421\u0435\u0434\u043e\u0433\u0440\u0438\u0432","\u0421\u0442\u0440\u0430\u0436 \u0421\u043c\u0435\u0440\u0442\u0438","\u0422\u0435\u0440\u043c\u043e\u0448\u0442\u0435\u043f\u0441\u0435\u043b\u044c","\u0422\u043a\u0430\u0447 \u0421\u043c\u0435\u0440\u0442\u0438","\u0427\u0435\u0440\u043d\u044b\u0439 \u0428\u0440\u0430\u043c","\u042f\u0441\u0435\u043d\u0435\u0432\u044b\u0439 \u043b\u0435\u0441");
server_US=new Array("Aegwynn","Aerie Peak","Agamaggan","Aggramar","Akama","Alexstrasza","Alleria","Altar of Storms","Alterac Mountains","Aman'Thul","Andorhal","Anetheron","Antonidas","Anub'arak","Anvilmar","Arathor","Archimonde","Area 52","Argent Dawn","Arthas","Arygos","Auchindoun","Azgalor","Azjol-Nerub","Azralon","Azshara","Azuremyst","Baelgun","Balnazzar","Barthilas","Black Dragonflight","Blackhand","Blackrock","Blackwater Raiders","Blackwing Lair","Blade's Edge","Bladefist","Bleeding Hollow","Blood Furnace","Bloodhoof","Bloodscalp","Bonechewer","Borean Tundra","Boulderfist","Bronzebeard","Burning Blade","Burning Legion","Caelestrasz","Cairne","Cenarion Circle","Cenarius","Cho'gall","Chromaggus","Coilfang","Crushridge","Daggerspine","Dalaran","Dalvengyr","Dark Iron","Darkspear","Darrowmere","Dath'Remar","Dawnbringer","Deathwing","Demon Soul","Dentarg","Destromath","Dethecus","Detheroc","Doomhammer","Draenor","Dragonblight","Dragonmaw","Drak'tharon","Drak'thul","Draka","Drakkari","Dreadmaul","Drenden","Dunemaul","Durotan","Duskwood","Earthen Ring","Echo Isles","Eitrigg","Eldre'Thalas","Elune","Emerald Dream","Eonar","Eredar","Executus","Exodar","Farstriders","Feathermoon","Fenris","Firetree","Fizzcrank","Frostmane","Frostmourne","Frostwolf","Galakrond","Gallywix","Garithos","Garona","Garrosh","Ghostlands","Gilneas","Gnomeregan","Goldrinn","Gorefiend","Gorgonnash","Greymane","Grizzly Hills","Grizzly Hills","Gul'dan","Gundrak","Gurubashi","Hakkar","Haomarush","Hellscream","Hydraxis","Hyjal","Icecrown","Illidan","Jaedenar","Jubei'Thos","Kael'thas","Kalecgos","Kargath","Kel'Thuzad","Khadgar","Khaz Modan","Khaz'goroth","Kil'Jaeden","Kilrogg","Kirin Tor","Korgath","Korialstrasz","Kul Tiras","Laughing Skull","Lethon","Lightbringer","Lightning's Blade","Lightninghoof","Llane","Lothar","Madoran","Maelstrom","Magtheridon","Maiev","Mal'Ganis","Malfurion","Malorne","Malygos","Mannoroth","Medivh","Misha","Mok'Nathal","Moon Guard","Moonrunner","Mug'thol","Muradin","Nagrand","Nathrezim","Nazgrel","Nazjatar","Nemesis","Ner'zhul","Nesingwary","Nordrassil","Norgannon","Onyxia","Perenolde","Proudmoore","Quel'Dorei","Quel'Thalas","Ragnaros","Ravencrest","Ravenholdt","Rexxar","Rivendare","Runetotem","Sargeras","Saurfang","Scarlet Crusade","Scilla","Sen'Jin","Sentinels","Shadow Council","Shadowmoon","Shadowsong","Shandris","Shattered Halls","Shattered Hand","Shu'Halo","Silver Hand","Silvermoon","Sisters of Elune","Skullcrusher","Skywall","Smolderthorn","Spinebreaker","Spirestone","Staghelm","Steamwheedle Cartel","Stonemaul","Stormrage","Stormreaver","Stormscale","Suramar","Tanaris","Terenas","Terokkar","Thaurissan","The Forgotten Coast","The Scryers","The Underbog","The Venture Co","Thorium Brotherhood","Thrall","Thunderhorn","Thunderlord","Tichondrius","Tol Barad","Tortheldrin","Trollbane","Turalyon","Twisting Nether","Uldaman","Uldum","Undermine","Ursin","Uther","Vashj","Vek'nilash","Velen","Warsong","Whisperwind","Wildhammer","Windrunner","Winterhoof","Wyrmrest Accord","Ysera","Ysondre","Zangarmarsh","Zul'jin","Zuluhed");
		
populateSelect();
			
$(function() {
	$('#r').change(function(){
			populateSelect();
		});
	});
			
	function populateSelect(){
		region=$('#r').val();
		$('#s').html('');
		
		if(region=='EU'){
			server_EU.forEach(function(t) { 
				$('#s').append('<option>'+t+'</option>');
			});
		}
		
		if(region=='US'){
			server_US.forEach(function(t) {
				$('#s').append('<option>'+t+'</option>');
			});
		}
}
</script>