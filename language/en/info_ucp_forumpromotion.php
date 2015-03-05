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
	'UCP_FORUMPROMOTION'          => 'Forum Promotion',
	'UCP_CRML_CASH_LOGS'         => 'Transaction Logs',
	'UCP_FROM'                   => 'From',
	'UCP_TO'                     => 'To',
	'UCP_AMOUNT'                 => 'Amount',

	'UCP_TOTAL_LOGS'             => '%d Logs',
));