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
	'MCP_FORUMPROMOTION'                => 'Forum Promotion',
	'MCP_MANAGE_CASH'            => 'Manage Cash',
	'MCP_MANAGE_CASH_SEARCH'     => 'Search Users to Manage',

	'MCP_CRML_USER_NOT_FOUND'    => 'The user you were trying to manage was not found.',
	'MCP_CRML_INVALID_TYPE'      => 'The submitted type was invalid.',

	'MCP_CRML_MANAGE_CASH_FOR'   => 'Manage Cash for %s',
	'MCP_CRML_USER_CURRENT'      => 'This user currently has %g %s.',
	'MCP_CRML_SET_CASH'          => 'Set Cash Value',
	'MCP_CRML_ADD_CASH'          => 'Add Value to Current Cash',
	'MCP_CRML_SUB_CASH'          => 'Subtract Value from Current Cash',

	'MCP_CRML_CASH_LOGS'         => 'Cash Transactions Log',
	'MCP_CRML_FROM'              => 'From',
	'MCP_CRML_TO'                => 'To',

	'MCP_SUCCESSFUL_SUBMIT'      => 'Cash value changed successfully.'
));