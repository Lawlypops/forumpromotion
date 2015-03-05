<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\controller;

/**
* Logic for donations
*/
class donate_controller
{
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var \phpbb\notification\manager */
	protected $notification_manager;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\symfony_request */
	protected $symfony_request;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user\user */
	protected $user;

	/** @var \lawlypops\forumpromotion\core\forumpromotion_manager */
	protected $forumpromotion_manager;

	/** @var string */
	protected $log_table;
	/** @var string */
	protected $log_types_table;

	/** 
	* Constructor 
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\notification\manager $notification_manager
	* @param \phpbb\request\request $request
	* @param \phpbb\symfony_request $symfony_request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \lawlypops\forumpromotion\core\forumpromotion_manager $forumpromotion_manager
	* @param string $log_table
	* @param string $log_types_table
	*/
	public function __construct(
		\phpbb\auth\auth $auth, 
		\phpbb\config\config $config, 
		\phpbb\db\driver\driver_interface $db, 
		\phpbb\controller\helper $helper, 
		\phpbb\notification\manager $notification_manager, 
		\phpbb\request\request $request, 
		\phpbb\symfony_request $symfony_request, 
		\phpbb\template\template $template, 
		\phpbb\user $user, 
		\lawlypops\forumpromotion\core\forumpromotion_manager $forumpromotion_manager, 
		$log_table, 
		$log_types_table
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->notification_manager = $notification_manager;
		$this->request = $request;
		$this->symfony_request = $symfony_request;
		$this->template = $template;
		$this->user = $user;

		$this->forumpromotion_manager = $forumpromotion_manager;

		$this->log_table = $log_table;
		$this->log_types_table = $log_types_table;
	}

	/**
	* Handles donation page and submission.
	*
	* @param int $uid   The receiving user's ID.
	*/
	public function donate($uid)
	{
		$this->user->add_lang_ext('lawlypops/forumpromotion', 'forumpromotion_donate');
		add_form_key('donate_form');

		$error = '';

		if(!$this->config['crml_enabled'] || !$this->config['crml_donations_enabled'])
		{
			trigger_error($this->user->lang['CRML_DONATIONS_DISABLED']);
		}

		if(!$this->auth->acl_get('u_crml_donate'))
		{
			trigger_error($this->user->lang['CRML_DONATIONS_NO_PERM']);
		}

		$uid = intval($uid); // Just in case.

		$sql = 'SELECT user_id, user_type, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . $uid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if(!$row || $row['user_type'] == USER_IGNORE)
		{
			trigger_error($this->user->lang('CRML_USER_NOT_EXIST'));
		}

		if($row['user_id'] == $this->user->data['user_id'])
		{
			trigger_error($this->user->lang('CRML_CANNOT_DONATE_SELF'));
		}

		$user_donate_to_username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

		// User submitted case.
		if($this->request->is_set_post('submit'))
		{
			$amount_s = $this->request->variable('amount', 0);

			if(!is_numeric($amount_s))
			{
				$error = $this->user->lang('CRML_AMOUNT_MUST_NUMERIC');
			} 
			else 
			{
				$amount = ($this->config['crml_enable_decimals']) ? 
					floatval($amount_s) : floor(floatval($amount_s));

				if($amount <= 0 || $amount > $this->user->data['user_crml_cash'])
				{
					$error = $this->user->lang('CRML_AMOUNT_MUST_POSITIVE');
				}
				else if(!check_form_key('donate_form'))
				{
					// Form key was invalid.
					$error = $this->user->lang('FORM_INVALID');
				}
				else 
				{
					// Updates cash value.
					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_crml_cash = user_crml_cash + ' . $amount . '
						WHERE user_id = ' . $uid;
					$res = $this->db->sql_query($sql);

					$sql = 'UPDATE ' . USERS_TABLE .'
						SET user_crml_cash = user_crml_cash - ' . $amount . '
						WHERE user_id = ' . $this->user->data['user_id'];
					$res = $this->db->sql_query($sql);

					// Gives receiving user a notification.
					$this->notification_manager->add_notifications(
						array('lawlypops.forumpromotion.notification.type.donate'), 
						array(
							'user_id_from' => $this->user->data['user_id'],
							'user_id_to'   => $uid,
							'cash_amount'  => $amount,
							'message'      => $this->request->variable('message', '')
						)
					);

					// Logs transaction.
					$log_message = $this->user->lang('CRML_LOG_MESSAGE_DONATE',  $amount);
					$this->forumpromotion_manager->log_cash_action('donate', $log_message, $amount, $this->user->data['user_id'], $uid);

					trigger_error($this->user->lang('CRML_SUCCESSFUL_DONATION'));
				}
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'       => (!empty($error)) ? TRUE : FALSE,
			'ERROR_MSG'     => $error,

			'DONATION_EXPLAINED' => $this->user->lang('CRML_DONATION_EXPLAIN', $user_donate_to_username),
			'DONATION_MESSAGE_IN' => $this->request->variable('message', '')
		));

		return $this->helper->render('donate.html', $this->user->lang('CRML_DONATE'));
	}
}