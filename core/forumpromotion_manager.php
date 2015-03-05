<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\core;

/**
* For keeping code DRY. :)
*/
class forumpromotion_manager
{
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\log\log */
	protected $log;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $crml_log_table;
	/** @var string */
	protected $crml_log_types_table;

	/** 
	* Constructor 
	* 
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\log\log $log
	* @param \phpbb\pagination $pagination
	* @param \phpbb\request\request $request 
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param string $crml_log_table
	* @param string $crml_log_types_table
	*/
	public function __construct(
		\phpbb\auth\auth $auth, 
		\phpbb\config\config $config, 
		\phpbb\db\driver\driver_interface $db, 
		\phpbb\log\log $log, 
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template, 
		\phpbb\user $user, 
		$crml_log_table, 
		$crml_log_types_table
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		$this->crml_log_table = $crml_log_table;
		$this->crml_log_types_table = $crml_log_types_table;
	}

	/**
	* Logs a cash action.
	*
	* @param int       $type_name           Name of type from forumpromotion_types_table.
	* @param string    $log_message         The message to include in the log.
	* @param float     $log_points          The points involved in the transaction.
	* @param int       $user_id_from        The user id of the transaction sender.
	* @param int       $user_id_to          The user id of receiving user
	* @return bool                          Success status.
	*/
	public function log_cash_action($type_name, $log_message, $log_points, $user_id_from, $user_id_to)
	{
		$log_type_id = $this->get_log_type((string) $type_name);

		if($log_type_id == 0) 
		{
			return FALSE;
		}

		$data = array(
			'user_id_from' => (int) $user_id_from,
			'user_id_to'   => (int) $user_id_to,
			'log_type_id'  => (int) $log_type_id,
			'log_message'  => (string) $log_message,
			'log_time'     => time(),
			'log_points'   => (int) $log_points
		);

		$sql = 'INSERT INTO ' . $this->crml_log_table . ' ' . 
			$this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		return TRUE;
	}

	/**
	* Gets array of transaction logs.
	*
	* @param  int  $start      (optional) The item number to start at.
	* @param  int  $per_page   (optional) The number of items to grab.
	* @param  int  $user_id    (optional) Show only logs involving this user.
	* @return array            Array containing log rows.
	*/
	public function get_logs($start = 0, $per_page = 10, $user_id = NULL)
	{
		$ofTheJedi = array();

		$start = (int) $start;
		$per_page = (int) $per_page;

		// TODO: Make this more conforming to phpBB DB functions. Might be problems with ORDER BY when used on boards that aren't using MySQL. Not sure, though. Can't hurt to make it prettier though.
		$sql = 'SELECT * 
			FROM ' . $this->crml_log_table . ' ';
		if (is_int($user_id))
		{
			$sql .= 'WHERE user_id_from = ' . (int) $user_id . ' 
				OR user_id_to = ' . (int) $user_id . ' ';
		}
		$sql .= 'ORDER BY log_time DESC';
		$result = $this->db->sql_query_limit($sql, $per_page, $start);

		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	* Gets a log type id by the name of the type.
	*
	* @param  string   $name     Name of the type to get id for.
	* @return int                The integer id of the type.
	*/
	public function get_log_type($name)
	{
		$data = array(
			'log_type_name' => $name
		);

		$sql = 'SELECT log_type_id
			FROM ' . $this->crml_log_types_table . '
			WHERE ' . $this->db->sql_build_array('SELECT', $data);
		$result = $this->db->sql_query($sql);
		$ofTheJedi = $this->db->sql_fetchrow();
		$this->db->sql_freeresult($result);

		if($ofTheJedi)
		{
			return $ofTheJedi['log_type_id'];
		}
		else
		{
			return 0;
		}
	}

	/**
	* Gets array of log types with their field names from the database
	*
	* @return array Array of field names as keys and values as keys.
	*/ 
	public function get_log_types()
	{
		$sql = 'SELECT * 
			FROM ' . $this->crml_log_types_table;
		$result = $this->db->sql_query($sql);
		$ofTheJedi = $this->db->sql_fetchrowset();
		$this->db->sql_freeresult($result);

		return $ofTheJedi; // Lol.
	}

	/**
	* Gets username of specific user ID.
	*
	* @param int $user_id   The user ID of the user.
	* @return string        The colored, HTMLified username string.
	*/
	public function get_username($user_id)
	{
		$sql = 'SELECT username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);

		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return get_username_string('full', $user_id, $row['username'], $row['user_colour']);
	}

	/**
	* Adjusts user's cash.
	* 
	* @param int $user_id     The id of the user to change.
	* @param 
	*/
	public function adjust_user_cash($user_id, $adjustment_type, $amount = NULL, $type_data = NULL)
	{
		if(!is_numeric($user_id) || !$this->user->data['is_registered'])
		{
			return FALSE;
		}

		switch ($adjustment_type)
		{
			case 'change':
				$new_value = is_numeric($amount) ? (float) $amount : 0;
				break;
			case 'donation':
				if($this->config['crml_donations_enabled'])
				{
					$amount  = is_numeric($amount) ? (float) $amount : 0;
					$sending_uid = is_numeric($type_data) ? $type_data : $this->user->data['user_id'];

					if($amount <= 0)
					{
						return FALSE;
					}

					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_crml_cash = user_crml_cash + ' . $amount . '
						WHERE user_id = ' . $user_id;
					$this->db->sql_query($sql);

					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_crml_cash = user_crml_cash - ' . $amount . '
						WHERE user_id = ' . $sending_uid;
					$this->db->sql_query($sql);
				}

				break;
			case 'login':
				if($this->config['crml_login_increment'] != 0)
				{
					$login_increment = $this->config['crml_login_increment'];

					if(($this->request->variable($this->config['cookie_name'] . '_crml_login_flag', FALSE, TRUE, \phpbb\request\request_interface::COOKIE) == FALSE))
					{
						$sql = 'SELECT user_crml_last_login_incr
							FROM ' . USERS_TABLE . '
							WHERE user_id = ' . $this->user->data['user_id'];
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);
						$last_login_incr = $row['user_crml_last_login_incr'];

						// Sets cookie expiring in twenty minutes.
						$this->user->set_cookie('crml_login_flag', 'test', time() + 1200, false);

						$dayAgo = time() - (24 * 60 * 60);
						$timeIsSet = isset($last_login_incr);

						if($timeIsSet && ($last_login_incr <= $dayAgo))
						{
							$sql = 'UPDATE ' . USERS_TABLE . '
								SET user_crml_cash = user_crml_cash + ' . $login_increment . ', user_crml_last_login_incr = ' . time() . '
								WHERE user_id = ' . $this->user->data['user_id'];
							$this->db->sql_query($sql);

							// TODO: Notify user that they received an increment for visiting.
						}
					}
				}
				
				break;
		}
	}
}