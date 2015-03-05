<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class viewtopic_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $root_path;
	/** @var string */
	protected $php_ext;

	/** @var int */
	protected $mcp_manage_module_id;

	/** 
	* Constructor
	* 
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\template\template $template
	* @param \phpbb\user\user $user
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;

		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Assign callback functions to callback functions.
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_post_rowset_data' => 'add_cash_data_to_post',
			'core.viewtopic_cache_guest_data' => 'add_cash_to_cache',
			'core.viewtopic_cache_user_data'  => 'add_cash_to_cache',
			'core.viewtopic_modify_post_row'  => 'update_post_row'
		);
	}

	/**
	* Tells viewtopic.php to get our shit.
	*
	* @param array   Variables with event context.
	*/
	public function add_cash_data_to_post($event) 
	{
		if($this->config['crml_enabled'])
		{
			$rowset   = $event['rowset_data'];
			$post_row = $event['row'];

			// Turn off decimals
			$cash = ($this->config['crml_enable_decimals'] == 1) 
				? $post_row['user_crml_cash'] : intval($post_row['user_crml_cash']);

			$rowset = array_merge($rowset, array(
				'user_crml_cash' => $cash
			));

			$event['rowset_data'] = $rowset;
		}
	}

	/**
	* Caches cash data.
	*
	* @param array    The event data.
	*/
	public function add_cash_to_cache($event)
	{
		if($this->config['crml_enabled'])
		{
			$user_cache_data = $event['user_cache_data'];
			$user_cache_data['user_crml_cash'] = $event['row']['user_crml_cash'];
			$event['user_cache_data'] = $user_cache_data;
		}
	}

	/**
	* Adds the cash data to the postrow.
	*
	* @param array   The event context.
	*/
	public function update_post_row($event)
	{
		if($this->config['crml_enabled'])
		{
			$row = $event['row'];
			$post_row = $event['post_row'];
			$post_row = array_merge($post_row, array(
				'CRML_CASH'        => $row['user_crml_cash'],
				
				'S_DONATIONS_ENABLED' => $this->config['crml_donations_enabled'],				
				'S_ALLOW_DONATE'   => $this->auth->acl_get('u_crml_donate') ? TRUE : FALSE,
				'S_ALLOW_MANAGE'   => $this->auth->acl_get('m_crml_manage_user_cash') ? TRUE : FALSE,
				'S_ALLOW_TIP'      => $this->auth->acl_get('u_crml_tip') ? TRUE : FALSE,

				'U_DONATE'         => $this->helper->route('fp_forumpromotion_donate_controller', array('uid' => $row['user_id'])),
				'U_MANAGE'         => append_sid("{$this->root_path}mcp.$this->php_ext", 'i=\lawlypops\forumpromotion\mcp\manage_cash_module&amp;mode=manage_cash&amp;user_id=' . $row['user_id'])
			));

			$event['post_row'] = $post_row;
		}
	}
}
