<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\ucp;

/**
* UCP Module for Forum Promotion
*/
class forumpromotion_module
{
	/** @var string */
	public $u_action;

	/** @var int */
	protected $id;
	/** @var int */
	protected $mode;

	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\log\log */
	protected $phpbb_log;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\symfony_request */
	protected $symfony_request;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;

	protected $p_master;
	protected $module;

	/** @var \lawlypops\forumpromotion\core\forumpromotion_manager */
	protected $crml_manager;

	/**
	* Constructor
	*/
	public function __construct($p_master)
	{
		global $db, $user, $auth, $template, $cache, $request, $symfony_request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $pagination;
		global $phpbb_log;
		global $phpbb_container;
		global $module;

		$this->p_master = $p_master;
		$this->module   = $module;

		$this->phpEx = $phpEx;
		$this->root_path = $phpbb_root_path;

		$this->config    = $config;
		$this->db        = $db;
		$this->user      = $user;
		$this->phpbb_log = $phpbb_log;
		$this->request   = $request;
		$this->symfony_request = $symfony_request;
		$this->template  = $template;


		$this->pagination = $phpbb_container->get('pagination');
		$this->crml_manager = $phpbb_container->get('lawlypops.forumpromotion.core.manager');
	}

	/*
	* Entry point for module
	* 
	* @param int $id     The id of the module.
	* @param int $mode   The mode of the module to enter.
	*/
	public function main($id, $mode)
	{
		$this->id = $id;
		$this->mode = $mode;

		switch($this->mode)
		{
			case 'cash_logs':
				$this->cash_logs();
				break;
		}
	}

	/**
	* Displays cash transaction logs.
	*/
	public function cash_logs()
	{
		global $table_prefix;

		$start_item = (int) $this->request->variable('start', 0);
		$items_per_page = 15;
		$error = NULL;
		$uid = (int) $this->user->data['user_id'];

		// TODO: Standardize this with phpbb abstraction layer functions.
		$sql = 'SELECT l.log_id, l.user_id_from, l.user_id_to, l.log_type_id, t.log_type_name, l.log_message, l.log_time, l.log_points, t.log_type_name, uf.user_id AS uf_id, uf.username AS uf_username, uf.user_colour AS uf_colour, ut.user_id AS ut_id, ut.username AS ut_username, ut.user_colour AS ut_colour
			FROM ' . $table_prefix . 'forumpro_log AS l
			INNER JOIN ' . $table_prefix . 'forumpro_log_types AS t
				ON t.log_type_id = l.log_type_id
			LEFT JOIN ' . USERS_TABLE . ' AS uf
				ON uf.user_id = l.user_id_from
			LEFT JOIN ' . USERS_TABLE . ' AS ut
				ON ut.user_id = l.user_id_to
			WHERE (l.user_id_from = ' . $uid . ' OR l.user_id_to = ' . $uid . ')
				AND t.log_type_ucp_visible = 1
			ORDER BY l.log_time DESC
			LIMIT ' . $start_item . ', ' . $items_per_page;
		$result = $this->db->sql_query($sql);

		// Loops through results and creates log from each one.
		while($row = $this->db->sql_fetchrow($result))
		{
			$user_from_username = get_username_string('full', $row['uf_id'], $row['uf_username'], $row['uf_colour']);
			$user_to_username = get_username_string('full', $row['ut_id'], $row['ut_username'], $row['ut_colour']);

			// Add log to template block to be retrieved by ucp_cash_logs.html.
			$this->template->assign_block_vars('logs', array(
				'ID' => $row['log_id'],
				'USER_ID_FROM' => $row['user_id_from'],
				'USER_ID_TO' => $row['user_id_to'],
				'USERNAME_FROM' => $user_from_username,
				'USERNAME_TO' => $user_to_username,
				'TIME' => $this->user->format_date($row['log_time']),
				'POINTS' => $row['log_points'],
				'TYPE' => $row['log_type_name'],
				'MESSAGE' => $row['log_message']
			));
		}

		$this->db->sql_freeresult($result);

		// Creates URL for making pages.
		$params = array('i=-lawlypops-forumpromotion-ucp-forumpromotion_module', 'mode=cash_logs');
		$pagination_url = append_sid($this->root_path . 'ucp.' . $this->phpEx, implode('&amp;', $params));

		// Gets total logs for user for making pages.
		$sql_ary = array(
			'SELECT'    => 'COUNT(l.log_id) as total_logs',
			'FROM'      => array(
				$table_prefix . 'forumpro_log' => 'l',
			),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array($table_prefix . 'forumpro_log_types' => 't'),
					'ON' => 't.log_type_id = l.log_type_id'
				)
			),
			'WHERE'     => '(user_id_to = ' . $uid . ' OR user_id_from = ' . $uid . ') 
				AND t.log_type_ucp_visible = 1'
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$this->db->sql_freeresult($result);
		$total_logs = $this->db->sql_fetchfield('total_logs'); // This is a cool function.

		// Nicely handles creating template vars for pagination.html.
		$pagination = $this->pagination->generate_template_pagination(
			$pagination_url, 
			'pagination', 
			'start', 
			$total_logs, 
			$items_per_page, 
			$start_item
		);

		// Other template vars.
		$this->template->assign_vars(array(
			'S_ERROR'      => (isset($error)) ? TRUE : FALSE,
			'ERROR_MSG'    => $error,
			'TOTAL_LOGS'   => $this->user->lang('UCP_TOTAL_LOGS', $total_logs),
			'PAGINATION'   => $pagination,
			'PAGE_NUMBER'  => $this->pagination->on_page($total_logs, $items_per_page, $start_item)
		));

		// Dear phpbb, please display the templates for us.
		$this->tpl_name = 'ucp_cash_logs';
		$this->page_title = $this->user->lang('UCP_CRML_CASH_LOGS');
	}
}