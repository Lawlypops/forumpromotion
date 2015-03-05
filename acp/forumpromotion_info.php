<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\acp;

class forumpromotion_info
{
	function module()
	{
		return array(
			'filename'   => '\lawlypops\forumpromotion\acp\forumpromotion_module',
			'title'      => 'ACP_FORUMPROMOTION',
			'modes'      => array(
				'general'     => array(
					'title' => 'ACP_FORUMPROMOTION_GENERAL',
					'auth'  => 'ext_lawlypops/forumpromotion && acl_a_crml_manage_config',
					'cat'   => array('ACP_FORUMPROMOTION')
				)
			)
		);
	}
}