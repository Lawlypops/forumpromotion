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
	'ACP_FORUMPROMOTION'                => 'Forum Promotion',
	'ACP_FORUMPROMOTION_GENERAL'        => 'General',

	'CRML_ENABLE'    => 'Enable Forum Promotion cash system:',

	'CRML_UNIT'                    => 'Unit:',
	'CRML_UNIT_EXPLAIN'            => 'The name of the cash unit that will be used across the board.',
	'CRML_ENABLE_DECIMALS'         => 'Enable Decimals:',
	'CRML_ENABLE_DECIMALS_EXPLAIN' => 'Allows non-integer cash values to be used.',

	'CRML_INVALID_INPUT'  => 'Invalid input on %s field.',

	'ACP_CASH_DEFAULTS'                => 'Default cash increments:', // Section Title
	'CRML_REPLY_DEFAULT'               => 'Default reply increment:',
	'CRML_REPLY_DEFAULT_EXPLAIN'       => 'The default amount of cash a user receives when they post a reply to a topic. Can be negative.',
	'CRML_TOPIC_DEFAULT'               => 'Default topic increment:', 
	'CRML_TOPIC_DEFAULT_EXPLAIN'       => 'The default amount of cash a user receives when they create a new topic. Can be negative.',
	'CRML_LOGIN_INCREMENT'             => 'Login increment:',
	'CRML_LOGIN_INCREMENT_EXPLAIN'     => 'The amount of cash a user receives for logging in today. Can be negative.',

	'ACP_DONATION_FEATURES'            => 'Exchanging cash',
	'CRML_DONATIONS_ENABLED'           => 'Donations enabled:',
	'CRML_DONATIONS_ENABLED_EXPLAIN' => 'Allows users with permission to donate their cash to other users.',
	'CRML_TIPS_ENABLED'                => 'Tips enabled:',
	'CRML_TIPS_ENABLED_EXPLAIN'      => 'Allows users to give tips to each other for a post. Independent from donations being enabled.',
	'CRML_TIP_DEFAULT'                 => 'Tip default:',
	'CRML_TIP_DEFAULT_EXPLAIN'         => 'The default value for tips to be given to other users.',

	'CRML_ENABLE_FORUM'                      => 'Enable Forum Promotion for forum:',
	'CRML_ENABLE_FORUM_EXPLAIN'              => 'Allows users to earn virtual cash in this forum.',
	'CRML_FORUM_TOPIC_INCREMENT'             => 'Use global default new topic cash increment:',
	'CRML_FORUM_TOPIC_INCREMENT_EXPLAIN'     => 'Override the global default for cash earned on new topic.',    
	'CRML_FORUM_REPLY_INCREMENT'             => 'Use global default new reply cash increment:',
	'CRML_FORUM_REPLY_INCREMENT_EXPLAIN'     => 'Override the global default for cash earned on new reply.',
	'CRML_FORUM_PER_WORD_INCREMENT'          => 'Use global default per word increment:',
	'CRML_FORUM_PER_WORD_INCREMENT_EXPLAIN'  => 'Override the default value for cash earned for each word of a post. This is added on top of the values above.'
));