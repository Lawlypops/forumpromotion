<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'CRML_FORUMPROMOTION'   => 'Forum Promotion',
	'CRML_MANAGE'    => 'Manage',
	'CRML_DONATE'    => 'Donate',

	'NOTIFICATION_DONATE'   => 'You have received a donation of %d %s from %s!',

	'LOG_CHANGE_CASH' => 'Changed cash value for “%1$s” to %2$d'
));
