<?php

include('dbcon.php');
include('top.php');

$chars = array();
$fetch = mysqli_query($stream, "SELECT DISTINCT(`ch`) FROM `" .$_SESSION['t']. "` ORDER BY `ch` ASC");
while($char = mysqli_fetch_array($fetch)) {
	array_push($chars, $char['ch']);
}

if(!isset($_POST['c'])) {
	echo '<div class="graphbox" style="text-align: center;">
	<h2 id="cent">specific character</h2>
	<form action="" method="POST" id="cent">
	<select name="c" onChange="this.form.submit()">
	<option selected disabled>select character</option>';
	foreach($chars as $char) {
		echo '<option value="' .$char. '">' .$char. '</option>';
	}
	echo '</select>
	</form>
	<hr>
	<h2 id="cent">your guild</h2>
	<div style="width: 30%; display: inline-block;"><div id="ilvlalvl_g"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="alvlmythics_g"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="ilvlmythics_g"></div></div>
	<hr>
	<h2 id="cent">all guilds (updates once per hour)</h2>
	<div style="width: 30%; display: inline-block;"><div id="ilvlalvl_gl"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="alvlmythics_gl"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="ilvlmythics_gl"></div></div>
	</div>';
		
	?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Artifact Level', 'Itemlevel'],
			<?php
			foreach($chars as $char) {
				$data = mysqli_fetch_array(mysqli_query($stream, "SELECT `alvl`, `ilvlavg` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$char. "' AND `ilvlavg` >= '840'"));
				echo "[" .$data['ilvlavg']. ", " .$data['alvl']. "], ";
			}
			
			?>
		]);

		var options = {
			title: 'Artifact Level vs Itemlevel comparison',
			hAxis: {title: 'Itemlevel', minValue: 840 , maxValue: 940},
			vAxis: {title: 'Artifact Level', minValue: 0, maxValue: 54},
			backgroundColor: 'white',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('ilvlalvl_g'));

		chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Artifact Level', 'Mythics done'],
			<?php
			foreach($chars as $char) {
				$data = mysqli_fetch_array(mysqli_query($stream, "SELECT `alvl`, `sum` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$char. "'"));
				echo "[" .$data['alvl']. ", " .$data['sum']. "], ";
			}
			$highest_sum = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`sum`) AS `max` FROM `" .$_SESSION['t']. "`"));
			
			?>
		]);

		var options = {
			title: 'Artifact Level vs Mythics done comparison',
			hAxis: {title: 'Artifact Level', minValue: 0, maxValue: 54},
			vAxis: {title: 'Mythics done', minValue: 0, maxValue: <?php echo $highest_sum['max']; ?>},
			backgroundColor: 'white',
			legend: { position: 'none' },			
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('alvlmythics_g'));

		chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Mythics done', 'Itemlevel equipped, Mythics done'],
			<?php
			foreach($chars as $char) {
				$data = mysqli_fetch_array(mysqli_query($stream, "SELECT `ilvlavg`, `sum` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$char. "' AND `ilvlavg` >= '840'"));
				echo "[" .$data['ilvlavg']. ", " .$data['sum']. "], ";
			}
			$highest_sum = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`sum`) AS `max` FROM `" .$_SESSION['t']. "`"));
			
			?>
		]);

		var options = {
			title: 'Itemlevel equipped vs Mythics done comparison',
			hAxis: {title: 'Itemlevel', minValue: 840 , maxValue: 940},
			vAxis: {title: 'Mythics done', minValue: 0, maxValue: <?php echo $highest_sum['max']; ?>},
			backgroundColor: 'white',
			legend: { position: 'none' },			
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('ilvlmythics_g'));

		chart.draw(data, options);
		}
	</script>
	
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Artifact Level', 'Itemlevel'],
			<?php
	
			$timestamp = mysqli_fetch_array(mysqli_query($stream, "SELECT `sum` FROM `gg` WHERE `id` = '999999'"));
			if(time('now')-$timestamp['sum'] >= '3600') {
				mysqli_query($stream, "TRUNCATE `gg`");
				
				$guildids = array();
				$guildid_data = mysqli_query($stream, "SELECT `id` FROM `guilds`");
				while($data = mysqli_fetch_array($guildid_data)) {
					array_push($guildids, $data['id']);
				}
				foreach($guildids as $guildid) {
					$fetch = mysqli_query($stream, "SELECT DISTINCT(`ch`) FROM `" .$guildid. "` ORDER BY `ch` ASC");
					while($chars = mysqli_fetch_array($fetch)) {
						$data = mysqli_fetch_array(mysqli_query($stream, "SELECT `alvl`, `ilvlavg`, `sum` FROM `" .$guildid. "` WHERE `ch` = '" .$chars['ch']. "' AND `ilvlavg` >= '840'"));
						$insert = mysqli_query($stream, "INSERT INTO `gg` (`char`, `alvl`, `ilvl`, `sum`) VALUES ('" .$chars['ch']. "', '" .$data['alvl']. "', '" .$data['ilvlavg']. "', '" .$data['sum']. "'); ");
					}
				}
				mysqli_query($stream, "INSERT INTO `gg` (`id`, `sum`) VALUES ('999999', '" .time('now'). "')");
			}
			
			
			$graphdata = mysqli_query($stream, "SELECT `ilvl`, `alvl` FROM `gg` WHERE `id` != '999999' AND `ilvl` > '840'");
			while($chardata = mysqli_fetch_array($graphdata)) {
				echo "[" .$chardata['ilvl']. ", " .$chardata['alvl']. "], ";
			}
			
			?>
		]);

		var options = {
			title: 'Itemlevel vs Artifact Level comparison',
			hAxis: {title: 'Itemlevel', minValue: 840 , maxValue: 940 },
			vAxis: {title: 'Artifact Level', minValue: 0, maxValue: 54 },
			backgroundColor: 'white',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('ilvlalvl_gl'));

		chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Artifact Level', 'Aritfact Level, Mythics done'],
			<?php
			
			$graphdata = mysqli_query($stream, "SELECT `alvl`, `sum` FROM `gg` WHERE `id` != '999999'");
			while($chardata = mysqli_fetch_array($graphdata)) {
				echo "[" .$chardata['alvl']. ", " .$chardata['sum']. "], ";
			}
			$highest_sum = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`sum`) AS `max` FROM `gg` WHERE `id` != '999999'"));
			
			?>
		]);

		var options = {
			title: 'Artifact Level vs Mythics done comparison',
			hAxis: {title: 'Artifact Level', minValue: 0, maxValue: 54},
			vAxis: {title: 'Mythics done', minValue: 0, maxValue: <?php echo $highest_sum['max']; ?>},
			backgroundColor: 'white',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('alvlmythics_gl'));

		chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Mythics done', 'Itemlevel equipped, Mythics done'],
			<?php
			
			$graphdata = mysqli_query($stream, "SELECT `ilvl`, `sum` FROM `gg` WHERE `id` != '999999' AND `ilvl` > '840'");
			while($chardata = mysqli_fetch_array($graphdata)) {
				echo "[" .$chardata['ilvl']. ", " .$chardata['sum']. "], ";
			}
			$highest_sum = mysqli_fetch_array(mysqli_query($stream, "SELECT MAX(`ilvl`) AS `max` FROM `gg` WHERE `id` != '999999'"));
			
			?>
		]);

		var options = {
				title: 'Itemlevel equipped vs Mythics done comparison',
			hAxis: {title: 'Itemlevel', minValue: 840 , maxValue: 940},
			vAxis: {title: 'Mythics done', minValue: 0, maxValue: <?php echo $highest_sum['max']; ?>},
			backgroundColor: 'white',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('ilvlmythics_gl'));

		chart.draw(data, options);
		}
	</script>
	<?php
	
}

