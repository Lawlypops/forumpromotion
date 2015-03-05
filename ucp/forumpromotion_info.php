<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\ucp;

class forumpromotion_info
{
	function module()
	{
		return array(
			'filename'   => '\lawlypops\forumpromotion\ucp\carmel_module',
			'title'      => 'UCP_FORUMPROMOTION',
			'modes'     => array(
				'cash_logs' => array(
					'title' => 'UCP_CRML_CASH_LOGS',
					'auth'  => 'ext_lawlypops/forumpromotion',
					'cat'   => array('UCP_FORUMPROMOTION')
				),
			) 
		);
	}
}