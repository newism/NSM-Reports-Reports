<?php

/**
 * NSM Reports: New Members
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

/**
 * Report object
 *
 * @package NsmReports
 */
class New_members_report extends Nsm_report_base {
	
	/**
	 * Displays the report name in the control panel
	 *
	 * @var string
	 * @access protected
	 */
	protected $title = 'New members';
	
	/**
	 * Basic description of the report
	 *
	 * @var string
	 * @access protected
	 */
	protected $notes = 'Displays new members to the website within a specified period';
	
	/**
	 * Name and/or company of the report's creator
	 *
	 * @var string
	 * @access protected
	 */
	protected $author = 'Newism';
	
	/**
	 * A URL to the report's documentation (optional)
	 *
	 * @var string
	 * @access protected
	 */
	protected $docs_url = 'http://www.newism.com.au';
	
	/**
	 * Version number of report as a string to preserve decimal points
	 *
	 * @var string
	 * @access protected
	 */
	protected $version = '1.0.0';
	
	/**
	 * Report type as either 'simple' or 'complex'
	 *
	 * @var string
	 * @access protected
	 */
	protected $type = 'complex';
	
	/**
	 * Valid report output types
	 *
	 * @var array
	 * @access public
	 */
	public $output_types = array(
									'browser' => 'View in browser',
									'csv' => 'Comma-Seperated Values (CSV)',
									'html' => 'HyperText Markup Language (HTML)',
									'xml' => 'eXtensible Markup Language (XML)'
								);
	
	/**
	 * Default report configuration options with '_output' as a minumum entry
	 *
	 * @var array
	 * @access protected
	 */
	protected $config = array(
		'_output' => 'browser',
		'member_fields' => array(),
		'additional_fields' => array(),
		'order_by' => '',
		'row_limit' => 9999,
		'row_offset' => 0,
		'date_mode' => 'today',
		'date_start_filter' => false,
		'date_end_filter' => false,
		'chart_grouping' => 'day'
	);
	
	/**
	 * Stores the generated SQL statement used by the report
	 *
	 * @var string
	 * @access public
	 */
	public $sql = "";
	
	/**
	 * The file-path where the report is located and is used for including report views
	 *
	 * @var string
	 * @access public
	 */
	public $report_path = '';
	
	/**
	 * The file-path where the report output can be stored on the server
	 *
	 * @var string
	 * @access public
	 */
	public $cache_path = '';
	
	/**
	 * Stores any report errors that are encountered and saved at report run
	 *
	 * @var bool|string By default error is a boolean value and a string if an error is stored
	 * @access public
	 */
	public $error = false;
	
	/**
	 * PHP5 constructor function.
	 *
	 * Prepares instance of ExpressionEngine for object scope and sets report path
	 * Report classes extending this class should always call the parent's constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		if ( !defined('__DIR__') ) {
			define('__DIR__', dirname(__FILE__));
		}
		$this->report_path = __DIR__;
	}
	
	/**
	 * Returns Control Panel HTML to configure report if report type is complex or returns 'false' if simple
	 *
	 * @access public
	 * @return string|bool Configuration HTML or false 
	 */
	public function configHTML()
	{
		
		$member_fields = $this->getMemberDataFields();
		
		$this->EE->db->select('m_field_id, m_field_label');
		$this->EE->db->from('member_fields');
		$this->EE->db->order_by('m_field_order');
		$additional_fields = $this->EE->db->get();
		
		$data = array(
			'config' => $this->config,
			'member_fields' => $member_fields,
			'additional_fields' => $additional_fields->result_array()
		);
		
		$this->EE->cp->add_js_script(array('ui' => 'datepicker'));
		$this->EE->cp->add_to_foot('<link rel="stylesheet" href="'.BASE.AMP.'C=css'.AMP.'M=datepicker" type="text/css" media="screen" />');

		$default_date = $this->EE->localize->set_localized_time() * 1000;
		$current_time = date("' H:i'", gmt_to_local(now()));

		$behaviours = <<<BEHAVIOURS
<script type="text/javascript">
/* <![CDATA[ */

function show_hide_date_inputs(el){
	if(el.value == 'custom'){
		$("#nsm-report-config .date-picker").parents('tr').show();
	}else{
		$("#nsm-report-config .date-picker").parents('tr').hide();
	}
}

$(function(){
	var report_config_date_mode = $("#report_config_date_mode");
	show_hide_date_inputs(report_config_date_mode.get(0));
	report_config_date_mode.bind('load change', function(){
		show_hide_date_inputs(this);
	});

	$(".date-picker").datepicker({ 
		dateFormat: $.datepicker.W3C + {$current_time},
		defaultDate: new Date({$default_date})
	});
});
/* ]]> */
</script>
BEHAVIOURS;

		$this->EE->cp->add_to_foot($behaviours);

		if (APP_VER < '2.1.5') {
			// EE < .2.2.0
			return $this->EE->load->_ci_load(array(
				'_ci_vars' => $data,
				'_ci_path' => $this->report_path . 'views/configuration.php',
				'_ci_return' => true
			));
		} else {
			$this->EE->load->add_package_path($this->report_path);
			return $this->EE->load->view('configuration', $data, TRUE);
		}
	}
	
