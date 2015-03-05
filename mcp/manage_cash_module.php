<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\mcp;

class manage_cash_module
{
	/** @var string */
	public $u_action;

	/** @var int */
	protected $id;
	/** @var int */
	protected $mode;

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
	*
	*/
	public function __construct($p_master)
	{
		global $db, $user, $auth, $template, $cache, $request, $symfony_request;
        global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
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

        $this->crml_manager = $phpbb_container->get('lawlypops.forumpromotion.core.manager');
	}

	/**
	* Main function. 
	*
	* @param int $id   The id of the module.
	* @param int $mode The mode of the module to display.
	*/
	public function main($id, $mode)
	{
	
        $this->id   = $id;
        $this->mode = $mode;

        switch($this->mode)
        {
        	case 'manage_cash_search':
        		$this->manage_cash_search();
        		break;
        	case 'manage_cash':
        		$this->manage_cash();
        		break;

        }
	}

	/**
	* Allows moderator to search for users to manage.
	*/
	public function manage_cash_search()
	{
		global $phpbb_root_path, $phpEx;

		$referer = $this->symfony_request->get('_referer');

		$error = NULL;

		if($this->request->is_set_post('submit'))
		{
			$data = array(
				'username' => (string) $this->request->variable('username', '')
			);

			$sql = 'SELECT user_id 
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_build_array('SELECT', $data);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);

			if($row)
			{
				$redirect_url = append_sid("{$phpbb_root_path}mcp.$phpEx", "i=" . $this->id . "&amp;mode=manage_cash&amp;user_id=" . $row['user_id']);
				redirect($redirect_url);
			} 
			else
			{
				$error = $this->user->lang('MCP_CRML_USER_NOT_FOUND');
			}
		}

		$this->template->assign_vars(array(
			'U_FIND_USERNAME' => append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=mcp&amp;field=username&amp;select_single=true'),

			'S_ERROR'      => (isset($error)) ? TRUE : FALSE,
			'ERROR_MSG'    => $error
		));

		$this->tpl_name = 'mcp_manage_cash_search';
		$this->page_title = $this->user->lang('MCP_CRML_MANAGE_SEARCH');
	}

	/**
	* Allows moderator to set selected user's cash.
	*/
	public function manage_cash()
	{
		add_form_key('manage_cash');

		$this->module->set_display($this->id, 'manage_cash', true);
		$referer = $this->symfony_request->get('_referer');

		if(!is_numeric($this->request->variable('user_id', '')))
		{
			redirect($this->get_module_url('manage_cash_search'));
		}

		$uid = (int) $this->request->variable('user_id', '');
		$error = NULL;

		$sql = 'SELECT user_id, user_crml_cash, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $uid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		if(!$row)
		{
			redirect($this->get_module_url('manage_cash_search'));
		}

		$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

		if($this->request->is_set_post('submit'))
		{
			$cash_value = (float) $this->request->variable('cash_value', 0);
			$change_type = $this->request->variable('cash_change_type', '');
			$new_value = 0;

			if(!check_form_key('manage_cash'))
			{
				$error = $this->user->lang('FORM_INVALID');
			}

			switch($change_type)
			{
				case 'set':
					$new_value = $cash_value;
					break;
				case 'add':
					$new_value = $row['user_crml_cash'] + $cash_value;
					break;
				case 'subtract':
					$new_value = $row['user_crml_cash'] - $cash_value;
					break;
				default:
					$error = $this->user->lang('MCP_CRML_INVALID_TYPE');
			}

			if(!isset($error))
			{
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_crml_cash = ' . $new_value . '
					WHERE user_id = ' . $uid;
				$res = $this->db->sql_query($sql);

				$this->phpbb_log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_CHANGE_CASH', time(), array($username, $new_value));

				$message = $this->user->lang('MCP_SUCCESSFUL_SUBMIT') . '<br /><br />' 
					. $this->user->lang('RETURN_PAGE', '<a href="' . append_sid($referer, "") . '">', '</a>') . '<br />'
					. $this->user->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.$this->phpEx", "") . '">', '</a>');
				trigger_error($message);
			}
		}

		// Gets user logs and assigns them to block var.
		$user_logs = $this->crml_manager->get_logs(0, 10, $uid);
		$grabbed_usernames = array($uid => $username);

		$user_logs_out = array();

		foreach($user_logs as $key => $value)
		{
			$from_id = $value['user_id_from'];
			$to_id = $value['user_id_to'];

			$new_out = array();

			$from_username = (isset($grabbed_usernames[$from_id]))
				? $grabbed_usernames[$from_id] : $this->crml_manager->get_username($from_id);

			$to_username   = (isset($grabbed_usernames[$to_id]))
				? $grabbed_usernames[$t_id] : $this->crml_manager->get_username($to_id);

			$log_time = $this->user->format_date($value['log_time']);

			$this->template->assign_block_vars('cashlogs', array(
				'FROM_USERNAME' => $from_username,
				'TO_USERNAME'   => $to_username,
				'POINTS'        => $value['log_points'],
				'TIME'          => $log_time,
				'MESSAGE'       => $value['log_message'],
			));
		}
		
		// Gets sprintf'd display strings.
		$managing_username = $this->user->lang('MCP_CRML_MANAGE_CASH_FOR', $username, $row['user_crml_cash']);
		$user_current_status = $this->user->lang('MCP_CRML_USER_CURRENT', $row['user_crml_cash'], $this->config['crml_unit_name']);

		$this->template->assign_vars(array(
			'S_ERROR'               => isset($error) ? TRUE : FALSE,
			'ERROR_MSG'             => $error,

			'MCP_CRML_USER_CASH'         => $row['user_crml_cash'],
			'CRML_MANAGING_USERNAME'     => $managing_username,
			'CRML_USER_CURRENT_STATUS'   => $user_current_status,
		));

		$this->tpl_name = 'mcp_manage_cash';
		$this->page_title = $this->user->lang('MCP_CRML_MANAGE_USER');
	}

	/**
	* Builds a URL for a mode from this module given the mode string.
	* 
	* @param string $mode   The inner name of the mode. Does *not* convert special chars.
	*/
	protected function get_module_url($mode)
	{
		return append_sid("{$phpbb_root_path}mcp.$phpEx", "i=" . $this->id . "&amp;mode=" . $mode);
	}
}