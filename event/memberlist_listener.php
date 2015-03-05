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
* Event listener
*/
class memberlist_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth           $auth       Auth object
	* @param \phpbb\config\config       $config     Config object
	* @param \phpbb\controller\helper   $helper     Controller helper object
	* @access public
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_prepare_profile_data'	=> 'prepare_user_cash_data',
		);
	}

	/**
	* Display user cash amount and donate / manage URLS on user profile page
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function prepare_user_cash_data($event)
	{
		if($this->config['crml_enabled'])
		{
			$data = $event['data'];
			$template_data = $event['template_data'];

			$user_cash_value = ($this->config['crml_enable_decimals']) 
				? (float) $data['user_crml_cash'] : intval($data['user_crml_cash']);

			$template_data = array_merge($template_data, array(
				'CRML_CASH'        => $user_cash_value,
				
				'S_DONATIONS_ENABLED' => $this->config['crml_donations_enabled'],
				'S_ALLOW_DONATE'   => $this->auth->acl_get('u_crml_donate') ? TRUE : FALSE,
				'S_ALLOW_MANAGE'   => $this->auth->acl_get('m_crml_manage_user_cash') ? TRUE : FALSE,

				'U_DONATE'         => $this->helper->route('fp_forumpromotion_donate_controller', array('uid' => $data['user_id'])),
				'U_MANAGE'         => append_sid("{$this->root_path}mcp.$this->php_ext", 'i=\lawlypops\forumpromotion\mcp\manage_cash_module&amp;mode=manage_cash&amp;user_id=' . $data['user_id'])
			));

			$event['template_data'] = $template_data;
		}
	}
}
