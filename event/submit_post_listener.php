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
* Listeners for post submission
*/
class submit_post_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver */
	protected $db;
	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\user
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
	}

	/**
	* Gets core events subscribed to.
	*
	* @return array   Subscribed events.
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.submit_post_end' => 'add_cash'
		);
	}

	/**
	* Adds cash to posts.
	*/
	function add_cash($event) 
	{
		if($this->config['crml_enabled'] && $this->user->data['is_registered'])
		{
			$data = $event['data'];
			$mode = $event['mode'];

			$forum_id = $data['forum_id'];

			// Gets information about forum's cash stuff.
			$sql = 'SELECT forum_crml_enabled, forum_crml_topic_default, forum_crml_topic_incr, forum_crml_reply_default, forum_crml_reply_incr, forum_crml_edit_default, forum_crml_edit_incr, forum_crml_per_word_default, forum_crml_per_word_incr
				FROM ' . FORUMS_TABLE . "
				WHERE forum_id = $forum_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if((int) $row['forum_crml_enabled'] === 1 && $this->auth->acl_get('u_crml_earn'))
			{
				// Figure out the increment based on the forum's settings.
				$increment = 0;
				switch($mode)
				{
					case 'post':
						$increment = ($row['forum_crml_topic_default']) 
							? $this->config['crml_topic_default'] : $row['forum_crml_topic_incr'];
						break;
					case 'reply':
					case 'quote':
						$increment = ($row['forum_crml_reply_default']) 
							? $this->config['crml_reply_default'] : $row['forum_crml_reply_incr'];
						break;
				}

				if((float) $row['forum_crml_per_word'] != 0)
				{
					// TODO: Word increments.
				}

				// Drop decimals if decimals are disabled.
				if($this->config['crml_enable_decimals'] == 0)
				{
					$increment = intval($increment);
				} 

				// Prevents SQL injection if table value gets fucked up.
				$increment = (float) $increment;

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_crml_cash = user_crml_cash + ' . $increment . '
					WHERE user_id = ' . $this->user->data['user_id'];
				$this->db->sql_query($sql);
			}
		}
	}
}