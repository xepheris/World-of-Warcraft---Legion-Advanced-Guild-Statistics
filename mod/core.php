<?php

if($_SESSION['showequip'] == '1') {
	?>
	<script defer src="http://wow.zamimg.com/widgets/power.js"></script>
	<script>
	var wowhead_tooltips = {
		"hide": {
			"droppedby": true,
			"dropchance": true,
			"sellprice": true,
			"maxstack": true,
			"iconizelinks": true		
		}
	}
	</script>
	<?php
}

if(isset($_GET['r']) && is_numeric($_GET['r'])) {
	include('remover.php');
}

if($_GET['u'] == 'all') {
	require_once('func.php');
	include('dbcon.php');
	$current_chars = mysqli_query($stream, "SELECT DISTINCT(`ch`) FROM `" .$_SESSION['t']. "`");
	while($char = mysqli_fetch_array($current_chars)) {
		import($char['ch']);
	}
}

if(isset($_GET['sett'])) {
	include('settings.php');
}

if(isset($_GET['u']) && is_numeric($_GET['u'])) {
	require_once('func.php');
	include('dbcon.php');
	$char = mysqli_fetch_array(mysqli_query($stream, "SELECT `ch` FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$_GET['u']. "'"));
	import($char['ch']);
	echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
}

if(!empty($_SESSION['g'])) {
	include('top.php');		
}
		
if(isset($_POST['gr'])) {
	include('guildrankimport.php');
}

if(isset($_GET['mail'])) {
	include('mail.php');
	die();
}
	
if(isset($_POST['c'])) {
	
	foreach($_POST['c'] as $char) {
		require_once('func.php');
		import($char);
	}
	
	if(strpos($_POST['c'], ',') !== FALSE) {
		$chars = explode(',', $_POST['c']);
		foreach($chars as $char) {
			$char = str_replace(' ', '', $char);
			require_once('func.php');
			import($char);		
		}
	}
	elseif(strpos($_POST['c'], ',') === FALSE) {
		$char = $_POST['c'];
		require_once('func.php');
		import($char);
	}
	echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/" />';
}
	
if(isset($_GET['i'])) {
	include('setup.php');
}
	
if(!isset($_GET['i']) || isset($_GET['sl'])) {			
	if($count_rows['users'] == '' || $count_rows['users'] == '0') {
		if(!isset($_SESSION['guest'])) {
			echo '<meta http-equiv="refresh" content="0;url=http://guild.artifactpower.info/?i" />';
		}
		elseif(isset($_SESSION['guest'])) {
			echo '<p style="color: red; text-align: center;">Sorry, this guild has no characters listed yet and you may not import as a guest.</p>';
		}
	}	
	else {
		echo '<div class="t">
		<div class="tb">
		<div class="tr">';
		
		if(!isset($_GET['s']) || substr($_GET['s'], '-1') == '2') {
			$num = '1';
		}
		elseif(substr($_GET['s'], '-1') == '1') {
			$num = '2';
		}		
		$columnarray = array('' => '', 'Class' => 'cl' .$num. '', 'Role' => 'ro' .$num. '', 'Total AP' => 'to' .$num. '', 'Artifact Level (AK)' => 'al' .$num. '', 'Equipped' => 'eq' .$num. '', 'Bags' => 'ba' .$num. '', 'Weapon' => 'wa' .$num. '', 'Head' => 'he' .$num. '', 'Neck' => 'ne' .$num. '', 'Shoulder' => 'sh' .$num. '', 'Back' => 'ba' .$num. '', 'Chest' => 'ch' .$num. '', 'Wrist' => 'wr' .$num. '', 'Hands' => 'ha' .$num. '', 'Waist' => 'wi' .$num. '', 'Legs' => 'le' .$num. '', 'Feet' => 'fe' .$num. '', 'Ring1' => 'r1' .$num. '', 'Ring2' => 'r2' .$num. '', 'Trinket1' => 't1' .$num. '', 'Trinket2' => 't2' .$num. '', 'Mythics' => 'my' .$num. '', 'Highest M+' => 'mp' .$num. '', 'EN' => 'en' .$num. '', 'ToV' => 'tov' .$num. '', 'NH' => 'nh' .$num. '', '' => '', '' => '');
			
		foreach($columnarray as $column => $sort) {
			if($column == 'Head' || $column == 'Neck' || $column == 'Shoulder' || $column == 'Back' || $column == 'Chest' || $column == 'Wrist' || $column == 'Hands' || $column == 'Waist' || $column == 'Legs' || $column == 'Feet' || $column == 'Ring1' || $column == 'Ring2' || $column == 'Trinket1' || $column == 'Trinket2') {
				echo '<div class="tc" id="equip" style="border-bottom: 1px solid grey;"><a href="?s=' .$sort. '">' .$column. '</a></div>';
			}
			else {
				echo '<div class="tc" style="border-bottom: 1px solid grey;"><a href="?s=' .$sort. '">' .$column. '</a></div>';
			}
		}
		echo '</div>
		</div>';
		
		
		if(!isset($_GET['fs']) && !isset($_GET['fc'])) {
			if(isset($_GET['s'])) {
				if(substr($_GET['s'], '0', '2') == 'cl') { $sortby = '`c`'; }
				if(substr($_GET['s'], '0', '2') == 'ro') { $sortby = '`s`'; }
				if(substr($_GET['s'], '0', '2') == 'to') { $sortby = '`ap`'; }
				if(substr($_GET['s'], '0', '2') == 'al') { $sortby = '`alvl`'; }
				if(substr($_GET['s'], '0', '2') == 'eq') { $sortby = '`ilvlavg`'; }
				if(substr($_GET['s'], '0', '2') == 'ba') { $sortby = '`ilvlbags`'; }
				if(substr($_GET['s'], '0', '2') == 'wa') { $sortby = '`mh_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'he') { $sortby = '`he_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'ne') { $sortby = '`n_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'sh') { $sortby = '`s_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'bh') { $sortby = '`b_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'ch') { $sortby = '`c_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'wr') { $sortby = '`wr_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'ha') { $sortby = '`ha_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'wi') { $sortby = '`wa_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'le') { $sortby = '`l_ilvl`'; }			
				if(substr($_GET['s'], '0', '2') == 'fe') { $sortby = '`f_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'r1') { $sortby = '`f1_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'r2') { $sortby = '`f2_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 't1') { $sortby = '`t1_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 't2') { $sortby = '`t2_ilvl`'; }
				if(substr($_GET['s'], '0', '2') == 'my') { $sortby = '`sum`'; }
				if(substr($_GET['s'], '0', '2') == 'mp') { $sortby = '`mplus`'; }
				if(substr($_GET['s'], '0', '2') == 'en') { $sortby = '`en`'; }
				if(substr($_GET['s'], '0', '3') == 'tov') { $sortby = '`tov`'; }
				if(substr($_GET['s'], '0', '2') == 'nh') { $sortby = '`nh`'; }
				
						
				if(substr($_GET['s'], '-1') == '1') { $sortby.= ' DESC'; }
				elseif(substr($_GET['s'], '-1') == '2') { $sortby.= ' ASC'; }			
			}
		
			if(!isset($_GET['s'])) {
				$sortby = '`ch` ASC';
			}
		
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` ORDER BY " .$sortby. "");
		}
		elseif(isset($_GET['fs'])) {
			$classspecc = mysqli_fetch_array(mysqli_query($stream, "SELECT `id`, `s` FROM `weapons` WHERE `w` = '" .$_GET['fs']. "'"));
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` WHERE `s` = '" .$classspecc['s']. "' AND `c` = '" .$classspecc['id']. "' ORDER BY `ch` ASC");			
		}
		elseif(isset($_GET['fc'])) {
			$characterdata = mysqli_query($stream, "SELECT * FROM `" .$_SESSION['t']. "` WHERE `c` = '" .$_GET['fc']. "' ORDER BY `c` ASC");
		}

		$server = $_SESSION['s'];
		if(strpos($server, ' ') !== false) {
			$server = str_replace(' ', '-', $server);
		}
		
		$rowarray = array('he', 'n', 's', 'b', 'c', 'wr', 'ha', 'wa', 'l', 'f', 'f1', 'f2', 't1', 't2');
		foreach($rowarray as $max) {
			${'' .$max. '_color'} = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`" .$max. "_ilvl`) AS `" .$max. "_max` FROM `" .$_SESSION['t']. "`"));
		}
		
		$apcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ap`) AS `apcap` FROM `" .$_SESSION['t']. "`"));
		$alvlcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`alvl`) AS `alvlcap` FROM `" .$_SESSION['t']. "`"));
		$eqcap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ilvlavg`) AS `avgcap` FROM `" .$_SESSION['t']. "`"));
		$bagscap = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ilvlbags`) AS `bagmax` FROM `" .$_SESSION['t']. "`"));
			
		$num = '0';		
		while($data = mysqli_fetch_array($characterdata)) {
			if ($num % 2 != 0) {
				$style = 'style="background-color: darkslategrey;"';
			}
			else {
				$style = '';
			}

			$compare_old = mysqli_fetch_array(mysqli_query($stream, "SELECT `ap`, `sum`, `ilvlavg`, `ilvlbags`, `alvl` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$data['ch']. "' ORDER BY `lupd` DESC LIMIT 1"));
			
			if(!empty($compare_old)) {
				
				if(($data['ap']-$compare_old['ap']) != '0') { $ap_incr = '<span style="color: grey;">(+' .(round($data['ap']/$compare_old['ap']-1, 3)*100). '%)</span>'; }
				if(($data['sum']-$compare_old['sum']) != '0') { $sum_old = '<span style="color: grey;">(+' .($data['sum']-$compare_old['sum']). ')</span>'; }
				if(($data['ilvlavg']-$compare_old['ilvlavg']) != '0') { $ilvlavg_old = '<span style="color: grey;">(+' .($data['ilvlavg']-$compare_old['ilvlavg']). ')</span>'; }
				if(($data['ilvlbags']-$compare_old['ilvlbags']) != '0') { $bags_old = '<span style="color: grey;">(+' .($data['ilvlbags']-$compare_old['ilvlbags']). ')</span>'; }
				if(($data['alvl']-$compare_old['alvl']) != '0') { $alvl_old = '<span style="color: grey;">(+' .($data['alvl']-$compare_old['alvl']). ')</span>'; }
			}
			
			if(time('now')-$data['lupd'] > '86400') { $update = 'style="color: red; font-size: 10px;"'; }
			elseif(time('now')-$data['lupd'] < '86400' && time('now')-$data['lupd'] >= '43200') { $update = 'style="color: orange; font-size: 10px;"'; }
			elseif(time('now')-$data['lupd'] < '43200') { $update = 'style="color: green; font-size: 10px;"'; }
			
			
			$weapon_id = mysqli_fetch_array(mysqli_query($stream, "SELECT `w` FROM `weapons` WHERE `s` = '" .$data['s']. "' AND `id` = '" .$data['c']. "'"));
			$class = mysqli_fetch_array(mysqli_query($stream, "SELECT `class`, `color` FROM `classes` WHERE `id` = '" .$data['c']. "'"));
			
			if($data['r'] == '') {
				$data['r'] = $_SESSION['s'];
			}
			echo '<div class="tr" ' .$style. '>
			<div class="tc"><a href="http://' .$_SESSION['r']. '.battle.net/wow/en/character/' .$data['r']. '/' .$data['ch']. '/simple" title="Logged out: ' .round(((time('now')-$data['llog'])/3600), 2). ' hrs. ago – Last update: ' .round(((time('now')-$data['lupd'])/3600), 2). ' hrs. ago">' .$data['ch']. '</a> <span ' .$update. '>upd: ' .round(((time('now')-$data['lupd'])/3600), 2). ' hrs. ago <a href="http://www.wowprogress.com/character/' .$_SESSION['r']. '/' .$data['r']. '/' .$data['ch']. '"><img src="img/wpr.ico" alt="404" /></a> <a href="http://check.artifactpower.info/?r=' .$_SESSION['r']. '&s=' .$data['r']. '&c=' .$data['ch']. '"><img src="img/aaa.png" alt="404" /></a></div>
			<div class="tc" style="background:' .$class['color']. ';"><a href="?fc=' .$data['c']. '">' .$class['class']. '</a></div>
			<div class="tc"><a href="?fs=' .$weapon_id['w']. '">' .$data['s']. '</a></div>';
			
			$thresholds = mysqli_fetch_array(mysqli_query($stream, "SELECT `ap_low`, `ap_high`, `g_low`, `g_high` FROM `guilds` WHERE `id` = '" .$_SESSION['t']. "'"));
			
			$apcheck = round(($data['ap'])/($apcap['apcap']), 2);
			if($apcheck >= $thresholds['ap_high']) { $ap = 'style="color: green;"'; }
			if($apcheck >= $thresholds['ap_low'] && $apcheck < $thresholds['ap_high']) { $ap = 'style="color: orange;"'; }
			if($apcheck < $thresholds['ap_low']) { $ap = 'style="color: red;"'; }
			
			$alvlcheck = round(($data['alvl'])/($alvlcap['alvlcap']), 2);
			if($alvlcheck >= '0.8') { $alvl = 'style="color: green;"'; }
			if($alvlcheck >= '0.6' && $alvlcheck < '0.8') { $alvl = 'style="color: orange;"'; }
			if($alvlcheck < '0.6') { $alvl = 'style="color: red;"'; }
			
			$avgcheck = round(($data['ilvlavg']-800)/($eqcap['avgcap']-800), 2);
			if($avgcheck >= $thresholds['g_high']) { $avg = 'style="color: green;"'; }
			if($avgcheck >= $thresholds['g_low'] && $avgcheck < $thresholds['g_high']) { $avg = 'style="color: orange;"'; }
			if($avgcheck < $thresholds['g_low']) { $avg = 'style="color: red;"'; }
				
			$bagcheck = round(($data['ilvlbags']-800)/($bagscap['bagmax']-800), 2);
			if($bagcheck >= $thresholds['g_high']) { $bags = 'style="color: green;"'; }
			if($bagcheck >= $thresholds['g_low'] && $bagcheck < $thresholds['g_high']) { $bags = 'style="color: orange;"'; }
			if($bagcheck < $thresholds['g_low']) { $bags = 'style="color: red;"'; }				
			
			echo '<div class="tc"><span ' .$ap. '>' .number_format($data['ap']). ' ' .$ap_incr. '</span></div>
			<div class="tc"><span ' .$alvl. '>' .$data['alvl']. ' ' .$alvl_old. ' (' .$data['ak']. ')</span></div>
			<div class="tc"><span ' .$avg. '>' .$data['ilvlavg']. ' ' .$ilvlavg_old. '</span></div>
			<div class="tc"><span ' .$bags. '>' .$data['ilvlbags']. ' ' .$bags_old. '</span></div>';
			
			if(empty($data['oh_bonus'])) {
				$weapon = '<a href="http://wowhead.com/item=' .$weapon_id['w']. '&bonus=' .$data['mh_b']. '" rel="gems=' .$data['mh_r1']. ':' .$data['mh_r2']. ':' .$data['mh_r3']. '">' .$data['mh_ilvl']. '</a>';
			}
			else {
				$weapon = '<a href="http://wowhead.com/item=' .$weapon_id['w']. '&bonus=' .$data['oh_bonus']. '" rel="gems=' .$data['oh_r1']. ':' .$data['oh_r2']. ':' .$data['oh_r3']. '">' .$data['oh_ilvl']. '</a>';
			}
			
			echo '<div class="tc">' .$weapon. '</div>';
			$rowarray = array('he', 'n', 's', 'b', 'c', 'wr', 'ha', 'wa', 'l', 'f', 'f1', 'f2', 't1', 't2');
			foreach($rowarray as $row) {
							
				$qualitycheck = round(($data['' .$row. '_ilvl']-800)/(${'' .$row. '_color'}['' .$row. '_max']-800), 2);			
				if($qualitycheck >= '0.8') { $quality = 'style="color: green;"'; }
				if($qualitycheck >= '0.6' && $qualitycheck < '0.8') { $quality = 'style="color: orange;"'; }
				if($qualitycheck < '0.6') { $quality = 'style="color: red;"'; }
				
							
				if(!isset($_SESSION['showhide']) || $_SESSION['showhide'] == '0') {
					$socketcheck = strpos($data['' .$row. '_b'], '1808');
					if($data['' .$row. '_g'] != '0') {
						$swaparray = array('130218' => 'gps', '130221' => 'gbb', '130220' => 'gyb', '130216' => 'gys', '130246' => 'gsb', '130222' => 'gpb', '130219' => 'gob', '130248' => 'gsb', '130247' => 'gsb', '130215' => 'grs', '130217' => 'gbs');
						foreach($swaparray as $old => $new) {
							if($data['' .$row. '_g'] == $old) {
								$gc = $new;
							}
						}
						$gem = '<a href="http://wowhead.com/item=' .$data['' .$row. '_g']. '&lvl=110"><img src="img/' .$gc. '.gif" /></a>';
					}			
					elseif($data['' .$row. '_g'] == '0' && $socketcheck > '-1') {
						$gem = '<img src="img/mg.png" alt="404" />';
					}
					
					// IF ROW = ENCHANTABLE
					if($row == 'n' || $row == 's' || $row == 'b' || $row == 'f1' || $row == 'f2') {
						// IF ENCHANT = MISSING
						if($data['' .$row. '_e'] == '0') {
							$enchant = '<img src="img/me.png" alt="404" />';
						}
						// ELSE IF ENCHANT EXISTS
						elseif($data['' .$row. '_e'] != '0') {
							// VALID ENCHANTS
							$swaparray = array('5437' => '128551', '5439' => '128553', '5883' => '140219', '5889' => '141908', '5890' => '141909', '5891' => '141910', '5431' => '128545', '5432' => '128546', '5433' => '128547', '5434' => '128548', '5435' => '128549', '5436' => '128550', '5423' => '128537', '5424' => '128538', '5425' => '128539', '5426' => '128540', '5427' => '128541', '5428' => '128542', '5429' => '128543', '5430' => '128544', '5442' => '140214', '5882' => '140218', '5440' => '128554', '5883' => '140219', '5441' => '140213', '5443' => '140215', '5881' => '140217', '5899' => '144328', '5438' => '128552', '5895' => '144304', '5896' => '144305', '5897' => '144306', '5898' => '144307');
							
							// CHECK FOR VALID ENCHANTMENT
							foreach($swaparray as $old => $new) {
								// IF YES
								if($data['' .$row. '_e'] == $old) {
									
									// CHECK IF ROW = SHOULDER
									if($row == 's') {
										
										// SPECIAL SHOULDER ENCHANTMENTS
										$shoulder_e = array('140215' => 'eb', '140218' => 'em', '140219' => 'ebh', '140213' => 'eg', '140217' => 'es', '140214' => 'eh', '144328' => 'ebb');

										// CHECK FOR SPECIAL SHOULDER ENCHANTMENT
										foreach($shoulder_e as $shoulder_id => $e_img) {
											// IF SHOULDER ENCHANTMENT = SPECIAL
											if($shoulder_id == $new) {
												// SWAP
												$enchant_img = $e_img;
												
											}											
										}
									}
									$enchant = '<a href="http://wowhead.com/item=' .$new. '"><img src="img/' .$enchant_img. '.gif" /></a>';
								}
							}
						}
					}
				}
				if($_SESSION['showhide'] == '1') {
					if($row == 'he' || $row == 'n' || $row == 's' || $row == 'b' || $row == 'c' || $row == 'ha' || $row == 'wa' || $row == 'l' || $row == 'f' || $row == 'f1' || $row == 'f2' || $row == 't1' || $row == 't1') {
						$socketcheck = strpos($data['' .$row. '_b'], '1808');
						if($socketcheck > '-1') {
							$gemarray = array('130246', '130247', '130220', '130222', '130219', '130248', '130221');
							if($data['' .$row. '_g'] == '0' || !in_array($data['' .$row. '_g'], $gemarray)) {
								$gem = '<img src="img/mg.png" alt="404" />';
							}
						}
					}
					
					if($row == 'n' || $row == 's' || $row == 'b' || $row == 'f1' || $row == 'f2') {
						$enchantarray = array('5442', '5882', '5440', '5883', '5441', '5443', '5881', '5427', '5428', '5429', '5430', '5435', '5434', '5436', '5437', '5438', '5439', '5883', '5889', '5890', '5891');
						
						if($data['' .$row. '_e'] == '0' || !in_array($data['' .$row. '_e'], $enchantarray)) {
							$enchant = '<img src="img/me.png" alt="404" />';
						}
					}
				}
				
				echo '<div class="tc" id="equip"><a href="http://wowhead.com/item=' .$data['' .$row. '_id'].'&bonus=' .$data['' .$row. '_b']. '" rel="gems=' .$data['' .$row. '_g']. '&ench=' .$data['' .$row. '_e']. '" ' .$quality. '>' .$data['' .$row. '_ilvl']. '</a> ' .$gem. ' ' .$enchant. '</div>';
				unset($gem);
				unset($enchant);
				unset($gc);
				unset($enchant_img);
				
			}
			
			if($data['mplus'] == '15') { $mplus = '<span style="color: green;">15</span>'; }
			elseif($data['mplus'] == '10') { $mplus = '<span style="color: orange;">10</span>'; }
			elseif($data['mplus'] == '5') { $mplus = '<span style="color: red;">5</span>'; }
			elseif($data['mplus'] < '5') { $mplus = '<span style="color: grey;">' .$data['mplus']. '</span>'; }
			
			echo '<div class="tc"><span title="ARC ' .$data['arc']. ' BRH ' .$data['brh']. ' COS ' .$data['cos']. ' DHT ' .$data['dht']. ' EOA ' .$data['eoa']. ' HOV ' .$data['hov']. ' MOS ' .$data['mos']. ' NEL ' .$data['nel']. ' VOW ' .$data['vow']. ' VH ' .$data['vh']. '">' .$data['sum']. ' ' .$sum_old. '</span></div>
			<div class="tc">' .$mplus. '</div>';
			
			if($data['en'] == '0') { $color_en = 'style="color: red;"'; } elseif($data['en'] == '7') { $color_en = 'style="color: green;"'; } elseif($data['en'] > '0') { $color_en = 'style="color: orange;"'; }			
			if($data['tov'] == '0') { $color_tov = 'style="color: red;"'; } elseif($data['tov'] == '3') { $color_tov = 'style="color: green;"'; } elseif($data['tov'] > '0') { $color_tov = 'style="color: orange;"'; }
			if($data['nh'] == '0') { $color_nh = 'style="color: red;"'; } elseif($data['nh'] == '10') { $color_nh = 'style="color: green;"'; } elseif($data['nh'] > '0') { $color_nh = 'style="color: orange;"'; }
			
			echo '<div class="tc"><span ' .$color_en. '>' .$data['en']. '/7</span></div>
			<div class="tc"><span ' .$color_tov. '>' .$data['tov']. '/3</span></div>
			<div class="tc"><span ' .$color_nh. '>' .$data['nh']. '/10</span></div>
			<div class="tc"><a href="?u=' .$data['id']. '"><img src="img/upd.png" alt="404" style="width: 13px;"/></a></div>';
			
			if($_SESSION['guest'] == '1') {
				echo '<div class="tc"></div>';
			}
			elseif($_SESSION['guest'] != '1') {
				echo '<div class="tc"><a href="?r=' .$data['id']. '"><img src="img/rmv.png" alt="404" style="width: 13px;"/></a></div>';
			}
			echo '</div>';
			$num++;
			unset($ap_incr); unset($sum_old); unset($bags_old); unset($ilvlavg_old); unset($alvl_old);
		}
		echo '</div>';
	}
}

include('bot.php');
?>