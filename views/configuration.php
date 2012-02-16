<?php
/**
 * NSM Reports: New Members
 *  - configuration form
 *
 * @package NsmReports
 * @subpackage New_members_report
 * @version 1.0.0
 * @author Leevi Graham <http://leevigraham.com.au>
 * @author Iain Saxon <iain.saxon@newism.com.au>
 * @copyright Copyright (c) 2007-2010 Newism
 * @license Commercial - please see LICENSE file included with this distribution
 */
?>
<tr>
	<th scope="row">Member data</th>
	<td>
		<?php foreach ($member_fields as $field_name => $field_label) : ?>
				<label>
					<input
						type="checkbox" 
						name="report[member_fields][]"
						value="<?= $field_name; ?>"
						<?= (in_array($field_name, $config['member_fields']) ? ' checked="checked"' : ''); ?>
					/>
					<?= $field_label; ?>
				</label>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<th scope="row">Custom member data</th>
	<td>
		<?php foreach ($additional_fields as $field) : ?>
				<label>
					<input
						type="checkbox" 
						name="report[additional_fields][]"
						value="<?= $field['m_field_id']; ?>"
						<?= (in_array($field['m_field_id'], $config['additional_fields']) ? ' checked="checked"' : ''); ?>
					/>
					<?= $field['m_field_label']; ?>
				</label>
		<?php endforeach; ?>
	</td>
</tr>

<tr>
	<th scope="row">Date</th>
	<td>
		<select name="report[date_mode]" id="report_config_date_mode">
			<option value="today"<?= ($config['date_mode'] == 'today' ? ' selected="selected"' : ''); ?>>Today</option>
			<option value="yesterday"<?= ($config['date_mode'] == 'yesterday' ? ' selected="selected"' : ''); ?>>Yesterday</option>
			<option value="last_week"<?= ($config['date_mode'] == 'last_week' ? ' selected="selected"' : ''); ?>>Last week</option>
			<option value="last_month"<?= ($config['date_mode'] == 'last_month' ? ' selected="selected"' : ''); ?>>Last month</option>
			<option value="last_year"<?= ($config['date_mode'] == 'last_year' ? ' selected="selected"' : ''); ?>>Last year</option>
			<option value="custom"<?= ($config['date_mode'] == 'custom' ? ' selected="selected"' : ''); ?>>Custom</option>
		</select>
	</td>
</tr>
<tr>
	<th scope="row">Date start</th>
	<td>
		<input type="text" id="report_config_date_start_filter" class="date-picker" name="report[date_start_filter]" style="width:33%" value="<?= $config['date_start_filter']; ?>" />
	</td>
</tr>
<tr>
	<th scope="row">Date end</th>
	<td>
		<input type="text" id="report_config_date_end_filter" class="date-picker" name="report[date_end_filter]" style="width:33%" value="<?= $config['date_end_filter']; ?>" />
	</td>
</tr>
<tr>
	<th scope="row">Grouping</th>
	<td>
		<select name="report[chart_grouping]" id="report_config_chart_grouping">
			<option value="hour"<?= ($config['chart_grouping'] == 'hour' ? ' selected="selected"' : ''); ?>>Hour</option>
			<option value="day"<?= ($config['chart_grouping'] == 'day' ? ' selected="selected"' : ''); ?>>Day</option>
			<option value="week"<?= ($config['chart_grouping'] == 'week' ? ' selected="selected"' : ''); ?>>Week</option>
			<option value="month"<?= ($config['chart_grouping'] == 'month' ? ' selected="selected"' : ''); ?>>Month</option>
		</select>
	</td>
</tr>
<tr>
	<th scope="row">Show entries</th>
	<td>
		<select name="report[row_limit]">
			<option value="25"<?= ($config['row_limit'] == 25 ? ' selected="selected"' : ''); ?>>25</option>
			<option value="50"<?= ($config['row_limit'] == 50 ? ' selected="selected"' : ''); ?>>50</option>
			<option value="100"<?= ($config['row_limit'] == 100 ? ' selected="selected"' : ''); ?>>100</option>
			<option value="200"<?= ($config['row_limit'] == 200 ? ' selected="selected"' : ''); ?>>200</option>
			<option value="400"<?= ($config['row_limit'] == 400 ? ' selected="selected"' : ''); ?>>400</option>
			<option value=""<?= ($config['row_limit'] == '' ? ' selected="selected"' : ''); ?>>None</option>
		</select>
	</td>
</tr>
<tr>
	<th scope="row">Row Offset</th>
	<td>
		<input type="text" id="report_config_row_offset" name="report[row_offset]" style="width:50px" value="<?= $config['row_offset']; ?>" />
	</td>
</tr>