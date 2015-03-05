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

/**
* Forum Promotion ACP Module Logic
*/
class forumpromotion_module
{
	public $u_action;

	/** @var \phpbb\config\config **/
	protected $config;
	/** @var \phpbb\request\request **/
	protected $request;
	/** @var \phpbb\template\template **/
	protected $template;
	/** @var \phpbb\user **/
	protected $user;

	/**
	* Entry point for ACP module.
	*
	* @param int $id   The id of the 
	*/
	function main($id, $module)
	{
		// Entrance point for module to call acp module.

		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		switch($module)
		{
			case 'general':
				$this->tpl_name = 'forumpromotion_general';
				$this->page_title = $user->lang('ACP_FORUMPROMOTION_GENERAL');
				$this->display_general();
				break;
		}
	}

	/**
	* Displays the General section of the Forum Promotion ACP module.
	*/
	public function display_general()
	{
		$errors = array();
		$config_vals = array();

		$action = $this->request->variable('action', '');

		add_form_key('acp_forumpromotion');

		// TODO: Implement min / max.
		// Config settings to configure.
		$configs_to_use = array(
			array('crml_enabled', 'bool'),
			array('crml_unit_name', 'string'),
			array('crml_enable_decimals', 'bool'),
			array('crml_topic_default', 'float'),
			array('crml_reply_default', 'float'),
			array('crml_donations_enabled', 'float'),
			array('crml_login_increment', 'float'),
			array('crml_tips_enabled', 'bool'),
			array('crml_tip_default', 'float'),
		);

		$config_vals = array(); 

		// TODO: Functionalize this whole chunk for general use.
		// Pull in submitted value, if submitted, or default value if not.
		foreach($configs_to_use as $val)
		{
			$temp_name = $val[0];

			$default_val = ($val[1] == 'float') ? 0.00 : '';

			$config_vals[$temp_name] = ($this->request->is_set_post($temp_name) && $this->request->variable($temp_name, '') !== '') 
				? $this->request->variable($temp_name, $default_val) : $this->config[$temp_name];
		}

		// Data was submitted with no errors.
		if($this->request->is_set_post('submit') && (!sizeof($errors)))
		{
			$changes = array();
			$temp_name = $temp_type = '';

			if(check_form_key('acp_forumpromotion'))
			{
				foreach($configs_to_use as $to_use)
				{
					$temp_name = $to_use[0];
					$temp_type = $to_use[1];
					$temp_val  = $config_vals[$temp_name];

					switch($temp_type)
					{
						case 'bool':
							if($temp_val != 1 && $temp_val != 0)
							{
								$lang_field = $this->user->lang(strtoupper($temp_name));
								$errors[] = $this->user->lang('CRML_INVALID_INPUT', $lang_field);
							}
							else 
							{
								$changes[$temp_name] = ($temp_val == 1) ? 1 : 0;
							}
							break;
						case 'float':
						case 'int':
							if(!is_numeric($temp_val))
							{
								$lang_field = $this->user->lang(strtoupper($temp_name));
								$errors[] = $this->user->lang('CRML_INVALID_INPUT', $lang_field);
							}
							else 
							{
								$changes[$temp_name] = ($temp_type == 'float') 
									? (float) $temp_val : (int) $temp_val;
							}
							break;
						case 'string':
							$changes[$temp_name] = $temp_val;
							break;
					}
				}

				foreach($changes as $key => $value)
				{
					$this->config->set($key, $value); // Actually set values.
				}
			}
			else
			{
				$errors[] = $this->user->lang['FORM_INVALID'];
			}
		}

		// Go through and clean to real values.

		// Generates setting vars of the form S_ACP_CRML_CONFIG_NAME corresponding to 
		// the config names from $configs_to_use above.
		foreach($config_vals as $key => $value)
		{
			$this->template->assign_vars(array(
				'S_ACP_' . strtoupper($key)        => $value
			));
		}

		$this->template->assign_vars(array(
			'S_ERROR'       => (sizeof($errors)) ? TRUE : FALSE,
			'ERROR_MSG' => implode('<br />', $errors)
		));
	}
}