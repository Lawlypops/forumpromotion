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
	// User Permissions 	
	'ACL_U_CRML_EARN'		=>	'Can earn virtual cash',
	'ACL_U_CRML_BONUS'		=>	'Can earn bonuses',
	'ACL_U_CRML_DONATE'		=>	'Can donate virtual cash to other users',
	'ACL_U_CRML_TIP'		=>	'Can tip other users',
	'ACL_U_CRML_LOGIN_INCREMENT'	=>	'Can receive points daily for logging in',

	// Moderator Permissions
	'ACL_M_CRML_MANAGE_USER_CASH'   =>	'Can manage users\' virtual cash',
	'ACL_M_CRML_VIEW_LOGS'		    =>	'Can view virtual cash transactions',

	// Administration Permissions
	'ACL_A_CRML_MANAGE_CONFIG' 	=> 	'Can manage Forum Promotion configuration'
));