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

/** 
* Global listener.
*/
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \lawlypops\forumpromotion\core\forumpromotion_manager */
	protected $forumpromotion_manager;
 
 	/**
 	* Constructor
 	*
 	* @param \phpbb\config\config $config
 	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
 	* @param \lawlypops\forumpromotion\core\forumpromotion_manager $forumpromotion_manager
 	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $forumpromotion_manager)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		$this->forumpromotion_manager = $forumpromotion_manager;
	}

	/**
	* Gets core events subscribed to.
	*
	* @return array   Returns teh core events with their callbacks.
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.common'     => 'global_calls',
			'core.user_setup' => 'language_setup'
		);
	}

	/**
	* Handles logic that needs to be called on every page.
	*
	* @param array $event   Array containing situational data.
	*/
	public function global_calls($event)
	{
		// Assign global template vars.
		$this->template->assign_vars(array(
			'S_FORUMPROMOTION'           => $this->config['crml_enabled'] ? TRUE : FALSE,
			'S_FORUMPROMOTION_ENABLED'   => $this->config['crml_enabled'] ? TRUE : FALSE, 
			'CRML_UNIT'           => $this->config['crml_unit_name']
		));
	}

	/**
	* Adds common lang data to every page.
	*
	* @param array $event   Array containing situational data.
	*/
	public function language_setup($event)
	{
		// Handle login increment per day.
		if($this->config['crml_enabled'] && ($this->config['crml_login_increment'] != 0))
		{
			$this->forumpromotion_manager->adjust_user_cash($this->user->data['user_id'], 'login');
		}

		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'lawlypops/forumpromotion',
			'lang_set' => 'forumpromotion_common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
