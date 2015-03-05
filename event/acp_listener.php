<?php
/**
*
* Forum Promotion
*
* @copyright (c) 2015 Forum Promotion
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lawlypops\forumpromotion\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\template\template */
	protected $template;

	/**
	* Provides core events subscribed to in phpBB.
	*
	* @return array   Contains core events with callback functions.
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_manage_forums_request_data'		=> 'forum_request',
			'core.acp_manage_forums_initialise_data'	=> 'forum_initialise',
			'core.acp_manage_forums_display_form'		=> 'forum_display',
		);
	}

	/**
	* Constructor
	*
	* @param \phpbb\request\request   $request    Contains request tools.
	* @param \phpbb\template\template $template   The template object.
	*/
	public function __construct(\phpbb\request\request $request, \phpbb\template\template $template)
	{
		$this->request = $request;
		$this->template = $template;
	}

	/**
	* Add necessary settings to request info.
	*
	* @param object $event The event object.
	*/
	public function forum_request($event)
	{
		$forum_data = $event['forum_data'];

		$forum_data['forum_crml_enabled']           
			= $this->request->variable('reputation_enabled', 1);

		$forum_data['forum_crml_topic_default']
			= $this->request->variable('crml_topic_default', 1);
		$forum_data['forum_crml_topic_incr']
			= (float) $this->request->variable('crml_topic_incr', 0.00);

		$forum_data['forum_crml_reply_default']
			= $this->request->variable('crml_reply_default', 1);
		$forum_data['forum_crml_reply_incr']
			= (float) $this->request->variable('crml_reply_incr', 0.00);

		$forum_data['forum_crml_per_word_default']
			= $this->request->variable('crml_per_word_default', 1);
		$forum_data['forum_crml_per_word_incr']
			= (float) $this->request->variable('crml_per_word_incr', 0.00);

		$event['forum_data'] = $forum_data;
	}

	/**
	* Give default data from fields.
	*
	* @param object $event The event object.
	*/
	public function forum_initialise($event)
	{
		if($event['action'] == 'add')
		{
			$forum_data = $event['forum_data'];
			$forum_data = array_merge($forum_data, array(
				'forum_crml_enabled'	          => TRUE,
				'forum_crml_topic_default'        => TRUE,
				'forum_crml_topic_incr'           => 0,
				'forum_crml_reply_default'        => TRUE,
				'forum_crml_reply_incr'           => 0,
				'forum_crml_per_word_default'     => TRUE,
				'forum_crml_per_word_incr'        => 0,
			));
			$event['forum_data'] = $forum_data;
		}
	}

	/**
	* Give necessary data to the template.
	*
	* @param object $event The event object.
	*/
	public function forum_display($event)
	{
		$tpl_data = $event['template_data'];
		$tpl_data['S_FORUMPROMOTION_ENABLED_FORUM'] = $event['forum_data']['forum_crml_enabled'];
		$tpl_data['S_CRML_TOPIC_DEFAULT'] = $event['forum_data']['forum_crml_topic_default'];
		$tpl_data['S_CRML_TOPIC_INCR'] = $event['forum_data']['forum_crml_topic_incr'];
		$tpl_data['S_CRML_REPLY_DEFAULT'] = $event['forum_data']['forum_crml_reply_default'];
		$tpl_data['S_CRML_REPLY_INCR'] = $event['forum_data']['forum_crml_reply_incr'];
		$tpl_data['S_CRML_PER_WORD_DEFAULT'] = $event['forum_data']['forum_crml_per_word_default'];
		$tpl_data['S_CRML_PER_WORD_INCR'] = $event['forum_data']['forum_crml_per_word_incr'];
		$event['template_data'] = $tpl_data;
	}
}
