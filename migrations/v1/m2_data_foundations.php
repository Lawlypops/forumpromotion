<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\migrations\v1;

class m2_data_foundations extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\lawlypops\forumpromotion\migrations\v1\m1_schema_foundations'
		);
	}

	public function update_data()
	{
		return array(
			// TODO: Disable enabling by default for production release.
			// Feature settings
			array('config.add', array('crml_enabled', '1')),
			array('config.add', array('crml_unit_name', 'Cash')),
			array('config.add', array('crml_enable_decimals', '1')),
			array('config.add', array('crml_donations_enabled', '1')),
			array('config.add', array('crml_tips_enabled', '1')),

			// Cash defaults
			array('config.add', array('crml_topic_default', '10')),
			array('config.add', array('crml_reply_default', '5')),
			array('config.add', array('crml_edit_default', '0')), 
			array('config.add', array('crml_per_word_default', '0')),
			array('config.add', array('crml_login_increment', '5')),
			array('config.add', array('crml_tip_default', '3')),

			array('permission.add', array('a_crml_manage_config')),

			array('permission.add', array('m_crml_view_logs')),
			array('permission.add', array('m_crml_manage_user_cash')),

			// Permission creation
			array('permission.add', array('u_crml_earn')),
			array('permission.add', array('u_crml_donate')),
			array('permission.add', array('u_crml_tip')),
			array('permission.add', array('u_crml_bonus')),
			array('permission.add', array('u_crml_login_increment')),

			// Permission assignments
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_crml_manage_config')),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_crml_manage_config')),
			array('permission.permission_set', array('ROLE_MOD_FULL', array('m_crml_view_logs', 'm_crml_manage_user_cash'))),
			array('permission.permission_set', array('ROLE_USER_STANDARD', array('u_crml_earn', 'u_crml_donate', 'u_crml_tip', 'u_crml_bonus', 'u_crml_login_increment'))),
			array('permission.permission_set', array('ROLE_USER_FULL', array('u_crml_earn', 'u_crml_donate', 'u_crml_tip', 'u_crml_bonus', 'u_crml_login_increment'))),

			// ACP Module additions
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_FORUMPROMOTION')),
			array('module.add', array(
				'acp', 'ACP_FORUMPROMOTION', array(
					'module_basename' => '\lawlypops\forumpromotion\acp\forumpromotion_module',
					'modes' => array('general')
				)
			)),

			// MCP Module Additions
			array('module.add', array('mcp', '', 'MCP_FORUMPROMOTION')),
			array('module.add', array(
				'mcp', 
				'MCP_FORUMPROMOTION', 
				array(
					'module_basename'    => '\lawlypops\forumpromotion\mcp\manage_cash_module',
					'modes' => array('manage_cash_search', 'manage_cash')
				)
			)),

			array('module.add', array('ucp', '', 'UCP_FORUMPROMOTION')),
			array('module.add', array(
				'ucp',
				'UCP_FORUMPROMOTION',
				array(
					'module_basename' => '\lawlypops\forumpromotion\ucp\forumpromotion_module',
					'modes' => array('cash_logs'),
				)
			)),

			// Data into custom tables
			array('custom', array(array($this, 'add_log_type_data')))
		);
	}

	public function revert_data()
	{
		$this->remove_log_type_data();
		return array();
	}

	public function add_log_type_data()
	{
		$sql_array = array(
			array(
				'log_type_name' => 'donate',
				'log_type_ucp_visible' => 1
			),
		);

		$this->db->sql_multi_insert($this->table_prefix . 'forumpro_log_types', $sql_array);
	}

	public function remove_log_type_data()
	{
		$sql = 'DELETE * FROM ' . $this->table_prefix . 'forumpro_log_types';
		$this->sql_query($sql);
	}
}
