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

class m1_schema_foundations extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				// Cash Log -- Keeps UCP and MCP-accessible log of transactions.
				$this->table_prefix . 'forumpro_log' => array(
					'COLUMNS'      => array(
						'log_id'          => array('UINT', NULL, 'auto_increment'),
						'user_id_from'    => array('UINT', 0),
						'user_id_to'      => array('UINT', 0),
						'log_type_id'     => array('UINT', 0),
						'log_message'     => array('MTEXT_UNI', ''),
						'log_time'        => array('TIMESTAMP', 0),
						'log_points'      => array('UINT', 0)
					),
					'PRIMARY_KEY'  => 'log_id',
					'KEYS'         => array(
						'usr_from'    => array('INDEX', 'user_id_from'),
						'usr_to'      => array('INDEX', 'user_id_to'),
						'lg_type_id'  => array('INDEX', 'log_type_id')
					)
				),
				$this->table_prefix . 'forumpro_log_types' => array(
					'COLUMNS'      => array(
						'log_type_id'           => array('UINT', NULL, 'auto_increment'),
						'log_type_name'         => array('VCHAR_UNI:255', ''),
						'log_type_ucp_visible'  => array('BOOL', 0)
					),
					'PRIMARY_KEY' => 'log_type_id',
					'KEYS' => array(
						'ucp_vs' => array('INDEX', 'log_type_ucp_visible')
					)
				),
				$this->table_prefix . 'forumpro_tips' => array(
					'COLUMNS'     => array(
						'tip_id'             => array('UINT', NULL, 'auto_increment'),
						'tip_post_id'        => array('UINT', 0),
						'user_id_from'       => array('UINT', 0)
					),
					'PRIMARY_KEY' => 'tip_id',
					'KEYS'        => array(
						'pst_id'   => array('INDEX', 'tip_post_id'),
						'usr_fm'   => array('INDEX', 'user_id_from')
					)
				)
			),
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'user_crml_cash'            => array('DECIMAL:10', 0),
					'user_crml_last_login_incr' => array('TIMESTAMP', 0),
				),
				$this->table_prefix . 'forums' => array(
					'forum_crml_enabled'          => array('BOOL', 1),
					'forum_crml_topic_default'    => array('BOOL', 1),
					'forum_crml_topic_incr'       => array('DECIMAL:10', 0),
					'forum_crml_reply_default'    => array('BOOL', 1),
					'forum_crml_reply_incr'       => array('DECIMAL:10', 0),
					'forum_crml_edit_default'     => array('BOOL', 1),
					'forum_crml_edit_incr'        => array('DECIMAL:10', 0),
					'forum_crml_per_word_default' => array('BOOL', 1),
					'forum_crml_per_word_incr'    => array('DECIMAL:10', 0),
				),
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'forumpro_log',
				$this->table_prefix . 'forumpro_log_types',
				$this->table_prefix . 'forumpro_tips',
			),
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'user_crml_cash',
					'user_crml_last_login_incr'
				),
				$this->table_prefix . 'forums' => array(
					'forum_crml_enabled',
					'forum_crml_topic_default',
					'forum_crml_topic_incr',  
					'forum_crml_reply_default',
					'forum_crml_reply_incr',  
					'forum_crml_edit_default',
					'forum_crml_edit_incr',   
					'forum_crml_per_word_default',
					'forum_crml_per_word_incr',
				)
			)
		);
	}
}
