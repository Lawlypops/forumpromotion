<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\notification\type;

/**
* Notification handler for Forum Promotion donations.
*/
class donate extends \phpbb\notification\type\base
{
	protected $language_key = 'NOTIFICATION_DONATE';

	public static $notification_option = array(
		'lang'   => 'NOTIFICATION_DONATE_OPTION',
		'group'  => 'NOTIFICATION_GROUP_MISCELLANEOUS'
	);

	public function get_type()
	{
		return 'lawlypops.forumpromotion.notification.type.donate';
	}

	public function is_available()
	{
		return true;
	}

	public static function get_item_id($data)
	{
		return (int) $data['user_id_from'];
	}

	public static function get_item_parent_id($data)
	{
		return 0;
	}

	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users' => array(),
		), $options); // I don't think this is necessary?? Try removing...

		$users = array((int) $data['user_id_to']);

		return $this->check_user_notification_options($users, $options);
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id_from'));
	}

	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('user_id_from'), 'no_profile');

		return $this->user->lang($this->language_key, $this->get_data('cash_amount'), $this->config['crml_unit_name'], $username);
	}

	public function users_to_query()
	{
		$users = array(
			$this->get_data('user_id_from')
		);

		return $users;
	}

	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.php?mode=viewprofile&u=' . $this->get_data('user_id_from'));
	}

	public function get_redirect_url()
	{
		return $this->get_url();
	}

	public function get_email_template()
	{
		return false;
	}

	public function get_email_template_variables()
	{
		return array();
	}
	
	public function get_reference()
	{
		return censor_text($this->get_data('message'));
	}

	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_id_to', $data['user_id_to']);
		$this->set_data('user_id_from', $data['user_id_from']);
		$this->set_data('cash_amount', $data['cash_amount']);
		$this->set_data('message', $data['message']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}