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
	'CRML_DONATIONS_DISABLED'   => 'Donations are disabled',
	'CRML_DONATIONS_NO_PERM'    => 'You do not have permissions to make donations.',
	'CRML_USER_NOT_EXIST'       => 'The user you are trying to donate to does not exist, or this user cannot accept donations.',
	'CRML_CANNOT_DONATE_SELF'   => 'You cannot donate to yourself.',
	'CRML_AMOUNT_MUST_NUMERIC'  => 'Amount must be numeric.',
	'CRML_AMOUNT_MUST_POSITIVE' => 'Amount must be a positive number that is less than the amount of cash you have.',

	'CRML_DONATION_AMOUNT'      => 'Amount',
	'CRML_DONATION_MESSAGE'     => 'Message',

	'CRML_DONATION_EXPLAIN'     => 'You currently are currently donating to %s.',

	'CRML_SUCCESSFUL_DONATION'  => 'Donation was succesful.',

	'CRML_LOG_MESSAGE_DONATE'   => 'A donation of %d cash was made.',
));
