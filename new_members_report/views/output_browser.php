<?php
/**
 * NSM Reports: New Members
 *  - browser / html output view
 *
 * @package NsmReports
 * @subpackage New_members_report
 * @version 1.0.0
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au> 
 * @copyright Copyright (c) 2007-2011 Newism <http://newism.com.au>
 * @license Commercial - please see LICENSE file included with this distribution
 * @link http://ee-garage.com/nsm-reports
 * @see http://expressionengine.com/public_beta/docs/development/modules.html
 */
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<div class="tg">

<?php if (empty($rows)) : ?>

<div class="alert error">
	No new members have signed up within this period - 
	<strong>
		<?= date('Y-m-d', local_to_gmt($config['date_start_filter'])); ?>
		-
		<?= date('Y-m-d', local_to_gmt($config['date_end_filter'])); ?>
	</strong>
</div>

<?php else: ?>


	<h2>Graphs</h2>
	
	<div>
		<div id="chart_members" style="height:260px;"></div>
		
		<div id="chart_groups" style="height:260px;"></div>
	</div>
	
	<script type="text/javascript">
	/* <![CDATA[ */
	
		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		
		function drawCharts() {
			drawSignupsChart();
			drawGroupsChart();
		}
		
		function drawSignupsChart() {

			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Date');
			data.addColumn('number', 'Total');
			data.addColumn('number', 'Sign-ups');
			data.addRows([
			<?php
				$total_val = $totals['existing_members'];
				foreach ($charts['signups'] as $count => $row) {
					$total_val = ($total_val + intval($row['v']));
					echo "['" . $row['k'] . "', " . $total_val . ", " . $row['v'] . "], ";
				}
			?>
			]);
		
		 	 // Set chart options
			  var options = {
				title: "Member sign-ups (<?= date('Y-m-d', local_to_gmt($config['date_start_filter'])); ?> - <?= date('Y-m-d', local_to_gmt($config['date_end_filter'])); ?>)",
				//width: 700,
				height: 260,
				<?php if ($config['_output'] == 'browser') : ?>
				backgroundColor: "#ECF1F4",
				<?php endif; ?>
				seriesType: "line",
				series: {
					1: {
						type: "line",
						targetAxisIndex: 1
					}
				},
				vAxes: [
					{
						title: "Total",
						//minValue: 1000,
						//maxValue: 3000
					},
					{
						title: "Sign-ups",
						//minValue: 0,
						//maxValue: 10
					}
				]
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.ComboChart(document.getElementById('chart_members'));
			chart.draw(data, options);
		}
		
		function drawGroupsChart() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Groups');
			data.addColumn('number', 'Members');
			data.addRows([
			<?php
				foreach ($charts['groups'] as $count => $row) {
					echo "['" . $row['k'] . "', " . $row['v'] . "], ";
				}
			?>
			]);

			// Set chart options
			var options = {
				title: "New members (<?= $totals['new_members']; ?>)",
				//width: 240,
				height: 260,
				<?php if ($config['_output'] == 'browser') : ?>
				backgroundColor: "#ECF1F4",
				<?php endif; ?>
				legend: {
					position: "left"
				}
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.PieChart(document.getElementById('chart_groups'));
			chart.draw(data, options);

		}
		
		
	/* ]]> */
	</script>
	
	
	<h2>Stats</h2>
	<table>
		<tr>
			<td>Existing members</td>
			<td><?= ($totals['total_members']-$totals['new_members']); ?></td>
		</tr>
		<tr>
			<td>New members</td>
			<td><?= $totals['new_members']; ?></td>
		</tr>
		<tr>
			<td>Total members</td>
			<td><?= $totals['total_members']; ?></td>
		</tr>
		<tr>
			<td>Percentage increase</td>
			<td><?= round($totals['perc_increase'],2); ?>%</td>
		</tr>
	</table>
	
	<h2>
		Data
		<small>
			(showing <?= count($rows); ?>
			 of <?= $totals['new_members']; ?> members,
			 starting at <?= $config['row_offset']; ?>)
		</small>
	</h2>
	<table class="data col_sortable NSM_Stripeable">
		<thead>
			<tr>
				<?php foreach ($columns as $column) : ?>
					<th scope="col"><?= $column ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($rows as $row_i => $row) : $col_i = 0; ?>
			<tr>
				<?php foreach ($row as $column => $data) : $col_i++; ?>
					<?php if ($col_i == 0) : ?>
						<th scope="row"><?= $data; ?></th>
					<?php else: ?>
						<td><?= $data; ?></td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>


<script type="text/javascript">
/* <![CDATA[ */
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
	// Set a callback to run when the Google Visualization API is loaded.
	google.setOnLoadCallback(drawCharts);
/* ]]> */
</script>


<?php endif; ?>