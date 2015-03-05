<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\mcp;

class manage_cash_info
{
	function module()
	{
		return array(
			'filename'   => '\lawlypops\forumpromotion\mcp\manage_cash_module',
			'title'      => 'MCP_FORUMPROMOTION',
			'modes'     => array(
				'manage_cash_search' => array(
					'title' => 'MCP_MANAGE_CASH_SEARCH',
					'auth'  => 'ext_lawlypops/forumpromotion && acl_m_crml_manage_user_cash',
					'cat'   => array('MCP_FORUMPROMOTION')
				),
				'manage_cash' => array(
					'title'   => 'MCP_MANAGE_CASH',
					'auth'    => 'ext_lawlypops/forumpromotion && acl_m_crml_manage_user_cash',
					'cat'     => array('MCP_FORUMPROMOTION'),
					'display' => false
				)
			) 
		);
	}
}