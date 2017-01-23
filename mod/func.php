<?php

function import($char) {
	
	if(isset($specc)) {
		unset($specc);
	}
		
	global $stream;
	
	$char = ucwords(strtolower($char));
	$server = $_SESSION['s'];
	$guild = $_SESSION['g'];
	
	// REMOVE SPACES IN SERVER AND GUILD NAME TO PREVENT BUGS IN URL
	if(strpos($server, ' ') !== false) {
		$server = str_replace(' ', '-', $server);
	}
	if(strpos($guild, ' ') !== false) {
		$guild = str_replace(' ', '%20', $guild);
	}
	// REMOVE SLASHES IN SERVER NAME TO ALLOW ACTUAL SEARCH AGAIN
	$server = stripslashes($server);
		
	// CHECK FOR LAST UPDATE
	$old = mysqli_fetch_array(mysqli_query($stream, "SELECT `lupd` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$char. "' ORDER BY `id` DESC LIMIT 1"));
	$timediff = time('now')-$old['lupd'];
		
	if($timediff >= '300') {
		
		$url = 'https://' .$_SESSION['r']. '.api.battle.net/wow/guild/' .$server. '/' .$guild. '?fields=members&locale=en_GB&apikey=KEY_HERE';

		// ENABLE SSL
		$arrContextOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, ),);  
		$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));
	
		if($data != '') {		
			$data = json_decode($data, true);
		
			for($i = '0'; $i <= '1000'; $i++) {
				if($memberserver == '') {
					if($data['members'][$i]['character']['name'] == $char) {
						$memberserver = $data['members'][$i]['character']['realm'];
					}					
				}
			}
		}
	
		if($memberserver != $server) {
			$server = $memberserver;
		}
			
		// REMOVE SPACES IN SERVER AND GUILD NAME TO PREVENT BUGS IN URL
		if(strpos($server, ' ') !== false) {
			$server = str_replace(' ', '-', $server);
		}
		if(strpos($guild, ' ') !== false) {
			$guild = str_replace(' ', '%20', $guild);
		}
		// REMOVE SLASHES IN SERVER NAME TO ALLOW ACTUAL SEARCH AGAIN
		$server = stripslashes($server);
	
		// CHECK IF CHARACTER IS IN GUILD
		$url = 'https://' .$_SESSION['r']. '.api.battle.net/wow/character/' .$server. '/' .$char. '?fields=guild,items,statistics,achievements,talents&locale=en_GB&apikey=KEY_HERE';
		$data = @file_get_contents($url, false, stream_context_create($arrContextOptions));
		if($data != '') {
			
			$data = json_decode($data, true);
			
			if($data['guild']['name'] == $_SESSION['g']) {
				
				// 110 CHECK
				if($data['level'] == '110') {
					
					// LAST LOGOUT
					$llog = substr($data['lastModified'], '0', '10');
					
					// ALL ITEMS
					$items = array('head', 'neck', 'shoulder', 'back', 'chest', 'wrist', 'hands', 'waist', 'legs', 'feet', 'finger1', 'finger2', 'trinket1', 'trinket2');
					foreach($items as $item) {
						${'' .$item. '_id'} = $data['items']['' .$item. '']['id'];
						${'' .$item. '_qual'} = $data['items']['' .$item. '']['quality'];
						${'' .$item. '_ilvl'} = $data['items']['' .$item. '']['itemLevel'];
						if(!empty($data['items']['' .$item. '']['tooltipParams']['enchant'])) {
							${'' .$item. '_ench'} = $data['items']['' .$item. '']['tooltipParams']['enchant'];
						}
						if(!empty($data['items']['' .$item. '']['tooltipParams']['gem0'])) {
							${'' .$item. '_gem0'} = $data['items']['' .$item. '']['tooltipParams']['gem0'];
						}
						else {
							${'' .$item. '_gem0'} = '';
						}
						foreach($data['items']['' .$item. '']['bonusLists'] as $bonus) {
							if(!isset(${'' .$item. '_bonus'})) {
								${'' .$item. '_bonus'} = $bonus;
							}
							elseif(isset(${'' .$item. '_bonus'})) {
								${'' .$item. '_bonus'}.= ':' .$bonus. '';
							}
						}						
					}
				
					$class = $data['class'];			
									
					// SPECIALIZATION NAME
					for($i = '0'; $i <= '4'; $i++) {
						if($specc == '') {
							if($data['talents'][$i]['selected'] == '1') {
								for($k = '0'; $k <= '7'; $k++) {					
									if(isset($data['talents'][$i]['talents'][$k]['spec']['name'])) {
										$specc = $data['talents'][$i]['talents'][$k]['spec']['name'];
									}
								}
							}
						}
					}	
					
					$weapon = mysqli_fetch_array(mysqli_query($stream, "SELECT `w` FROM `weapons` WHERE `s` = '" .$specc. "' AND `id` = '" .$class. "'"));
					
					if($data['items']['mainHand']['id'] == $weapon['w']) {
						
						$mhilvl = $data['items']['mainHand']['itemLevel'];
						if(!empty($data['items']['offHand']['itemLevel'])) {
							$ohilvl = $data['items']['offHand']['itemLevel'];
						}
							
						foreach($data['items']['mainHand']['bonusLists'] as $bonus) {
							if(!isset($mh_bonus)) {
								$mh_bonus = $bonus;
							}
						elseif(isset($mh_bonus)) {
							$mh_bonus.= ':' .$bonus. '';
							}
						}
						if(!empty($data['items']['mainHand']['relics'])) {
							$i = '0';
								foreach($data['items']['mainHand']['relics'] as $relic) {
									${'mhrelic' .$i. ''} = $relic['itemId'];
						
									foreach($relic['bonusLists'] as $bonus) {
										if(!isset(${'mhrelicbonus' .$i. ''})) {
											${'mhrelicbonus' .$i. ''} = $bonus;
										}
									elseif(isset(${'mhrelicbonus' .$i. ''})) {
										${'mhrelicbonus' .$i. ''}.= ':' .$bonus. '';
									}
								}
							$i++;
							}
						}
					}
					elseif($data['items']['offHand']['id'] == $weapon['w']) {
						$ohilvl = $data['items']['offHand']['itemLevel'];
						if(!empty($data['items']['mainHand']['itemLevel'])) {
							$mhilvl = $data['items']['mainHand']['itemLevel'];
						}
					
						foreach($data['items']['offHand']['bonusLists'] as $bonus) {
							if(!isset($oh_bonus)) {
								$oh_bonus = $bonus;
							}
							elseif(isset($oh_bonus)) {
								$oh_bonus.= ':' .$bonus. '';
							}
						}
					
						if(!empty($data['items']['offHand']['relics'])) {
							$i = '0';
							foreach($data['items']['offHand']['relics'] as $relic) {
								${'ohrelic' .$i. ''} = $relic['itemId'];
						
								foreach($relic['bonusLists'] as $bonus) {
									if(!isset(${'ohrelicbonus' .$i. ''})) {
										${'ohrelicbonus' .$i. ''} = $bonus;
									}
									elseif(isset(${'ohrelicbonus' .$i. ''})) {
										${'ohrelicbonus' .$i. ''}.= ':' .$bonus. '';
									}
								}
							$i++;
							}
						}
					}
				
					// EQUIPPED ITEMLEVEL						
					$ilvlaverage = $data['items']['averageItemLevelEquipped'];
					
					// BAG ITEMLEVEL						
					$ilvlaveragebags = $data['items']['averageItemLevel'];
					
					// RAID PROGRESS MYTHIC
					$en = '0';					
					$enarray = array($data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['33']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['37']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['41']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['45']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['49']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['53']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['57']['quantity']);
					
					foreach($enarray as $enmythic) {
						if($enmythic > '0') {
							$en++;
						}
					}
					
					$tov = '0';					
					$tovarray = array($data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['61']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['65']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['69']['quantity']);
					
					foreach($tovarray as $tovmythic) {
						if($tovmythic > '0') {
							$tov++;
						}
					}
					
					$nh = '0';
					$nharray = array($data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['73']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['77']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['81']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['85']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['89']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['93']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['97']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['101']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['105']['quantity'], $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['109']['quantity']);
					
					foreach($nharray as $nhmythic) {
						if($nhmythic > '0') {
							$nh++;
						}
					}
					
					// MYTHIC AND MYTHIC PLUS STATS
					$eoa = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['2']['quantity'];
					$dht = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['5']['quantity'];
					$nel = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['8']['quantity'];
					$hov = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['11']['quantity'];
					$vh1 = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['16']['quantity'];
					$vh2 = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['17']['quantity'];
					$vh = $vh1+$vh2;
					$vow = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['20']['quantity'];
					$brh = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['23']['quantity'];
					$mos = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['26']['quantity'];
					$arc = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['27']['quantity'];
					$cos = $data['statistics']['subCategories']['5']['subCategories']['6']['statistics']['28']['quantity'];
					
					$mythicsum = $eoa+$dht+$nel+$hov+$vh+$vow+$brh+$mos+$arc+$cos;
					
					// HIGHEST M+ IN TIME ACCORDING TO ACHIEVEMENTS
					if(in_array('11162', $data['achievements']['achievementsCompleted'])) {
						$mplus = '15';
					}	
					elseif(in_array('11185', $data['achievements']['achievementsCompleted'])) {
						$mplus = '10';
					}
					elseif(in_array('11184', $data['achievements']['achievementsCompleted'])) {
						$mplus = '5';
					}
					elseif(in_array('11183', $data['achievements']['achievementsCompleted'])) {
						$mplus = '2';
					}
					
					// ARTIFACT POWER AND LEVEL
					$key = array_search('30103', $data['achievements']['criteria']);
					$key2 = array_search('29395', $data['achievements']['criteria']);
					$key3 = array_search('31466', $data['achievements']['criteria']);
			
					if($key != '') {
						$criterias = array();
						array_push($criterias, $data['achievements']['criteriaQuantity']);
						$criterias = $criterias['0'];
						$totalgained = $criterias[$key];
						$alevel = $criterias[$key2];
						$aknowledge = $criterias[$key3];
					}
					elseif($key == '') {
						$totalgained = '0';
						$alevel = '0';
						$aknowledge = '0';
					}
			
					if(strpos($server, '-') !== false) {
						$server = str_replace('-', ' ', $server);
					}
						
					// CREATE INDIVIDUAL TABLE					
					$table = mysqli_query($stream, "CREATE TABLE IF NOT EXISTS `" .$_SESSION['t']. "` (`id` int(4) NOT NULL AUTO_INCREMENT,
					`ch` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
					`r` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					`llog` int(10) NOT NULL,
					`lupd` int(10) NOT NULL,
					`c` tinyint(1) NOT NULL,
					`s` text COLLATE latin1_german2_ci NOT NULL,
					`he_id` mediumint(6) NOT NULL,
					`he_ilvl` smallint(4) NOT NULL,
					`he_g` mediumint(6) NOT NULL,
					`he_e` mediumint(6) NOT NULL,
					`he_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`n_id` mediumint(6) NOT NULL,
					`n_ilvl` smallint(4) NOT NULL,
					`n_g` mediumint(6) NOT NULL,
					`n_e` mediumint(6) NOT NULL,
					`n_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`s_id` mediumint(6) NOT NULL,
					`s_ilvl` smallint(4) NOT NULL,
					`s_g` mediumint(6) NOT NULL,
					`s_e` mediumint(6) NOT NULL,
					`s_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`b_id` mediumint(6) NOT NULL,
					`b_ilvl` smallint(4) NOT NULL,
					`b_g` mediumint(6) NOT NULL,
					`b_e` mediumint(6) NOT NULL,
					`b_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`c_id` mediumint(6) NOT NULL,
					`c_ilvl` smallint(4) NOT NULL,
					`c_g` mediumint(6) NOT NULL,
					`c_e` mediumint(6) NOT NULL,
					`c_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`wr_id` mediumint(6) NOT NULL,
					`wr_ilvl` smallint(4) NOT NULL,
					`wr_g` mediumint(6) NOT NULL,
					`wr_e` mediumint(6) NOT NULL,
					`wr_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`ha_id` mediumint(6) NOT NULL,
					`ha_ilvl` smallint(4) NOT NULL,
					`ha_g` mediumint(6) NOT NULL,
					`ha_e` mediumint(6) NOT NULL,
					`ha_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`wa_id` mediumint(6) NOT NULL,
					`wa_ilvl` smallint(4) NOT NULL,
					`wa_g` mediumint(6) NOT NULL,
					`wa_e` mediumint(6) NOT NULL,
					`wa_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`l_id` mediumint(6) NOT NULL,
					`l_ilvl` smallint(4) NOT NULL,
					`l_g` mediumint(6) NOT NULL,
					`l_e` mediumint(6) NOT NULL,
					`l_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f_id` mediumint(6) NOT NULL,
					`f_ilvl` smallint(4) NOT NULL,
					`f_g` mediumint(6) NOT NULL,
					`f_e` mediumint(6) NOT NULL,
					`f_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f1_id` mediumint(6) NOT NULL,
					`f1_ilvl` smallint(4) NOT NULL,
					`f1_g` mediumint(6) NOT NULL,
					`f1_e` mediumint(6) NOT NULL,
					`f1_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f2_id` mediumint(6) NOT NULL,
					`f2_ilvl` smallint(4) NOT NULL,
					`f2_g` mediumint(6) NOT NULL,
					`f2_e` mediumint(6) NOT NULL,
					`f2_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`t1_id` mediumint(6) NOT NULL,
					`t1_ilvl` smallint(4) NOT NULL,
					`t1_g` mediumint(6) NOT NULL,
					`t1_e` mediumint(6) NOT NULL,
					`t1_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`t2_id` mediumint(6) NOT NULL,
					`t2_ilvl` smallint(4) NOT NULL,
					`t2_g` mediumint(6) NOT NULL,
					`t2_e` mediumint(6) NOT NULL,
					`t2_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_ilvl` smallint(4) NOT NULL,
					`mh_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r1` mediumint(6) NOT NULL,
					`mh_r1b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r2` mediumint(6) NOT NULL,
					`mh_r2b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r3` mediumint(6) NOT NULL,
					`mh_r3b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_ilvl` smallint(4) NOT NULL,
					`oh_bonus` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r1` mediumint(6) NOT NULL,
					`oh_r1b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r2` mediumint(6) NOT NULL,
					`oh_r2b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r3` mediumint(6) NOT NULL,
					`oh_r3b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`ilvlavg` smallint(4) NOT NULL,
					`ilvlbags` smallint(4) NOT NULL,
					`eoa` int(5) NOT NULL,
					`dht` int(5) NOT NULL,
					`nel` int(5) NOT NULL,
					`hov` int(5) NOT NULL,
					`vh` int(5) NOT NULL,
					`vow` int(5) NOT NULL,
					`brh` int(5) NOT NULL,
					`mos` int(5) NOT NULL,
					`arc` int(5) NOT NULL,
					`cos` int(5) NOT NULL,
					`sum` int(5) NOT NULL,
					`mplus` tinyint(2) NOT NULL,
					`en` tinyint(1) NOT NULL,
					`tov` tinyint(1) NOT NULL,
					`nh` tinyint(1) NOT NULL,
					`alvl` smallint(3) NOT NULL,
					`ap` int(11) NOT NULL,
					`ak` tinyint(3) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `id` (`id`),
					KEY `id_2` (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=2 ;");
					if(!$table) {
						echo '<p id="error">Sorry, could not generate database entry at this time. Please retry!</p>';
					}
					
					// CREATE ARCHIVE TABLE
					$archive = mysqli_query($stream, "CREATE TABLE IF NOT EXISTS `" .$_SESSION['t']. "_archive` (`id` int(4) NOT NULL AUTO_INCREMENT,
					`ch` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
					`r` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					`llog` int(10) NOT NULL,
					`lupd` int(10) NOT NULL,
					`c` tinyint(1) NOT NULL,
					`s` text COLLATE latin1_german2_ci NOT NULL,
					`he_id` mediumint(6) NOT NULL,
					`he_ilvl` smallint(4) NOT NULL,
					`he_g` mediumint(6) NOT NULL,
					`he_e` mediumint(6) NOT NULL,
					`he_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`n_id` mediumint(6) NOT NULL,
					`n_ilvl` smallint(4) NOT NULL,
					`n_g` mediumint(6) NOT NULL,
					`n_e` mediumint(6) NOT NULL,
					`n_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`s_id` mediumint(6) NOT NULL,
					`s_ilvl` smallint(4) NOT NULL,
					`s_g` mediumint(6) NOT NULL,
					`s_e` mediumint(6) NOT NULL,
					`s_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`b_id` mediumint(6) NOT NULL,
					`b_ilvl` smallint(4) NOT NULL,
					`b_g` mediumint(6) NOT NULL,
					`b_e` mediumint(6) NOT NULL,
					`b_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`c_id` mediumint(6) NOT NULL,
					`c_ilvl` smallint(4) NOT NULL,
					`c_g` mediumint(6) NOT NULL,
					`c_e` mediumint(6) NOT NULL,
					`c_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`wr_id` mediumint(6) NOT NULL,
					`wr_ilvl` smallint(4) NOT NULL,
					`wr_g` mediumint(6) NOT NULL,
					`wr_e` mediumint(6) NOT NULL,
					`wr_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`ha_id` mediumint(6) NOT NULL,
					`ha_ilvl` smallint(4) NOT NULL,
					`ha_g` mediumint(6) NOT NULL,
					`ha_e` mediumint(6) NOT NULL,
					`ha_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`wa_id` mediumint(6) NOT NULL,
					`wa_ilvl` smallint(4) NOT NULL,
					`wa_g` mediumint(6) NOT NULL,
					`wa_e` mediumint(6) NOT NULL,
					`wa_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`l_id` mediumint(6) NOT NULL,
					`l_ilvl` smallint(4) NOT NULL,
					`l_g` mediumint(6) NOT NULL,
					`l_e` mediumint(6) NOT NULL,
					`l_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f_id` mediumint(6) NOT NULL,
					`f_ilvl` smallint(4) NOT NULL,
					`f_g` mediumint(6) NOT NULL,
					`f_e` mediumint(6) NOT NULL,
					`f_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f1_id` mediumint(6) NOT NULL,
					`f1_ilvl` smallint(4) NOT NULL,
					`f1_g` mediumint(6) NOT NULL,
					`f1_e` mediumint(6) NOT NULL,
					`f1_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`f2_id` mediumint(6) NOT NULL,
					`f2_ilvl` smallint(4) NOT NULL,
					`f2_g` mediumint(6) NOT NULL,
					`f2_e` mediumint(6) NOT NULL,
					`f2_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`t1_id` mediumint(6) NOT NULL,
					`t1_ilvl` smallint(4) NOT NULL,
					`t1_g` mediumint(6) NOT NULL,
					`t1_e` mediumint(6) NOT NULL,
					`t1_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`t2_id` mediumint(6) NOT NULL,
					`t2_ilvl` smallint(4) NOT NULL,
					`t2_g` mediumint(6) NOT NULL,
					`t2_e` mediumint(6) NOT NULL,
					`t2_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_ilvl` smallint(4) NOT NULL,
					`mh_b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r1` mediumint(6) NOT NULL,
					`mh_r1b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r2` mediumint(6) NOT NULL,
					`mh_r2b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`mh_r3` mediumint(6) NOT NULL,
					`mh_r3b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_ilvl` smallint(4) NOT NULL,
					`oh_bonus` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r1` mediumint(6) NOT NULL,
					`oh_r1b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r2` mediumint(6) NOT NULL,
					`oh_r2b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`oh_r3` mediumint(6) NOT NULL,
					`oh_r3b` varchar(20) COLLATE latin1_german2_ci NOT NULL,
					`ilvlavg` smallint(4) NOT NULL,
					`ilvlbags` smallint(4) NOT NULL,
					`eoa` int(5) NOT NULL,
					`dht` int(5) NOT NULL,
					`nel` int(5) NOT NULL,
					`hov` int(5) NOT NULL,
					`vh` int(5) NOT NULL,
					`vow` int(5) NOT NULL,
					`brh` int(5) NOT NULL,
					`mos` int(5) NOT NULL,
					`arc` int(5) NOT NULL,
					`cos` int(5) NOT NULL,
					`sum` int(5) NOT NULL,
					`mplus` tinyint(2) NOT NULL,
					`en` tinyint(1) NOT NULL,
					`tov` tinyint(1) NOT NULL,
					`nh` tinyint(1) NOT NULL,
					`alvl` smallint(3) NOT NULL,
					`ap` int(11) NOT NULL, 
					`ak` tinyint(3) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `id` (`id`),
					KEY `id_2` (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=2 ;");
					if(!$archive) {
						echo '<p id="error">Sorry, could not generate database entry at this time. Please retry!</p>';
					}
										
					// SELECT, IF EXISTING, PREVIOUS DATA AND MOVE
					$current_db_entry = mysqli_fetch_array(mysqli_query($stream, "SELECT `id`, `llog`, `ap` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$char. "'"));
					
					$insert_sql = "INSERT INTO `" .$_SESSION['t']. "` (`ch`, `r`, `llog`, `lupd`, `c`, `s`, `he_id`, `he_ilvl`, `he_g`, `he_e`, `he_b`, `n_id`, `n_ilvl`, `n_g`, `n_e`, `n_b`, `s_id`, `s_ilvl`, `s_g`, `s_e`, `s_b`, `b_id`, `b_ilvl`, `b_g`, `b_e`, `b_b`, `c_id`, `c_ilvl`, `c_g`, `c_e`, `c_b`, `wr_id`, `wr_ilvl`, `wr_g`, `wr_e`, `wr_b`, `ha_id`, `ha_ilvl`, `ha_g`, `ha_e`, `ha_b`, `wa_id`, `wa_ilvl`, `wa_g`, `wa_e`, `wa_b`, `l_id`, `l_ilvl`, `l_g`, `l_e`, `l_b`, `f_id`, `f_ilvl`, `f_g`, `f_e`, `f_b`, `f1_id`, `f1_ilvl`, `f1_g`, `f1_e`, `f1_b`, `f2_id`, `f2_ilvl`, `f2_g`, `f2_e`, `f2_b`, `t1_id`,`t1_ilvl`, `t1_g`, `t1_e`, `t1_b`,`t2_id`, `t2_ilvl`, `t2_g`, `t2_e`,`t2_b`, `mh_ilvl`,`mh_b`, `mh_r1`,`mh_r1b`, `mh_r2`,`mh_r2b`, `mh_r3`,`mh_r3b`, `oh_ilvl`,`oh_bonus`, `oh_r1`,`oh_r1b`, `oh_r2`,`oh_r2b`, `oh_r3`,`oh_r3b`, `ilvlavg`, `ilvlbags`, `eoa`, `dht`, `nel`,`hov`, `vh`, `vow`, `brh`, `mos`, `arc`, `cos`, `sum`, `mplus`, `en`, `tov`, `nh`, `alvl`, `ap`, `ak`) VALUES ('" .$char. "', '" .$server. "', '" .$llog. "', '" .time('now'). "', '" .$class. "', '" .$specc. "', '" .$head_id. "', '" .$head_ilvl. "', '" .$head_gem0. "', '" .$head_ench. "', '" .$head_bonus. "', '" .$neck_id. "', '" .$neck_ilvl. "', '" .$neck_gem0. "', '" .$neck_ench. "', '" .$neck_bonus. "', '" .$shoulder_id. "', '" .$shoulder_ilvl. "', '" .$shoulder_gem0. "', '" .$shoulder_ench. "', '" .$shoulder_bonus. "', '" .$back_id. "', '" .$back_ilvl. "', '" .$back_gem0. "', '" .$back_ench. "', '" .$back_bonus. "', '" .$chest_id. "', '" .$chest_ilvl. "', '" .$chest_gem0. "', '" .$chest_ench. "', '" .$chest_bonus. "', '" .$wrist_id. "', '" .$wrist_ilvl. "', '" .$wrist_gem0. "', '" .$wrist_ench. "', '" .$wrist_bonus. "', '" .$hands_id. "', '" .$hands_ilvl. "', '" .$hands_gem0. "', '" .$hands_ench. "', '" .$hands_bonus. "', '" .$waist_id. "', '" .$waist_ilvl. "', '" .$waist_gem0. "', '" .$waist_ench. "', '" .$waist_bonus. "', '" .$legs_id. "', '" .$legs_ilvl. "', '" .$legs_gem0. "', '" .$legs_ench. "', '" .$legs_bonus. "', '" .$feet_id. "', '" .$feet_ilvl. "', '" .$feet_gem0. "', '" .$feet_ench. "', '" .$feet_bonus. "', '" .$finger1_id. "', '" .$finger1_ilvl. "', '" .$finger1_gem0. "', '" .$finger1_ench. "', '" .$finger1_bonus. "', '" .$finger2_id. "', '" .$finger2_ilvl. "', '" .$finger2_gem0. "', '" .$finger2_ench. "', '" .$finger2_bonus. "', '" .$trinket1_id. "', '" .$trinket1_ilvl. "', '" .$trinket1_gem0. "', '" .$trinket1_ench. "', '" .$trinket1_bonus. "', '" .$trinket2_id. "', '" .$trinket2_ilvl. "', '" .$trinket2_gem0. "', '" .$trinket2_ench. "', '" .$trinket2_bonus. "', '" .$mhilvl. "', '" .$mh_bonus. "', '" .$mhrelic0. "', '" .$mhbonusrelic0. "', '" .$mhrelic1. "', '" .$mhbonusrelic1. "', '" .$mhrelic2. "', '" .$mhbonusrelic2. "', '" .$ohilvl. "', '" .$oh_bonus. "', '" .$ohrelic0. "', '" .$ohbonusrelic0. "', '" .$ohrelic1. "', '" .$ohbonusrelic1. "', '" .$ohrelic2. "', '" .$ohbonusrelic2. "', '" .$ilvlaverage. "', '" .$ilvlaveragebags. "', '" .$eoa. "', '" .$dht. "', '" .$nel. "', '" .$hov. "', '" .$vh. "', '" .$vow. "', '" .$brh. "', '" .$mos. "', '" .$arc. "', '" .$cos. "', '" .$mythicsum. "', '" .$mplus. "', '" .$en. "', '" .$tov. "' , '" .$nh. "', '" .$alevel. "', '" .$totalgained. "', '" .$aknowledge. "')";
										
					if($current_db_entry['id'] != '') {
						
						// IS ENTRY THE SAME?
						if($current_db_entry['llog'] != $llog) {
							
							mysqli_query($stream, "INSERT INTO `" .$_SESSION['t']. "_archive` SELECT * FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$current_db_entry['id']. "'");
							mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "` WHERE `id` = '" .$current_db_entry['id']. "'");
							$insert = mysqli_query($stream, $insert_sql);
						
							if(!$insert) {
								echo '<p id="error">Sorry, could not insert character data at this time. Most likely the armory is unavailable – please retry!</p>';
							}
						}
						// IF ENTRY IS JUST AS OLD
						else {
							mysqli_query($stream, "UPDATE `" .$_SESSION['t']. "` SET `lupd` = '" .time('now'). "' WHERE `id` = '" .$current_db_entry['id']. "'");
						}
					}
					else {
						$insert = mysqli_query($stream, $insert_sql);						
					
						if(!$insert) {
							echo '<p id="error">Sorry, could not insert character data at this time. Most likely the armory is unavailable – please retry!</p>';
						}
					}				
					
					/*
					// ONLY ALLOW 25 LAST ENTRIES PER CHARACTER
					$highest_id = mysqli_fetch_array(mysqli_query($stream, "SELECT `id` FROM `" .$_SESSION['t']. "` ORDER BY `id` DESC LIMIT 1"));
					$delete = mysqli_query($stream, "DELETE FROM `" .$_SESSION['t']. "` WHERE `id` <= '" .($highest_id['id']-25). "'");
					*/
				}
			}	
		}
	}
	elseif($timediff < '300') {
		echo '<p id="error">Sorry, updating allowed only once every 5 minutes. Seconds left for <u>' .$char. '</u>: ' .(300-$timediff). '</p>';
	}
}	

?>