	/**
	 * Generates the SQL query string and returns the results as an array
	 * 
	 * @access public
	 * @return array Array of database results
	 */
	public function generateResults()
	{
		// prepare the class's configuration
		$config = $this->config;
		
		$db =& $this->EE->db;
		
		switch ($config['date_mode']) {
			case 'today':
				$date_start = strtotime('today');
				$date_end = strtotime('now');
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
			case 'yesterday':	
				$date_start = strtotime('today - 1 day');
				$date_end = strtotime('now');
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
			case 'last_week':
				$date_start = strtotime('today - 1 week');
				$date_end = strtotime('now');
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
			case 'last_month':
				$date_start = strtotime('today - 1 month');
				$date_end = strtotime('now');
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
			case 'last_year':
				$date_start = strtotime('today - 1 year');
				$date_end = strtotime('now');
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
			case 'custom':
				$date_start = human_to_unix($config['date_start_filter']);
				$date_end = human_to_unix($config['date_end_filter']);
				$config['date_start_filter'] = $date_start;
				$config['date_end_filter'] = $date_end;
			break;
		}
		$this->config = $config;
		
		$filter_modes = array(
			'after' => ">",
			'before' => "<",
			'equal' => "=",
			'not' => "<>"
		);
		
		// select member id from the members table
		$db->select('members.`member_id` AS `ID`', false);
		$db->from('members');
		
		// prepare a list of member data columns to build SQL with
		$member_fields = $this->getMemberDataFields();
		
		// iterate over the chosen member data columns and add the fields to the CI-AR
		if (count($config['member_fields']) > 0) {
			foreach ($config['member_fields'] as $field_name) {
				$db->select('`' . $field_name . '` AS `' . $member_fields[$field_name] . '`', false);
			}
		}
		
		// now prepare the information required to add the custom member fields to the result-set
		if (count($config['additional_fields']) > 0) {
			
			// return a CI-DB result containing the chosen custom member column ids and names
			$additional_fields = $db->query('
				SELECT 
					`m_field_id` AS `id`, 
					`m_field_label` AS `label`
				FROM `exp_member_fields`
				WHERE `m_field_id` IN 
					(' . implode( ',', $config['additional_fields'] ) . ')
				');
			
			// prepare the database table join in the CI-AR and group by the member id
			$db->join('member_data', 'member_data.member_id = members.member_id', 'left');
			$db->group_by('members.`member_id`');
			
			// iterate over the returned custom member fields and add them to the CI-AR
			foreach ($additional_fields->result_array() as $additional_field) {
				$member_data_field = '`m_field_id_'.$additional_field['id'].'` '.
										'AS `'.$additional_field['label'].'`';
				$db->select($member_data_field, false);
			}
		}
		
		if (in_array('group_title', $config['member_fields'])) {
			$db->join('member_groups', 'member_groups.group_id = members.group_id', 'left');
			$db->group_by('members.`member_id`');
		}
		
		$db->order_by('join_date', 'asc');
		
		if (! empty($config['date_start_filter'])) {
			$db->where(
				'join_date >',
				($config['date_start_filter'])
			);
		}
		if (! empty($config['date_end_filter'])) {
			$db->where(
				'join_date <',
				($config['date_end_filter'])
			);
		}
		
		if (! empty($config['row_limit'])) {
			$db->limit($config['row_limit'], $config['row_offset']);
		}
		
		// get the data results
		$query = $db->get();
		
		// check for a false result and return false if an error was found
		if ($query == false) {
			return false;
		}
		
		// return the results as an array and prepare another array for manipulation
		$original_results = $query->result_array();
		$results = array();
		
		// prepare an array to store graph data
		$charts = array(
			'signups' => array(),
			'groups' => array()
		);
		// iterate over the original results array and add them to the results
		// we keep these two seperate in case we decide to do something different
		// with the members data
		foreach ($original_results as $count => $member) {
			// add this member to the results array
			$results[] = $member;
		}
		
		// get total members
		$db->select('COUNT(*) as `num`');
		$db->from('members');
		$get_total_members = $db->get();
		$db_total_members = $get_total_members->row();
		$total_members = $db_total_members->num;
		
		// get new members
		$db->select('COUNT(*) as `num`');
		$db->from('members');
		if (! empty($config['date_start_filter'])) {
			$db->where(
				'join_date >',
				($config['date_start_filter'])
			);
		}
		if (! empty($config['date_end_filter'])) {
			$db->where(
				'join_date <',
				($config['date_end_filter'])
			);
		}
		$get_new_members = $db->get();
		$db_new_members = $get_new_members->row();
		$new_members = $db_new_members->num;
		
		$perc_increase =  ($new_members / ($total_members - $new_members)) * 100;
		
		// get chart data
		$chart_groupings = array(
			'hour' => array(
				'select' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%a %l%p'".
					")",
				'group' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%Y-%m-%d-%H'".
					")",
			),
			'day' => array(
				'select' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%a %e/%c'".
					")",
				'group' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%Y-%m-%d'".
					")",
			),
			'week' => array(
				'select' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'Week %v (%D %b)'".
					")",
				'group' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%Y-%u'".
					")",
			),
			'month' => array(
				'select' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%b %Y'".
					")",
				'group' => "DATE_FORMAT(".
					"FROM_UNIXTIME(`join_date`), ".
					"'%Y-%m'".
					")"
			)
		);
		$db->from('members');
		$db->select('COUNT(`member_id`) as `v`');
		$db->order_by('join_date', 'asc');
		
		if (! empty($config['date_start_filter'])) {
			$db->where(
				'join_date >',
				($config['date_start_filter'])
			);
		}
		if (! empty($config['date_end_filter'])) {
			$db->where(
				'join_date <',
				($config['date_end_filter'])
			);
		}
		$db->_protect_identifiers = false;
		$db->select($chart_groupings[$config['chart_grouping']]['select'].' as `k`');
		$db->group_by($chart_groupings[$config['chart_grouping']]['group']);
		$get_chart_data = $db->get();
		$db->_protect_identifiers = true;
		$charts['signups'] = $get_chart_data->result_array();
		
		// get member groups
		$db->from('members');
		$db->join('member_groups', 'member_groups.group_id = members.group_id', 'left');
		$db->select('group_title as `k`');
		$db->select('COUNT(exp_members.`member_id`) as `v`');
		$db->order_by('member_groups.group_title', 'asc');
		$db->group_by('members.group_id');
		
		if (! empty($config['date_start_filter'])) {
			$db->where(
				'join_date >',
				($config['date_start_filter'])
			);
		}
		if (! empty($config['date_end_filter'])) {
			$db->where(
				'join_date <',
				($config['date_end_filter'])
			);
		}
		$get_chart_data = $db->get();
		$charts['groups'] = $get_chart_data->result_array();
		
		$results['_totals'] = array(
			'total_members' => $total_members,
			'existing_members' => ($total_members-$new_members),
			'new_members' => $new_members,
			'perc_increase' => $perc_increase
		);
		
		$results['_charts'] = $charts;
		
		return $results;
	}
	
	/**
	 * Renders a View from the report results to display in the browser
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as HTML
	 */
	public function outputBrowser($results)
	{
		// prepare the class's configuration
		$config = $this->config;
		
		$this->EE->load->helper('date');
		
		// take the _totals meta data out of our results
		if (!empty($results['_totals'])) {
			$totals = $results['_totals'];
			unset($results['_totals']);
		} else {
			$totals = array(
				'total_members' => 0,
				'existing_members' => 0,
				'new_members' => 0,
				'perc_increase' => 0
			);
		}
		
		// take the _charts meta data out of our results
		if (!empty($results['_charts'])) {
			$charts = $results['_charts'];
			unset($results['_charts']);
		} else {
			$charts = array();
		}
		
		// prepare the db object
		$db =& $this->EE->db;
		
		// we want to get the titles of the member groups
		$member_groups = array();
		$db->select('group_id, group_title');
		$db->from('member_groups');
		$get_member_groups = $db->get();
		foreach ($get_member_groups->result_array() as $count => $row) {
			$member_groups[ $row['group_id'] ] = $row['group_title'];
		}
		
		// prepare columns array and row data
		$columns = array();
		$rows = $results;
		
		if (!empty($results)) {
		
			// iterate over the first row to get column names
			foreach ($rows[0] as $column => $data) {
				$columns[] = $column;
			}
		
			// prepare member data fields
			$member_fields = $this->getMemberDataFields();
			$cp_url = $this->EE->config->item('cp_url');
			for ( $row_i=0, $row_m=count($rows); $row_i<$row_m; $row_i+=1 ) {
			
				// if username was added to the member columns list then alter output to inlcude hyperlink
				if (in_array('username', $config['member_fields'])) {
					$value = $rows[$row_i][ $member_fields['username'] ];
					$link = $cp_url.'?D=cp'.AMP.'C=myaccount'.AMP.'id='.$rows[$row_i]['ID'];
					$rows[$row_i][ $member_fields['username'] ] = '<a href="'.$link.'">'.$value.'</a>';
				}
			
				// if email address was added to the member columns list then alter output to inlcude hyperlink
				if (in_array('email', $config['member_fields'])) {
					$value = $rows[$row_i][ $member_fields['email'] ];
					$link = 'mailto:'.$value;
					$rows[$row_i][ $member_fields['email'] ] = '<a href="'.$link.'">'.$value.'</a>';
				}
			
				if (in_array('join_date', $config['member_fields'])) {
					$value = $rows[$row_i][ $member_fields['join_date'] ];
					$rows[$row_i][ $member_fields['join_date'] ] = date('Y-m-d H:i', local_to_gmt($value));
				}
			
				if (in_array('last_visit', $config['member_fields'])) {
					$value = $rows[$row_i][ $member_fields['last_visit'] ];
					$rows[$row_i][ $member_fields['last_visit'] ] = date('Y-m-d H:i', local_to_gmt($value));
				}
			}
		}
		
		// make our data ready for viewing
		$data = array(
			'columns' => $columns,
			'rows' => $rows,
			'totals' => $totals,
			'charts' => $charts,
			'config' => $config,
			'input_prefix' => __CLASS__
		);
		
		if (APP_VER < '2.1.5') {
			// EE < .2.2.0
			return $this->EE->load->_ci_load(array(
				'_ci_vars' => $data,
				'_ci_path' => $this->report_path . 'views/output_browser.php',
				'_ci_return' => true
			));
		} else {
			$this->EE->load->add_package_path($this->report_path);
			return $this->EE->load->view('output_browser', $data, TRUE);
		}
	}
	
	public function outputCSV($results)
	{
		unset($results['_totals']);
		unset($results['_charts']);
		return parent::outputCSV($results);
	}
	
	public function outputXML($results)
	{
		unset($results['_totals']);
		unset($results['_charts']);
		return parent::outputXML($results);
	}
	
	/**
	 * Renders a View from the report results to display in the browser
	 *
	 * @access public
	 * @param object $results Array of report results.
	 * @return string Result data represented as HTML
	 */
	public function outputHTML($results)
	{
		// we'll just let the outputBrowser method do the work
		return $this->outputBrowser($results);
	}
	
	
	/**
	 * Returns a value-name-pair of all member data fields for use in the report
	 *
	 * @access private
	 * @return array List of member data columns
	 */
	private function getMemberDataFields()
	{
		$fields = array(
			'username' => 'Username',
			'screen_name' => 'Name',
			'email' => 'Email address',
			'join_date' => 'Join date',
			'group_title' => 'Group',
			'last_visit' => 'Last visit',
			'total_entries' => 'Entries',
			'total_comments' => 'Comments'
		);
		return $fields;
	}
	
}

