<?php
/**
 * Code Igniter View of report preview page in Control Panel
 *
 * This file is used as the default View for a report preview and is used during the New_members_report::outputBrowser() method.
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

<html>

<head>
<title>New members report</title>
</head>

<body>
<?php
	$total_val = $totals['existing_members'];
	$total_members_h_axis = array();
	$total_members_data = array();
	$total_members_max = 0;
	$total_members_min = 0;
	$new_members_h_axis = array();
	$new_members_data = array();
	$new_members_max = 0;
	$new_members_min = 0;
	foreach ($charts['signups'] as $count => $row) {
		$total_val = ($total_val + intval($row['v']));
		
		$total_members_h_axis[] = urlencode($row['k']);
		$total_members_data[] = $total_val;
		
		$new_members_h_axis[] = urlencode($row['k']);
		$new_members_data[] = $row['v'];
		
		//echo "['" . $row['k'] . "', " . $total_val . ", " . $row['v'] . "], ";
	}
	
	$total_members_max = max($total_members_data);
	$total_members_min = min($total_members_data);
	
	$new_members_max = max($new_members_data);
	$new_members_min = min($new_members_data);
	
?>
	<h1>Graphs</h1>
	
		<?php
			$signup_chart_options = array(
				'chxl=0:|'.implode('|', $new_members_h_axis),
				'chxr=1,' .
					round($new_members_min, -2) . ',' . 
					round($new_members_max, -2),
				'chxt=x,y',
				'chs=600x300',
				'cht=lxy',
				'chco=3072F3',
				'chds=0,100,' . 
					round($new_members_min, -2) . ',' . 
					round($new_members_max, -2),
				'chd=t:-1|'.implode(',', $new_members_data),
				'chdl=Sign-ups',
				'chdlp=b',
				'chg=0,20,5,5',
				'chls=1,4,0',
				'chma=0,5,5,25|0,5',
				'chm=N,FF0000,0,-1,14,1'
			);
		?>
	
		<h2>New member sign-ups</h2>
		<img src="http://chart.googleapis.com/chart?<?= implode('&', $signup_chart_options) ?>"/>
		
		<table style="width:600px;">
			<thead>
				<tr>
					<th>
						<?= implode('</th><th>', array_map("urldecode", $new_members_h_axis)); ?>
					</th>
				</tr>
			</thead>
		
			<tbody>
				<tr>
					<td>
						<?= implode('</td><td>', array_map("urldecode", $new_members_data)); ?>
					</td>
				</tr>
			</tbody>
			
		</table>
	

	<h1>Stats</h1>
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
	
	<h1>Data</h1>
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

</body>
</html>