if(isset($_POST['c'])) {
	echo '<div style="text-align: center;">
	<div style="width: 30%; display: inline-block;"><div id="ilvlgain_indiv"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="mythicgain_indiv"></div></div>
	</div>
	<div style="text-align: center;">
	<div style="width: 30%; display: inline-block;"><div id="apgain_indiv"></div></div>
	<div style="width: 30%; display: inline-block;"><div id="alvlgain_indiv"></div></div>
	</div>';
	
	?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Time', 'Itemlevel equipped', 'Itemlevel Bags'],
				<?php
				$old_entries = mysqli_query($stream, "SELECT `ilvlavg`, `ilvlbags`, `llog` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$_POST['c']. "' ORDER BY `llog` ASC");
				while($rows = mysqli_fetch_array($old_entries)) {
					echo "['" .date('d.m.Y - H:i', $rows['llog']) ."', " .$rows['ilvlavg']. ", " .$rows['ilvlbags']. "],";
				}
	
				$right_now = mysqli_fetch_array(mysqli_query($stream, "SELECT `llog`, `ilvlavg`, `ilvlbags` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$_POST['c']. "'"));
				echo "['" .date('d.m.Y - H:i', $right_now['llog']). "', " .$right_now['ilvlavg']. ", " .$right_now['ilvlbags']. "],";
				?>
			]);

		var options = {
			title: 'Itemlevel equipped & in bags over time',
			curveType: 'function',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.LineChart(document.getElementById('ilvlgain_indiv'));

		chart.draw(data, options);
		}
    </script>
    <script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Time', 'Artifact Power'],
				<?php
				$old_entries = mysqli_query($stream, "SELECT `ap`, `llog` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$_POST['c']. "' ORDER BY `llog` ASC");
				while($rows = mysqli_fetch_array($old_entries)) {
					echo "['" .date('d.m.Y - H:i', $rows['llog']) ."', " .$rows['ap']. "],";
				}
	
				$right_now = mysqli_fetch_array(mysqli_query($stream, "SELECT `llog`, `ap` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$_POST['c']. "'"));
				echo "['" .date('d.m.Y - H:i', $right_now['llog']). "', " .$right_now['ap']. "],";
				?>
			]);

		var options = {
			title: 'Artifact Power collected over time',
			curveType: 'function',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.LineChart(document.getElementById('apgain_indiv'));

		chart.draw(data, options);
		}
    </script>
    <script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Time', 'Mythic dungeons completed'],
				<?php
				$old_entries = mysqli_query($stream, "SELECT `sum`, `llog` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$_POST['c']. "' ORDER BY `llog` ASC");
				while($rows = mysqli_fetch_array($old_entries)) {
					echo "['" .date('d.m.Y - H:i', $rows['llog']) ."', " .$rows['sum']. "],";
				}
	
				$right_now = mysqli_fetch_array(mysqli_query($stream, "SELECT `llog`, `sum` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$_POST['c']. "'"));
				echo "['" .date('d.m.Y - H:i', $right_now['llog']). "', " .$right_now['sum']. "],";
				?>
			]);

		var options = {
			title: 'Mythic dungeons completed over time',
			curveType: 'function',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.LineChart(document.getElementById('mythicgain_indiv'));

		chart.draw(data, options);
		}
    </script>
    <script type="text/javascript">
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Time', 'Artifact Level over time'],
				<?php
				$old_entries = mysqli_query($stream, "SELECT `alvl`, `llog` FROM `" .$_SESSION['t']. "_archive` WHERE `ch` = '" .$_POST['c']. "' ORDER BY `llog` ASC");
				while($rows = mysqli_fetch_array($old_entries)) {
					echo "['" .date('d.m.Y - H:i', $rows['llog']) ."', " .$rows['alvl']. "],";
				}
	
				$right_now = mysqli_fetch_array(mysqli_query($stream, "SELECT `llog`, `alvl` FROM `" .$_SESSION['t']. "` WHERE `ch` = '" .$_POST['c']. "'"));
				echo "['" .date('d.m.Y - H:i', $right_now['llog']). "', " .$right_now['alvl']. "],";
				?>
			]);

		var options = {
			title: 'Artifact Level over time',
			curveType: 'function',
			legend: { position: 'none' }
		};

		var chart = new google.visualization.LineChart(document.getElementById('alvlgain_indiv'));

		chart.draw(data, options);
		}
    </script>
	<?php
}



?>



