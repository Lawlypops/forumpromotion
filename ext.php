<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion;

class ext extends \phpbb\extension\base
{
	protected $crml_notification_types = array(
		'lawlypops.forumpromotion.notification.type.donate',
	);

	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				// Enable reputation notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->crml_notification_types as $notification_type)
				{
					$phpbb_notifications->enable_notifications($notification_type);
				}
				return 'notifications';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

	function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				// Disable reputation notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->crml_notification_types as $notification_type)
				{
					$phpbb_notifications->disable_notifications($notification_type);
				}
				return 'notifications';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

	function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->crml_notification_types as $notification_type)
				{
					$phpbb_notifications->purge_notifications($notification_type);
				}
				return 'notifications';
			break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
			break;
		}
	}
}
